<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;

use App\Mail\ReportMail;
use App\Exports\CustomerExport;

use Auth;
use PDF;
use Mail;
use Excel;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        config(['site.page' => 'customer']);
        $mod = new Customer();
        $company = $name = $phone_number = '';
        if ($request->get('company') != ""){
            $company = $request->get('company');
            $mod = $mod->where('company', 'LIKE', "%$company%");
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
        return view('admin.customers', compact('data', 'company', 'name', 'phone_number'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $item = Customer::find($request->get("id"));
        $item->name = $request->get("name");
        $item->company = $request->get("company");
        $item->email = $request->get("email");
        $item->phone_number = $request->get("phone_number");
        $item->address = $request->get("address");
        $item->city = $request->get("city");
        $item->save();
        return response()->json('success');
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        Customer::create([
            'name' => $request->get('name'),
            'company' => $request->get('company'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
        ]);
        return response()->json('success');
    }

    public function delete($id){
        $item = Customer::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
        
    public function report($id){
        $customer = Customer::find($id);
        $pdf = PDF::loadView('reports.customers_report.pdf', compact('customer'));        
        return $pdf->download('customer_report_'.$customer->company.'.pdf');        
        // return view('reports.customers_report.pdf', compact('customer'));
    }
    
    public function email($id){
        $customer = Customer::find($id);
        $pdf = PDF::loadView('reports.customers_report.pdf', compact('customer'));   
        if(filter_var($customer->email, FILTER_VALIDATE_EMAIL)){
            $to_email = $customer->email;
            Mail::to($to_email)->send(new ReportMail($pdf, 'Customer Report'));
            return back()->with('success', __('page.email_is_sent_successfully'));
        }else{
            return back()->withErrors('email', __('page.invalid_email_address'));
        }
    }

    public function export($id) {        
        $customer = Customer::find($id);
        $sales = $customer->sales;
        $sale_array = $customer->sales->pluck('id');
        $payments = Payment::whereIn('paymentable_id', $sale_array)->where('paymentable_type', 'App\Models\Sale')->get();
        return Excel::download(new CustomerExport($sales, $payments), 'customer_report.xlsx');
    }
}
