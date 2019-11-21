<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Order;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Image;
use App\Models\Currency;

use App;

class VueController extends Controller
{
    
    public function get_products() {
        $products = Product::all();

        return response()->json($products);
    }

    public function get_product(Request $request) {
        $id = $request->get('id');

        $product = Product::find($id)->load('tax');

        return response()->json($product);
    }

    public function get_orders(Request $request) {
        $id = $request->get('id');
        $type = $request->get('type');
        // dd($request->all());
        if($type == 'purchase'){
            $item = Purchase::find($id);
        }elseif($type == 'sale'){
            $item = Sale::find($id);
        }        
        $orders = $item->orders;
        return response()->json($orders);
    }

    public function get_data(Request $request){
        $id = $request->get('id');
        $type = $request->get('type');
        // dd($request->all());
        if($type == 'purchase'){
            $item = Purchase::find($id);
        }elseif($type == 'sale'){
            $item = Sale::find($id);
        }
        return response()->json($item);
    }

    public function get_first_product(Request $request){
        $item = Product::with('tax')->first();
        return response()->json($item);
    }

    public function get_autocomplete_products(Request $request){
        $keyword = $request->get('keyword');
        $data = Product::with('tax')->where('name', 'LIKE', "%$keyword%")->orWhere('code', 'LIKE', "%$keyword%")->get();
        return response()->json($data);
    }

    public function get_pre_order(Request $request){
        $id = $request->get('id');
        $item = PreOrder::find($id)->load('items');
        return response()->json($item);
    }

    public function get_received_quantity(Request $request){
        $id = $request->get('id');
        $item = PreOrderItem::find($id);
        $received_quantity = $item->purchased_items->sum('quantity');
        return response()->json($received_quantity);
    }

    public function image_migrate(){
        $data = Purchase::all();
        foreach ($data as $item) {
            if($item->attachment){                
                Image::create([
                    'imageable_id' => $item->id,
                    'imageable_type' => 'App\Models\Purchase',
                    'path' => $item->attachment,
                ]);
            }
        }
    }

    public function get_rate() {
        return response()->json(Currency::all());
    }
    
}
