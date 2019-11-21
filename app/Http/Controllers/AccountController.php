<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\TransactionHistory;

use Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        config(['site.page' => 'account']);
        $data = Account::all();
        return view('admin.settings.account', compact('data'));
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
            'currency' => 'required',
        ]);
        
        Account::create([
            'name' => $request->get('name'),
            'currency_id' => $request->get('currency'),
            'balance' => $request->get('balance') ?? 0,
        ]);
        return back()->with('success', __('page.created_successfully'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
            'currency'=>'required',
        ]);
        $item = Account::find($request->get("id"));
        $item->name = $request->get("name");
        $item->currency_id = $request->get("currency");
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }

    public function set_balance(Request $request) {
        $request->validate([
            'amount'=>'required',
        ]);
        $user = Auth::user();
        $item = Account::find($request->get("id"));
        $amount = $request->amount;
        $item->increment('balance', $amount);
        TransactionHistory::create([
            'account_id' => $item->id,
            'amount' => $amount,
            'description' => $user->name . " set $amount to " . $item->name,
        ]);
        return back()->with("success", __('page.successfully_set'));
    }

    public function delete($id){
        Account::destroy($id);
        return back()->with("success", __('page.deleted_successfully'));
    }
}
