<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Preturn;
use App\Models\Purchase;
use App\Models\Company;

use Auth;
use PDF;

class PreturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $id)
    {
        config(['site.page' => 'purchase']);
        $purchase = Purchase::find($id);
        $data = $purchase->preturns;
        return view('preturn.index', compact('data', 'id'));
    }

    public function report($id){
        $purchase = Purchase::find($id);
        $pdf = PDF::loadView('preturn.report', compact('purchase'));        
        return $pdf->download('return_report_'.$purchase->reference_no.'.pdf');
        // return view('preturn.report', compact('purchase'));
    }

    public function create(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_no'=>'required|string',
            'purchase_id'=>'required',
        ]);
        
        $user = Auth::user();
        
        $item = new Preturn();
        $item->timestamp = $request->get('date').":00";
        $item->reference_no = $request->get('reference_no');
        $item->amount = $request->get('amount');
        $item->purchase_id = $request->get('purchase_id');
        $item->note = $request->get('note');

        if($user->hasRole('secretary')){
            $item->status = 0;
        }else{
            $item->status = 1;
        }

        if($request->has("attachment")){
            $picture = request()->file('attachment');
            
            $purchase = Purchase::find($request->get('purchase_id'));
            $supplier_company = $purchase->supplier->company;
            $company_name = $purchase->company->name;
            $date_time = date('Y-m-d-H-i-s');
            $reference_no = $purchase->reference_no;
            $attach_name = $company_name . "_" . $request->get('reference_no'). "_" . $reference_no . "_" . $supplier_company . "_" . $date_time;

            $imageName = $attach_name . '.' . $picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/return_images/'), $imageName);
            $item->attachment = 'images/uploaded/return_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.added_successfully'));
    }

    public function edit(Request $request){
        $request->validate([
            'date'=>'required',
        ]);
        $data = $request->all();
        $item = Preturn::find($request->get("id"));
        $item->timestamp = $request->get("date");
        $item->reference_no = $request->get("reference_no");
        $item->amount = $request->get("amount");
        $item->note = $request->get("note");
        if($request->has("attachment")){
                          
            $purchase = $item->purchase;
            $supplier_company = $purchase->supplier->company;
            $company_name = $purchase->company->name;
            $date_time = date('Y-m-d-H-i-s');
            $reference_no = $purchase->reference_no;
            $attach_name = $company_name . "_" . $request->get('reference_no'). "_" . $reference_no . "_" . $supplier_company . "_" . $date_time;
                
            $picture = request()->file('attachment');
            $imageName = $attach_name . '.' . $picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/return_images/'), $imageName);
            $item->attachment = 'images/uploaded/return_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }


    public function delete($id){
        $item = Preturn::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    public function approve($id){
        $item = Preturn::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->update(['status' => 1]);
        return back()->with("success", __('page.approved_successfully'));
    }
}
