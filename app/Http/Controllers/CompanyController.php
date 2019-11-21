<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        config(['site.page' => 'company']);
        $data = Company::paginate(15);
        return view('admin.settings.company', compact('data'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $item = Company::find($request->get("id"));
        $item->name = $request->get("name");
        $item->save();
        return back()->with('success', 'Updated Successfully');
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        Company::create([
            'name' => $request->get('name'),
        ]);
        return back()->with('success', 'Created Successfully');
    }

    public function delete($id){
        $item = Company::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
