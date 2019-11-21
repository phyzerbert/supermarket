<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;

class TaxRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        config(['site.page' => 'tax_rate']);
        $data = Tax::all();
        return view('admin.settings.tax_rate', compact('data'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
            'code'=>'required',
            'rate'=>'required|numeric',
            'type'=>'required',
        ]);
        $data = $request->all();
        $item = Tax::find($request->get("id"));
        $item->update($data);
        return back()->with('success', __('page.updated_successfully'));
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required',
            'code'=>'required',
            'rate'=>'required|numeric',
            'type'=>'required',
        ]);
        $data = $request->all();
        Tax::create($data);
        return back()->with('success', __('page.created_successfully'));
    }

    public function delete($id){
        $item = Tax::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
