<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\BarcodeSymbology;
use App\Models\Tax;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        config(['site.page' => 'product']);
        $categories = Category::all();
        $mod = new Product();
        $code = $name = $category_id = '';
        if ($request->get('code') != ""){
            $code = $request->get('code');
            $mod = $mod->where('code', 'LIKE', "%$code%");
        }
        if ($request->get('name') != ""){
            $name = $request->get('name');
            $mod = $mod->where('name', 'LIKE', "%$name%");
        }
        if ($request->get('category_id') != ""){
            $category_id = $request->get('category_id');
            $mod = $mod->where('category_id', $category_id);
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('product.index', compact('data', 'categories', 'code', 'name', 'category_id'));
    }

    public function create(Request $request){
        config(['site.page' => 'product']);
        $categories = Category::all();
        $taxes = Tax::all();
        $barcode_symbologies = BarcodeSymbology::all();
        $suppliers = Supplier::all();
        return view('product.create', compact('categories', 'taxes', 'barcode_symbologies', 'suppliers'));
    }

    public function save(Request $request){
        $request->validate([
            'name'=>'required|string',
            'code'=>'required|string',
            'barcode_symbology_id'=>'required',
            'category'=>'required',
            'currency'=>'required',
            'unit'=>'required|string',
            'cost'=>'required|numeric',
            'price'=>'required|numeric',
        ]);
        $data = $request->all();
        $item = new Product();
        $item->name = $data['name'];
        $item->code = $data['code'];
        $item->barcode_symbology_id = $data['barcode_symbology_id'];
        $item->category_id = $data['category'];
        $item->currency_id = $data['currency'];
        $item->unit = $data['unit'];
        $item->cost = $data['cost'];
        $item->price = $data['price'];
        $item->tax_id = $data['tax_id'];
        $item->tax_method = $data['tax_method'];
        $item->alert_quantity = $data['alert_quantity'];
        $item->supplier_id = $data['supplier_id'];
        $item->detail = $data['detail'];

        if($request->has("image")){
            $picture = request()->file('image');
            $imageName = "product_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/product_images/'), $imageName);
            $item->image = 'images/uploaded/product_images/'.$imageName;
        }
        $item->save();

        return back()->with('success', __('page.created_successfully'));
    }

    public function edit(Request $request, $id){
        config(['site.page' => 'product']);
        $product = Product::find($id);
        $categories = Category::all();
        $taxes = Tax::all();
        $barcode_symbologies = BarcodeSymbology::all();
        $suppliers = Supplier::all();

        return view('product.edit', compact('product', 'categories', 'taxes', 'barcode_symbologies', 'suppliers'));
    }

    public function detail(Request $request, $id){
        config(['site.page' => 'product']);
        $product = Product::find($id);

        return view('product.detail', compact('product'));
    }

    public function update(Request $request){
        $request->validate([
            'name'=>'required|string',
            'code'=>'required|string',
            'barcode_symbology_id'=>'required',
            'category_id'=>'required',
            'currency_id'=>'required',
            'unit'=>'required|string',
            'cost'=>'required',
            'price'=>'required',
        ]);
        $data = $request->all();
        $item = Product::find($request->get("id"));
        $data['image'] = $item->image;

        if($request->has("image")){
            $picture = request()->file('image');
            $imageName = "product_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/product_images/'), $imageName);
            $data['image'] = 'images/uploaded/product_images/'.$imageName;
        }
        $item->update($data);
        return back()->with('success', __('page.updated_successfully'));
    }

    public function delete($id){
        $item = Product::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    public function ajax_create(Request $request){
        $request->validate([
            'name'=>'required|string',
            'code'=>'required|string',
            'unit'=>'required|string',
        ]);
        $data = $request->all();
        // dd($data);
        $item = new Product();
        $item->name = $data['name'];
        $item->code = $data['code'];
        // $item->barcode_symbology_id = $data['barcode_symbology_id'];
        // $item->category_id = $data['category_id'];
        $item->unit = $data['unit'];
        $item->cost = $data['cost'];
        // $item->price = $data['price'];
        // $item->tax_id = $data['tax_id'];
        // $item->tax_method = $data['tax_method'];
        // $item->alert_quantity = $data['alert_quantity'];
        // $item->supplier_id = $data['supplier_id'];
        // $item->detail = $data['detail'];

        if($request->has("image")){
            $picture = request()->file('image');
            $imageName = "product_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/product_images/'), $imageName);
            $item->image = 'images/uploaded/product_images/'.$imageName;
        }
        $item->save();

        return response()->json($item);
    }
}
