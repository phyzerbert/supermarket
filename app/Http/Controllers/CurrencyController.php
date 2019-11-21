<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        config(['site.page' => 'currency']);
        $data = Currency::all();
        return view('admin.settings.currency', compact('data'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
            'rate'=>'required',
        ]);
        $item = Currency::find($request->get("id"));
        $item->name = $request->get("name");
        $item->rate = $request->get("rate");
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
            'rate'=>'required|string',
        ]);
        
        Currency::create([
            'name' => $request->get('name'),
            'rate' => $request->get('rate'),
        ]);
        return back()->with('success', __('page.created_successfully'));
    }

    public function save(Request $request) {
        $bolivar = Currency::find(1);
        $dollar = Currency::find(2);
        $euro = Currency::find(3);

        $bolivar->update(['rate' => $request->get('rate_bolivar')]);
        $dollar->update(['rate' => $request->get('rate_dollar')]);
        $euro->update(['rate' => $request->get('rate_euro')]);
        return back()->with('success', __('page.successfully_set'));
    }

    public function delete($id){
        Currency::destroy($id);
        return back()->with("success", __('page.deleted_successfully'));
    }
}
