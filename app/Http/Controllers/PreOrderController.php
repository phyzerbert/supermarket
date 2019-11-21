<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Company;
use App\Models\Store;
use App\Models\StoreProduct;
use App\User;

use Auth;

class PreOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        config(['site.page' => 'purchase_order']);
        $user = Auth::user();
        $suppliers = Supplier::all();
        $companies = Company::all();

        $mod = new PreOrder();
        if($user->company){
            $mod = $user->company->pre_orders();
        }
        $company_id = $reference_no = $supplier_id = $period = $expiry_period = $keyword = '';
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
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        if ($request->get('expiry_period') != ""){   
            $expiry_period = $request->get('expiry_period');
            $from = substr($expiry_period, 0, 10);
            $to = substr($expiry_period, 14, 10);
            $mod = $mod->whereBetween('expiry_date', [$from, $to]);
        }
        if ($request->get('keyword') != ""){
            $keyword = $request->keyword;
            $company_array = Company::where('name', 'LIKE', "%$keyword%")->pluck('id');
            $supplier_array = Supplier::where('company', 'LIKE', "%$keyword%")->pluck('id');

            $mod = $mod->where(function($query) use($keyword, $company_array, $supplier_array){
                return $query->where('reference_no', 'LIKE', "%$keyword%")
                        ->orWhereIn('company_id', $company_array)
                        ->orWhereIn('supplier_id', $supplier_array)
                        ->orWhere('timestamp', 'LIKE', "%$keyword%")
                        ->orWhere('grand_total', 'LIKE', "%$keyword%");
            });
        }
        if($request->sort_by_date != ''){
            $sort_by_date = $request->sort_by_date;
        }
        // dump($sort_by_date);
        $pagesize = session('pagesize');
        $data = $mod->orderBy('timestamp', $sort_by_date)->paginate($pagesize);
        return view('pre_order.index', compact('data', 'companies', 'suppliers', 'company_id', 'supplier_id', 'reference_no', 'period', 'expiry_period', 'keyword', 'sort_by_date'));
    }

    public function create(Request $request){
        config(['site.page' => 'purchase_order_create']);
        $user = Auth::user();  
        $suppliers = Supplier::all();
        $products = Product::all();
        $companies = Company::all();
        return view('pre_order.create', compact('suppliers', 'products', 'companies'));
    }

    public function save(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_number'=>'required|string',
            'supplier'=>'required',
        ]);

        $data = $request->all();
        if(!isset($data['product_id']) ||  count($data['product_id']) == 0 || in_array(null, $data['product_id'])){
            return back()->withErrors(['product' => __('page.select_product')]);
        }

        $item = new PreOrder();
        $item->user_id = Auth::user()->id;  
        $item->timestamp = $data['date'].":00";
        $item->reference_no = $data['reference_number'];
        $item->company_id = isset($data['company_id']) ? $data['company_id'] : Auth::user()->company_id;
        $item->supplier_id = $data['supplier'];
        $item->note = $data['note'];

        $company_name = Company::find($item->company_id)->name;
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $date_time = date('Y-m-d-H-i-s');            
            $supplier_company = Supplier::find($data['supplier'])->company;
            $imageName = $company_name . "_" . $data['reference_number'] . "_" . $supplier_company . "_" . $date_time . '.' . $picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/purchase_order_images/'), $imageName);
            $item->attachment = 'images/uploaded/purchase_order_images/'.$imageName;
        }        
        
        $item->grand_total = $data['grand_total'];
        
        $item->save();

        if(isset($data['product_id']) && count($data['product_id']) > 0){

            for ($i=0; $i < count($data['product_id']); $i++) { 
                PreOrderItem::create([
                    'product_id' => $data['product_id'][$i],
                    'cost' => $data['cost'][$i],
                    'quantity' => $data['quantity'][$i],
                    // 'expiry_date' => $data['expiry_date'][$i],
                    'discount' => $data['discount'][$i],
                    'discount_string' => $data['discount_string'][$i],
                    'subtotal' => $data['subtotal'][$i],
                    'pre_order_id' => $item->id,
                ]);                
            }
        }
        return redirect(route('pre_order.index'))->with('success', __('page.created_successfully'));
    }

    public function edit(Request $request, $id){    
        config(['site.page' => 'purchase_order']); 
        $user = Auth::user();   
        $order = PreOrder::find($id);        
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('pre_order.edit', compact('order', 'suppliers', 'products'));
    }

    public function detail(Request $request, $id){    
        config(['site.page' => 'purchase_order']);    
        $order = PreOrder::find($id);

        return view('pre_order.detail', compact('order'));
    }

    public function update(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_number'=>'required|string',
            'supplier'=>'required',
        ]);
        $data = $request->all();

        if(!isset($data['product_id']) ||  count($data['product_id']) == 0 || in_array(null, $data['product_id'])){
            return back()->withErrors(['product' => __('page.select_product')]);
        }
        // dd($data);
        $item = PreOrder::find($request->get("id"));
 
        $item->timestamp = $data['date'].":00";
        $item->reference_no = $data['reference_number'];
        $item->supplier_id = $data['supplier'];
        // if($data['credit_days'] != ''){
        //     $item->credit_days = $data['credit_days'];
        //     $item->expiry_date = date('Y-m-d', strtotime("+".$data['credit_days']."days", strtotime($item->timestamp)));
        // }
        // $item->status = $data['status'];
        $item->note = $data['note'];

        $company_name = Company::find($item->company_id)->name;
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $date_time = date('Y-m-d-H-i-s');            
            $supplier_company = Supplier::find($data['supplier'])->company;
            $imageName = $company_name . "_" . $data['reference_number'] . "_" . $supplier_company . "_" . $date_time . '.' . $picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/purchase_order_images/'), $imageName);
            $item->attachment = 'images/uploaded/purchase_order_images/'.$imageName;
        }

        $item->grand_total = $data['grand_total'];

        $item->save();

        $order_items = $item->items->pluck('id')->toArray();
        $diff_items = array_diff($order_items, $data['item_id']);
        foreach ($diff_items as $key => $value) {
            PreOrderItem::find($value)->delete();
        }

        if(isset($data['item_id']) && count($data['item_id']) > 0){
            for ($i=0; $i < count($data['item_id']); $i++) { 
                if($data['item_id'][$i] == ''){
                    PreOrderItem::create([
                        'product_id' => $data['product_id'][$i],
                        'cost' => $data['cost'][$i],
                        'quantity' => $data['quantity'][$i],
                        'discount' => $data['discount'][$i],
                        'discount_string' => $data['discount_string'][$i],
                        'subtotal' => $data['subtotal'][$i],
                        'pre_order_id' => $item->id,
                    ]);
                }else{
                    $order = PreOrderItem::find($data['item_id'][$i]);
                    $order->update([
                        'product_id' => $data['product_id'][$i],
                        'cost' => $data['cost'][$i],
                        'quantity' => $data['quantity'][$i],
                        // 'expiry_date' => $data['expiry_date'][$i],
                        'discount' => $data['discount'][$i],
                        'discount_string' => $data['discount_string'][$i],
                        'subtotal' => $data['subtotal'][$i],
                    ]);
                }
            }
        }
        
        return back()->with('success', __('page.updated_successfully'));
    }

    public function delete($id){
        $item = PreOrder::find($id);
        $purchases = $item->purchases;
        foreach ($purchases as $purchase) {
            $purchase->items()->delete();
            $purchase->delete();
        }
        $item->items()->delete();        
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    public function receive($id){
        config(['site.page' => 'purchase_order']);    
        $order = PreOrder::find($id);
        $stores = Store::all();
        if($order->company){
            $stores = $order->company->stores;
        }

        return view('pre_order.receive', compact('order', 'stores'));
    }

    public function save_receive(Request $request){
        $request->validate([
            'store' => 'required',
            'reference_number' => 'required',
        ]);
        $data = $request->all();
        if(!isset($data['item']) ||  count($data['item']) == 0 || in_array(null, $data['item'])){
            return back()->withErrors(['product' => __('page.select_product')]);
        }
        $user = Auth::user();
        $order = PreOrder::find($data['id']);
        $purchase = new Purchase();
        $purchase->order_id = $order->id;
        $purchase->user_id = $order->user_id;
        $purchase->store_id = $data['store'];
        $store = Store::find($data['store']);
        $purchase->company_id = $order->company ? $order->company_id : $store->company_id;
        $purchase->supplier_id = $order->supplier_id;
        $purchase->reference_no = $data['reference_number'];
        $purchase->note = $data['note'];
        $purchase->timestamp = $order->timestamp;

        $purchase->discount_string = $data['discount_string'];
        $purchase->discount = $data['discount'];

        $purchase->shipping_string = $data['shipping_string'];
        $purchase->shipping = -1 * $data['shipping'];
        $purchase->returns = $data['returns'];

        $purchase->grand_total = $data['grand_total'];
        $purchase->note = $order->note;

        if($user->hasRole('secretary')){
            $purchase->status = 0;
        }else{
            $purchase->status = 1;
        }
        $company_name = Company::find($purchase->company_id)->name;
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $date_time = date('Y-m-d-H-i-s');
            $supplier_company = Supplier::find($order->supplier_id)->company;
            $imageName = $company_name . "_" . $data['reference_number'] . "_" . $supplier_company . "_" . $date_time . '.' . $picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/purchase_images/'), $imageName);
            $purchase->attachment = 'images/uploaded/purchase_images/'.$imageName;
        }
        $purchase->save();

        foreach($data["item"] as $id => $value){
            $order_item = PreOrderItem::find($value);
            Order::create([
                'product_id' => $order_item->product_id,
                'cost' => $order_item->cost - $order_item->discount,
                'quantity' => $data['receive_quantity'][$value],
                'subtotal' => $data['subtotal'][$value],
                'pre_order_item_id' => $value,
                'orderable_id' => $purchase->id,
                'orderable_type' => Purchase::class,
            ]);
            
            $store_product = StoreProduct::where('store_id', $data['store'])->where('product_id', $order_item->product_id)->first();
            if(isset($store_product)){
                $store_product->increment('quantity', $data['receive_quantity'][$value]);
            }else{
                $store_product = StoreProduct::create([
                    'store_id' => $data['store'],
                    'product_id' => $order_item->product_id,
                    'quantity' => $data['receive_quantity'][$value],
                ]);
            }
        }

        return redirect(route('pre_order.index'))->with('success', __('page.received_successfully'));
    }

    public function received_orders(Request $request){
        config(['site.page' => 'received_order']);
        $user = Auth::user();
        $stores = Store::all();
        $suppliers = Supplier::all();
        $companies = Company::all();

        $mod = new Purchase();
        // if($user->role->slug == 'user'){
        //     $mod = $user->company->purchases();
        //     $stores = $user->company->stores;
        // }
        if($user->company){
            $mod = $user->company->purchases();
        }
        $mod = $mod->whereNotNull('order_id');
        $order_id = $company_id = $reference_no = $supplier_id = $store_id = $period = $keyword = '';
        $sort_by_date = 'desc';
        if ($request->get('order_id') != ""){
            $order_id = $request->get('order_id');
            $mod = $mod->where('order_id', $order_id);
        }        
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
        if ($request->get('keyword') != ""){
            $keyword = $request->keyword;
            $company_array = Company::where('name', 'LIKE', "%$keyword%")->pluck('id');
            $supplier_array = Supplier::where('company', 'LIKE', "%$keyword%")->pluck('id');
            $store_array = Store::where('name', 'LIKE', "%$keyword%")->pluck('id');

            $mod = $mod->where(function($query) use($keyword, $company_array, $store_array, $supplier_array){
                return $query->where('reference_no', 'LIKE', "%$keyword%")
                        ->orWhereIn('company_id', $company_array)
                        ->orWhereIn('store_id', $store_array)
                        ->orWhereIn('supplier_id', $supplier_array)
                        ->orWhere('timestamp', 'LIKE', "%$keyword%")
                        ->orWhere('grand_total', 'LIKE', "%$keyword%");
            });
        }
        if($request->sort_by_date != ''){
            $sort_by_date = $request->sort_by_date;
        }
        // dump($sort_by_date);
        $pagesize = session('pagesize');
        $data = $mod->orderBy('timestamp', $sort_by_date)->paginate($pagesize);
        return view('received_order.index', compact('data', 'companies', 'stores', 'suppliers', 'order_id', 'company_id', 'store_id', 'supplier_id', 'reference_no', 'period', 'keyword', 'sort_by_date')); 
    }

    public function edit_received_order($id){
        
    }
    public function update_received_order($id){
        
    }
    public function delete_received_order($id){
        
    }
}
