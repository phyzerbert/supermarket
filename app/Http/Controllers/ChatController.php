<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\User;
use App\Events\MessageSent;

use Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        config(['site.page' => 'chat']);
        return view('chat.index');
    }

    public function fetchMessages(User $user)
    {
        $privateCommunication= Message::with('user')
            ->where(['user_id'=> auth()->id(), 'receiver_id'=> $user->id])
            ->orWhere(function($query) use($user){
                $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id()]);
            })->get();

        return $privateCommunication;
    }

    public function sendMessage(Request $request,User $user)
    {
        if(request()->has('file')){

            // $filename = request('file')->store('chat');
            $imageables = ['jpg', 'JPG', 'jpeg', 'png', 'gif'];
            $attachment = request()->file('file');
            if(in_array($attachment->getClientOriginalExtension(), $imageables)){
                $is_image = 1;
            }else{
                $is_image = 0;
            }
            $imageName = time().'.'.$attachment->getClientOriginalExtension();
            $attachment->move(public_path('images/messages'), $imageName);
            $message=Message::create([
                'user_id' => request()->user()->id,
                'attachment' => 'images/messages/'.$imageName,
                'is_image' => $is_image,
                'receiver_id' => $user->id
            ]);
        }else{
            $input=$request->all();
            $input['receiver_id']=$user->id;
            $message=auth()->user()->messages()->create($input);
        }
        $loaded_message = $message->load('user');
        broadcast(new MessageSent($loaded_message))->toOthers();
        return response(['status' => 'success','message' => $loaded_message]);
    }

    public function users()
    {
        $user = Auth::user();
        $mod = new User();
        $mod = $mod->where('role_id', '<>', 3);
        if($user->company){
            $mod = $mod->where('company_id', $user->company_id)->orWhere('role_id', 1);
        }
        $data = $mod->get();
        return $data;
    }

    public function unread_messages(){
        $users = User::all();
        $unread_messages = array();
        foreach ($users as $user) {
            $unread_messages[$user->id] = $user->messages()->where('receiver_id', Auth::user()->id)->where('is_read', 0)->count();
        }
        return $unread_messages;
    }

    public function read_messages($id){
        $sender = User::find($id);
        $messages = Auth::user()->received_messages()->where('user_id', $sender->id)->get();
        foreach ($messages as $message) {
            $message->update(['is_read' => 1]);
        }
        return 'success';
    }
}
