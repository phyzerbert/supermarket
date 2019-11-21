<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Company;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        config(['site.page' => 'store']);
        $companies = Company::all();
        $data = Store::paginate(15);
        return view('admin.settings.store', compact('data', 'companies'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required|string',
            'company'=>'required',
        ]);
        $item = Store::find($request->get("id"));
        $item->name = $request->get("name");
        $item->company_id = $request->get("company");
        $item->save();
        return back()->with('success', 'Updated Successfully');
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
            'company'=>'required',
        ]);
        
        Store::create([
            'name' => $request->get('name'),
            'company_id' => $request->get('company'),
        ]);
        return back()->with('success', 'Created Successfully');
    }

    public function delete($id){
        $item = Store::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
