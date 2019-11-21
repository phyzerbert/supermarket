<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Nexmo;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'name';
    }

    public function authenticated(Request $request, Authenticatable $user)
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if($user->hasRole('secretary') && $user->ip_address != '' && $ip_address != $user->ip_address){
            Auth::logout();
            return redirect(route('login'))->with('ip_restriction' , __('page.this_computer_is_not_allowed'));
        }else {
            $user->update(['last_ip' => $ip_address]);
        }
        
        // Auth::logout();
            
        // $request->session()->put('verify:user:id', $user->id);

        // $url = 'https://api.nexmo.com/verify/json?' . http_build_query([
        //     'api_key' => env('NEXMO_KEY'),
        //     'api_secret' => env('NEXMO_SECRET'),
        //     'number' => $user->phone_number,
        //     'brand' => config('app.name'),
        // ]);

        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        // $response = json_decode($response);
        // // dd($response);
        // if($response->status == 0){
        //     $request->session()->put('verify:request_id', $response->request_id);
        //     return redirect(route('verify'));
        // }else{
        //     return redirect(route('login'))->withErrors(['phone' => $response->status]);
    // }
    }
}
