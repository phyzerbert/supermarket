<?php

namespace App\Http\Controllers;

use App;
use Auth;
use Nexmo;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function show(Request $request) {
        return view('auth.phone_verify');
    }

    public function verify(Request $request) {
        $this->validate($request, [
            'code' => 'size:4',
        ]);
    
        try {
            Nexmo::verify()->check(
                $request->session()->get('verify:request_id'),
                $request->code
            );
            Auth::loginUsingId($request->session()->pull('verify:user:id'));
            return redirect('/home');
        } catch (Nexmo\Client\Exception\Request $e) {
            return redirect()->back()->withErrors([
                'code' => $e->getMessage()
            ]);    
        }
    }

    public function lang($locale)
    {
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
