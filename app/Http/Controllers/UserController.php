<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Company;

use Auth;
use Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        config(['site.page' => 'user']);
        $companies = Company::all();
        $mod = new User();
        $company_id = $name = $phone_number = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', "$company_id");
        }
        if ($request->get('name') != ""){
            $name = $request->get('name');
            $mod = $mod->where('name', 'LIKE', "%$name%");
        }
        if ($request->get('phone_number') != ""){
            $phone_number = $request->get('phone_number');
            $mod = $mod->where('phone_number', 'LIKE', "%$phone_number%");
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('admin.users', compact('data', 'companies', 'company_id', 'name', 'phone_number'));
    }

        
    public function profile(Request $request){
        $user = Auth::user();
        config(['site.page' => 'profile']);
        $companies = Company::all();
        return view('profile', compact('user', 'companies'));
    }

    public function updateuser(Request $request){
        $request->validate([
            'name'=>'required',
            'phone_number'=>'required',
            'password' => 'confirmed',
        ]);
        $user = Auth::user();
        $user->name = $request->get("name");
        $user->phone_number = $request->get("phone_number");
        $user->first_name = $request->get("first_name");
        $user->last_name = $request->get("last_name");

        if($request->get('password') != ''){
            $user->password = Hash::make($request->get('password'));
        }
        if($request->has("picture")){
            $picture = request()->file('picture');
            $imageName = time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/profile_pictures'), $imageName);
            $user->picture = 'images/profile_pictures/'.$imageName;
        }
        $user->update();
        return back()->with("success", __('page.updated_successfully'));
    }

    public function edituser(Request $request){
        $user = User::find($request->get("id"));
        $validate_array = array(
            'name'=>'required',
            'phone_number'=>'required',
            'password' => 'confirmed',
        );
        $request->validate($validate_array);
        
        $user->name = $request->get("name");
        $user->first_name = $request->get("first_name");
        $user->last_name = $request->get("last_name");
        $user->phone_number = $request->get("phone_number");
        $user->company_id = $request->get("company_id");
        $user->ip_address = $request->get("ip_address");

        if($request->get('password') != ''){
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        return response()->json('success');
    }

    public function create(Request $request){
        $validate_array = array(
            'name'=>'required|string|unique:users',
            'role'=>'required',
            'phone_number'=>'required',
            'password'=>'required|string|min:6|confirmed'
        );
        if($request->get('role') == '2' || $request->get('role') == '4'){
            $validate_array['company_id'] = 'required';
        }
        
        $request->validate($validate_array);
        
        User::create([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'company_id' => $request->get('company_id'),
            'role_id' => $request->get('role'),
            'ip_address' => $request->get('ip_address'),
            'password' => Hash::make($request->get('password'))
        ]);
        return response()->json('success');
    }

    public function delete($id){
        $user = User::find($id);
        if(!$user){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $user->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
