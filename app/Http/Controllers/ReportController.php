<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Supplier;
use App\User;

use Auth;
use DB;

use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        config(['site.page' => 'overview_chart']);
        $user = Auth::user();
        $companies = Company::all();
        $company_names = Company::pluck('name')->toArray();
        $company_purchases_array = $company_sales_array = array();
        $period = '';
        $company_id = Company::first()->id;
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }
        $company = Company::find($company_id);

        $mod1 = $company->purchases();
        $mod2 = $company->sales();

        if($request->has('period') && $request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod1 = $mod1->whereBetween('timestamp', [$from, $to]);
            $mod2 = $mod2->whereBetween('timestamp', [$from, $to]);
        }

        $company_purchases = $mod1->pluck('id');
        $company_sales = $mod2->pluck('id');

        $data['purchase'][0] = Order::whereIn('orderable_id', $company_purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
        $data['sale'][0] = Order::whereIn('orderable_id', $company_sales)->where('orderable_type', Sale::class)->sum('subtotal');

        return view('reports.index', compact('data', 'companies', 'company_id', 'period'));

    }

    public function overview_chart(Request $request){
        config(['site.page' => 'overview_chart']);
        $user = Auth::user();
        $companies = Company::all();
        $company_id = Company::first()->id;
        if($user->role->slug == 'user'){
            $company_id = $user->company_id;
        }
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }
        $company = Company::find($company_id);

        $start_this_month = new Carbon('first day of this month');
        $end_this_month = new Carbon('last day of this month');
        
        $start_this_month = $start_this_month->format('Y-m-d');
        $end_this_month = $end_this_month->format('Y-m-d');
        
        $start_last_month = new Carbon('first day of last month');
        $end_last_month = new Carbon('last day of last month');
        
        $start_last_month = $start_last_month->format('Y-m-d');
        $end_last_month = $end_last_month->format('Y-m-d');
        
        $currentMonth = date('F');
        $return['this_month']['month_name'] = $currentMonth." ".date('Y');
        $return['last_month']['month_name'] = Date('F', strtotime($currentMonth . " last month"))." ".date('Y');

        $this_month_purchases = Purchase::where('company_id', $company_id)->whereBetween('timestamp', [$start_this_month, $end_this_month])->pluck('id')->toArray();
        $return['this_month']['purchase'] = Order::whereIn('orderable_id', $this_month_purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
        $this_month_sales = Sale::where('company_id', $company_id)->whereBetween('timestamp', [$start_this_month, $end_this_month])->pluck('id')->toArray();
        $return['this_month']['sale'] = Order::whereIn('orderable_id', $this_month_sales)->where('orderable_type', Sale::class)->sum('subtotal');

        $last_month_purchases = Purchase::where('company_id', $company_id)->whereBetween('timestamp', [$start_last_month, $end_last_month])->pluck('id')->toArray();
        $return['last_month']['purchase'] = Order::whereIn('orderable_id', $last_month_purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
        $last_month_sales = Sale::where('company_id', $company_id)->whereBetween('timestamp', [$start_last_month, $end_last_month])->pluck('id')->toArray();
        $return['last_month']['sale'] = Order::whereIn('orderable_id', $last_month_sales)->where('orderable_type', Sale::class)->sum('subtotal');
        
        return view('reports.overview_chart', compact('return', 'companies', 'company_id'));
    }

    public function company_chart(Request $request){
        config(['site.page' => 'company_chart']);

        $companies = Company::all();
        $company_names = Company::pluck('name')->toArray();
        $company_purchases_array = $company_sales_array = array();
        $period = '';

        foreach ($companies as $company) {
            $mod1 = $company->purchases();
            $mod2 = $company->sales();            

            if($request->has('period') && $request->get('period') != ""){   
                $period = $request->get('period');
                $from = substr($period, 0, 10);
                $to = substr($period, 14, 10);
                $mod1 = $mod1->whereBetween('timestamp', [$from, $to]);
                $mod2 = $mod2->whereBetween('timestamp', [$from, $to]);
            }

            $company_purchases = $mod1->pluck('id');
            $company_sales = $mod2->pluck('id');

            $company_purchases_total = Order::whereIn('orderable_id', $company_purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
            $company_sales_total = Order::whereIn('orderable_id', $company_sales)->where('orderable_type', Sale::class)->sum('subtotal');
            array_push($company_purchases_array, $company_purchases_total);
            array_push($company_sales_array, $company_sales_total);
        }

        return view('reports.company_chart', compact('company_names', 'company_purchases_array', 'company_sales_array', 'period'));
    }

    public function store_chart(Request $request){
        config(['site.page' => 'store_chart']);

        $stores = Store::all();
        $store_names = Store::pluck('name')->toArray();
        $store_purchases_array = $store_sales_array = array();
        $period = '';

        foreach ($stores as $store) {
            $mod1 = $store->purchases();
            $mod2 = $store->sales();            

            if($request->has('period') && $request->get('period') != ""){   
                $period = $request->get('period');
                $from = substr($period, 0, 10);
                $to = substr($period, 14, 10);
                $mod1 = $mod1->whereBetween('timestamp', [$from, $to]);
                $mod2 = $mod2->whereBetween('timestamp', [$from, $to]);
            }

            $store_purchases = $mod1->pluck('id');
            $store_sales = $mod2->pluck('id');
            
            $store_purchases_total = Order::whereIn('orderable_id', $store_purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
            $store_sales_total = Order::whereIn('orderable_id', $store_sales)->where('orderable_type', Sale::class)->sum('subtotal');
            array_push($store_purchases_array, $store_purchases_total);
            array_push($store_sales_array, $store_sales_total);
        }

        return view('reports.store_chart', compact('store_names', 'store_purchases_array', 'store_sales_array', 'period'));
    }

    public function product_quantity_alert(Request $request){
        config(['site.page' => 'product_quantity_alert']);

        $data = Product::all();        

        return view('reports.product_quantity_alert', compact('data'));
    }

    public function product_expiry_alert(Request $request){
        config(['site.page' => 'product_expiry_alert']);
        $user = Auth::user();
        $companies = Company::all();
        $company_id = '';
        if($user->role->slug == 'user'){
            $company_id = $user->company_id;
        }

        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }        
        
        $products = Product::all();
        $mod = new Order();
        $mod = $mod->where('orderable_type', Purchase::class)->where('expiry_date', '!=', "")->where('expiry_date', '<=', date('Y-m-d'));

        $product_id = '';
        if($company_id != ''){
            $company = Company::find($company_id);
            $company_purchases = $company->purchases()->pluck('id');
            $mod = $mod->whereIn('orderable_id', $company_purchases)->where('orderable_type', Purchase::class);
        }
        if ($request->get('product_id') != ""){
            $product_id = $request->get('product_id');
            $mod = $mod->where('product_id', $product_id);
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);

        return view('reports.product_expiry_alert', compact('data', 'products', 'product_id', 'companies', 'company_id'));
    }

    public function products_report(Request $request){
        config(['site.page' => 'products_report']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Product();
        $product_code = $product_name = $company_id = '';
        if($user->role->slug == 'user'){
            $company_id = $user->company_id;
        }
        if($request->get('product_code') != ''){
            $product_code = $request->get('product_code');
            $mod = $mod->where('code', 'LIKE', "%$product_code%");
        }
        if($request->get('product_name') != ''){
            $product_name = $request->get('product_name');
            $mod = $mod->where('name', 'LIKE', "%$product_name%");
        }        
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }

        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);        

        return view('reports.products_report', compact('data', 'companies', 'product_name', 'product_code', 'company_id'));
    }

    public function categories_report(Request $request){
        config(['site.page' => 'categories_report']);

        $user = Auth::user();
        $companies = Company::all();
        $mod = new Category();
        $name = $company_id = '';
        if($user->role->slug == 'user'){
            $company_id = $user->company_id;
        }
        if($request->get('name') != ''){
            $name = $request->get('name');
            $mod = $mod->where('name', 'LIKE', "%$name%");
        }        
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }

        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);        

        return view('reports.categories_report', compact('data', 'companies', 'name', 'company_id'));
    }

    public function sales_report(Request $request){
        config(['site.page' => 'sales_report']);
        $user = Auth::user();
        $stores = Store::all();
        $customers = Customer::all();
        $companies = Company::all();

        $mod = new Sale();
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company = $user->company;
            $stores = $company->stores;
            $mod = $company->sales();
        }

        $company_id = $reference_no = $customer_id = $store_id = $period = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('customer_id') != ""){
            $customer_id = $request->get('customer_id');
            $mod = $mod->where('customer_id', $customer_id);
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.sales_report', compact('data', 'companies', 'stores', 'customers', 'company_id', 'store_id', 'customer_id', 'reference_no', 'period'));
    }

    public function purchases_report(Request $request){
        config(['site.page' => 'purchases_report']);
        $user = Auth::user();
        $stores = Store::all();
        $suppliers = Supplier::all();
        $companies = Company::all();

        $mod = new Purchase();
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company = $user->company;
            $stores = $company->stores;
            $mod = $company->purchases();
        }
        $mod = $mod->where('status', 1);
        $company_id = $reference_no = $supplier_id = $store_id = $period = $keyword = '';
        $sort_by_date = 'desc';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('supplier_id') != ""){
            $supplier_id = $request->get('supplier_id');
            $mod = $mod->where('supplier_id', $supplier_id);
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.purchases_report', compact('data', 'companies', 'stores', 'suppliers', 'company_id', 'store_id', 'supplier_id', 'reference_no', 'period', 'sort_by_date', 'keyword'));
    }

    public function payments_report(Request $request){
        config(['site.page' => 'payments_report']);
        $user = Auth::user();
        $companies = Company::all();
        $suppliers = Supplier::all();
        $mod = new Payment();
        $mod = $mod->where('paymentable_type', Purchase::class);
        $reference_no = $period = $company_id = $supplier_id = '';
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;            
        }
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }
        if($company_id != ''){
            $company = Company::find($company_id);
            $company_purchases = $company->purchases()->pluck('id');
            $company_sales = $company->sales()->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $company_purchases);
            // $mod = $mod->where(function($query) use($company_purchases){
            //     $query->whereIn('paymentable_id', $company_purchases)->where('paymentable_type', Purchase::class);
            // })->orWhere(function($query) use($company_sales){
            //     $query->whereIn('paymentable_id', $company_sales)->where('paymentable_type', Sale::class);
            // });
        }
        if($request->get('supplier_id') != ''){
            $supplier_id = $request->get('supplier_id');
            $supplier = Supplier::find($supplier_id);
            $supplier_purchases = $supplier->purchases()->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $supplier_purchases);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.payments_report', compact('data', 'companies', 'suppliers', 'company_id', 'supplier_id', 'reference_no', 'period'));
    }

    public function income_report(Request $request){
        config(['site.page' => 'payments_report']);
        $user = Auth::user();
        $companies = Company::all();
        $customers = Supplier::all();
        $mod = new Payment();
        $mod = $mod->where('paymentable_type', Sale::class);
        $reference_no = $period = $company_id = $customer_id = '';
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;            
        }
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }
        if($company_id != ''){
            $company = Company::find($company_id);
            $company_sales = $company->sales()->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $company_sales);
            // $mod = $mod->where(function($query) use($company_purchases){
            //     $query->whereIn('paymentable_id', $company_purchases)->where('paymentable_type', Purchase::class);
            // })->orWhere(function($query) use($company_sales){
            //     $query->whereIn('paymentable_id', $company_sales)->where('paymentable_type', Sale::class);
            // });
        }
        if($request->get('customer_id') != ''){
            $customer_id = $request->get('customer_id');
            $customer = Supplier::find($customer_id);
            $customer_sales = $customer->sales()->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $customer_sales);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.income_report', compact('data', 'companies', 'customers', 'company_id', 'customer_id', 'reference_no', 'period'));
    }

    public function customers_report(Request $request){
        config(['site.page' => 'customers_report']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Customer();
        $customer_company = $name = $phone_number = $company_id = '';
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;            
        }else{
            if ($request->get('company_id') != ""){
                $company_id = $request->get('company_id');
            }
        }        
        if ($request->get('customer_company') != ""){
            $customer_company = $request->get('customer_company');
            $mod = $mod->where('company', 'LIKE', "%$customer_company%");
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
        return view('reports.customers_report.index', compact('data', 'companies', 'company_id', 'name', 'customer_company', 'phone_number'));
    }

    public function customer_sales(Request $request, $id){
        config(['site.page' => 'customers_report']);
        $user = Auth::user();
        $customer = Customer::find($id);
        $stores = Store::all();
        $companies = Company::all();

        $mod = $customer->sales();
        $company_id = $reference_no = $store_id = $period = '';
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;
            $stores = $user->company->stores;
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');            
        }
        if ($company_id != ''){
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.customers_report.sales', compact('data', 'customer', 'companies', 'stores', 'company_id', 'store_id', 'reference_no', 'period'));
    }

    public function customer_payments(Request $request, $id){
        config(['site.page' => 'customers_report']);
        $user = Auth::user();
        $companies = Company::all();
        $customer = Customer::find($id);
        
        $mod = new Payment();
        $sales_array = $customer->sales()->pluck('id')->toArray();
        
        $reference_no = $period = $company_id = '';

        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;
            $stores = $user->company->stores;
            $sales_array = $customer->sales()->where('company_id', $company_id)->pluck('id')->toArray();
        }
        $mod = $mod->where('paymentable_type', Sale::class)->whereIn('paymentable_id', $sales_array);
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');            
        }
        if ($company_id != ''){
            $company_purchases = Sale::where('company_id', $company_id)->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $company_purchases);
        }

        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        // dd($mod->get());
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.customers_report.payments', compact('data', 'companies', 'customer', 'reference_no', 'period'));
    }

    public function suppliers_report(Request $request){
        config(['site.page' => 'suppliers_report']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Supplier();
        $company = $name = $phone_number = $company_id = '';

        if($user->company){
            $company_id = $user->company_id;            
        }else{
            if ($request->get('company_id') != ""){
                $company_id = $request->get('company_id');
            }
        } 

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
        return view('reports.suppliers_report.index', compact('data', 'companies', 'company_id', 'name', 'phone_number', 'company'));
    }

    public function suppliers_report1(Request $request){
        config(['site.page' => 'suppliers_report']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Supplier();
        $supplier_company = $name = $phone_number = $company_id = '';
        if($user->hasRole('user')){
            $company_id = $user->company_id;            
        }else{
            if ($request->get('company_id') != ""){
                $company_id = $request->get('company_id');
            }
        }  
        if ($request->get('supplier_company') != ""){
            $supplier_company = $request->get('supplier_company');
            $mod = $mod->where('company', 'LIKE', "%$supplier_company%");
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
        return view('reports.suppliers_report.index', compact('data', 'companies', 'company_id', 'name', 'supplier_company', 'phone_number', 'sort_by_total'));
    }

    public function supplier_purchases(Request $request, $id){
        config(['site.page' => 'suppliers_report']);
        $user = Auth::user();
        $supplier = Supplier::find($id);
        $stores = Store::all();
        $companies = Company::all();

        $mod = $supplier->purchases();
        $mod = $mod->where('status', 1);
        $company_id = $reference_no = $store_id = $period = '';
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company_id = $user->company_id;
            $stores = $user->company->stores;
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');            
        }
        if ($company_id != ''){
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.suppliers_report.purchases', compact('data', 'supplier', 'companies', 'stores', 'company_id', 'store_id', 'reference_no', 'period'));
    }

    public function supplier_payments(Request $request, $id){
        config(['site.page' => 'suppliers_report']);
        $supplier = Supplier::find($id);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Payment();
        $purchases_array = $supplier->purchases()->where('status', 1)->pluck('id')->toArray();
        
        $reference_no = $period = $company_id = '';

        if($user->hasRole('user')){
            $company_id = $user->company_id;
            $stores = $user->company->stores;
            $purchases_array = $supplier->purchases()->where('company_id', $company_id)->where('status', 1)->pluck('id')->toArray();
        }
        $mod = $mod->where('paymentable_type', Purchase::class)->whereIn('paymentable_id', $purchases_array);
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');            
        }
        if ($company_id != ''){
            $company_purchases = Purchase::where('company_id', $company_id)->pluck('id');
            $mod = $mod->whereIn('paymentable_id', $company_purchases);
        }

        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        // dd($mod->get());
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.suppliers_report.payments', compact('data', 'companies', 'supplier', 'company_id', 'reference_no', 'period'));
    }

    public function users_report(Request $request){
        config(['site.page' => 'users_report']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new User();        
        if($user->role->slug == 'user'){
            $mod = $user->company->users();
        }
        $company_id = $name = $phone_number = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', 'LIKE', "%$company_id%");
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
        return view('reports.users_report.index', compact('data', 'companies', 'name', 'company_id', 'phone_number'));
    }

    public function user_purchases(Request $request, $id){
        $user = User::find($id);
        config(['site.page' => 'users_report']);
        $stores = Store::all();
        $suppliers = Supplier::all();
        $companies = Company::all();

        $mod = $user->purchases();
        $mod = $mod->where('status', 1);
        $company_id = $reference_no = $supplier_id = $store_id = $period = '';
        $sort_by_date = 'desc';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('supplier_id') != ""){
            $supplier_id = $request->get('supplier_id');
            $mod = $mod->where('supplier_id', $supplier_id);
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.users_report.purchases', compact('data', 'user', 'companies', 'stores', 'suppliers', 'company_id', 'store_id', 'supplier_id', 'reference_no', 'period', 'sort_by_date'));
    }

    public function user_sales(Request $request, $id){
        $user = User::find($id);
        config(['site.page' => 'users_report']);
        $stores = Store::all();
        $customers = customer::all();
        $companies = Company::all();

        $mod = $user->sales();
        $company_id = $reference_no = $customer_id = $store_id = $period = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('customer_id') != ""){
            $customer_id = $request->get('customer_id');
            $mod = $mod->where('customer_id', $customer_id);
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.users_report.sales', compact('data', 'user', 'companies', 'stores', 'customers', 'company_id', 'store_id', 'customer_id', 'reference_no', 'period'));
    }

    public function user_payments(Request $request, $id){
        $user = User::find($id);
        config(['site.page' => 'users_report']);
        
        $mod = new Payment();
        $purchases_array = $user->purchases()->where('status', 1)->pluck('id')->toArray();
        $sales_array = $user->sales()->pluck('id')->toArray();
        $mod = $mod->where(function($query) use($purchases_array){
            $query->where('paymentable_type', Purchase::class)->whereIn('paymentable_id', $purchases_array);
        })->orWhere(function($query) use($sales_array){
            $query->where('paymentable_type', Sale::class)->whereIn('paymentable_id', $sales_array);
        });
        $reference_no = $period = '';
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        // dd($mod->get());
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('reports.users_report.payments', compact('data', 'user', 'reference_no', 'period'));
    }

    public function expired_purchases_report(Request $request){
        config(['site.page' => 'expired_purchases_report']);
        $user = Auth::user();
        $stores = Store::all();
        $suppliers = Supplier::all();
        $companies = Company::all();
        $mod = new Purchase();      
        
        if($user->hasRole('user') || $user->hasRole('secretary')){
            $company = $user->company;
            $stores = $company->stores;
            $mod = $company->purchases();
        }
        $mod = $mod->whereNotNull('credit_days');
        $company_id = $reference_no = $supplier_id = $store_id = $period = $expiry_date = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        // if ($request->get('reference_no') != ""){
        //     $reference_no = $request->get('reference_no');
        //     $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        // }
        // if ($request->get('supplier_id') != ""){
        //     $supplier_id = $request->get('supplier_id');
        //     $mod = $mod->where('supplier_id', $supplier_id);
        // }
        // if ($request->get('store_id') != ""){
        //     $store_id = $request->get('store_id');
        //     $mod = $mod->where('store_id', $store_id);
        // }
        // if ($request->get('period') != ""){   
        //     $period = $request->get('period');
        //     $from = substr($period, 0, 10);
        //     $to = substr($period, 14, 10);
        //     $mod = $mod->whereBetween('timestamp', [$from, $to]);
        // }
        if ($request->get('expiry_period') != ""){   
            $expiry_period = $request->get('expiry_period');
            $from = substr($expiry_period, 0, 10);
            $to = substr($expiry_period, 14, 10);
            $mod = $mod->whereBetween('expiry_date', [$from, $to]);
        }else{
            $from = "1970-01-01";
            $to = date('Y-m-d');
            $mod = $mod->whereBetween('expiry_date', [$from, $to]);
        }
        // $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->get();
        return view('reports.expired_purchases_report', compact('data', 'companies', 'stores', 'suppliers', 'company_id', 'store_id', 'supplier_id', 'reference_no', 'period', 'expiry_date'));
    }
    
    

    











    
    
    
    
    
    
    
    public function getTodayData($table, $where = ''){        
        $sql = "select id from ".$table." where TO_DAYS(timestamp) = TO_DAYS(now()) ".$where;
        $orderables = collect(DB::select($sql))->pluck('id')->toArray();
        $return['count'] = count($orderables);
        if($table == 'purchases'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Purchase::class)->sum('subtotal');
        }elseif($table == 'sales'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Sale::class)->sum('subtotal');
        }       
        return $return;
    }

    public function getWeekData($table, $where = ''){
        $sql = "select id from ".$table." where YEARWEEK(DATE_FORMAT(timestamp,'%Y-%m-%d')) = YEARWEEK(now()) ".$where;
        $orderables = collect(DB::select($sql))->pluck('id')->toArray();
        $return['count'] = count($orderables);
        if($table == 'purchases'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Purchase::class)->sum('subtotal');
        }elseif($table == 'sales'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Sale::class)->sum('subtotal');
        }       
        return $return;
    }

    public function getMonthData($table, $where = ''){
        $sql = "select id from ".$table." where YEARWEEK(DATE_FORMAT(timestamp,'%Y-%m-%d')) = YEARWEEK(now()) ".$where;
        $orderables = collect(DB::select($sql))->pluck('id')->toArray();
        $return['count'] = count($orderables);
        if($table == 'purchases'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Purchase::class)->sum('subtotal');
        }elseif($table == 'sales'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Sale::class)->sum('subtotal');
        }       
        return $return;
    }

    public function getOverallData($table, $where = ''){
        $sql = "select id from ". $table . $where;
        $orderables = collect(DB::select($sql))->pluck('id')->toArray();
        $return['count'] = count($orderables);
        if($table == 'purchases'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Purchase::class)->sum('subtotal');
        }elseif($table == 'sales'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Sale::class)->sum('subtotal');
        }       
        return $return;
    }
}
