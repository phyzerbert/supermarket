<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Payment;

use Carbon\Carbon;
use DB;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {        
        config(['site.page' => 'home']);
        $user = Auth::user();
        if($user->hasRole('buyer')){
            return redirect(route('pre_order.create'));
        }
        $companies = Company::all();
        if ($user->hasRole('user') || $user->hasRole('secretary')) {
            $top_company = $user->company->id;
        }else{
            $top_company = Company::first()->id;
        }
        
        $period = '';
        if($request->has('period') && $request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
        }

        if(isset($from) && isset($to)){
            $chart_start = Carbon::createFromFormat('Y-m-d', $from);
            $chart_end = Carbon::createFromFormat('Y-m-d', $to);
        }else{
            $chart_start = Carbon::now()->startOfMonth();
            $chart_end = Carbon::now()->endOfMonth();
        }

        if($request->get('top_company') != ''){
            $top_company = $request->get('top_company');
        } 
        $key_array = $purchases = $sales = $purchase_array = $sale_array = $payment_array = array();

        for ($dt=$chart_start; $dt < $chart_end; $dt->addDay()) {
            $key = $dt->format('Y-m-d');
            $key1 = $dt->format('M/d');
            array_push($key_array, $key1);
            $purchases = Purchase::where('company_id', $top_company)->whereDate('timestamp', $key)->pluck('id')->toArray();
            $sales = Sale::where('company_id', $top_company)->whereDate('timestamp', $key)->pluck('id')->toArray();
            $daily_purchase = Order::whereIn('orderable_id', $purchases)->where('orderable_type', Purchase::class)->sum('subtotal');
            $daily_sale = Order::whereIn('orderable_id', $sales)->where('orderable_type', Sale::class)->sum('subtotal');
            $daily_purchase_payment = Payment::whereIn('paymentable_id', $purchases)->where('paymentable_type', Purchase::class)->sum('amount');
            // $daily_sale_payment = Payment::whereIn('paymentable_id', $sales)->where('paymentable_type', Sale::class)->sum('amount');
            array_push($purchase_array, $daily_purchase);
            array_push($sale_array, $daily_sale);
            array_push($payment_array, $daily_purchase_payment);
        }
        
        if($request->get('top_company') != ''){
            $top_company = $request->get('top_company');
        }
        $where = "and company_id = $top_company";
        // dd($where);

        $return['today_purchases'] = $this->getTodayData('purchases', $where);
        $return['today_sales'] = $this->getTodayData('sales', $where);
        $return['week_purchases'] = $this->getWeekData('purchases', $where);
        $return['week_sales'] = $this->getWeekData('sales', $where);
        $return['month_purchases'] = $this->getMonthData('purchases', $where);
        $return['month_sales'] = $this->getMonthData('sales', $where);
        $return['overall_purchases'] = $this->getOverallData('purchases', $where);
        $return['overall_sales'] = $this->getOverallData('sales', $where);
        $return['company_grand_total'] = Purchase::where('company_id', $top_company)->sum('grand_total');

        $expired_purchases = Purchase::where('company_id', $top_company)->whereNotNull('credit_days')->where("expiry_date", "<=", date('Y-m-d'))->get();
        $expired_count = 0;
        foreach($expired_purchases as $item){
            if($item->grand_total == $item->payments()->sum('amount')) continue;
            $expired_count++;
        }
        $return['expired_purchases'] = $expired_count;

        $after_5day = date('Y-m-d', strtotime("+5 days"));
        $expiry_date = date('Y-m-d')." to ".$after_5day;
        $expired_in_5days_purchases = Purchase::where('company_id', $top_company)->whereNotNull('credit_days')->whereBetween("expiry_date", [date('Y-m-d'), $after_5day])->get();
        $expired_5day_count = 0;
        foreach($expired_in_5days_purchases as $item){
            $expired_grand_total = $item->grand_total;
            $expired_paid = $item->payments()->sum('amount');
            if($expired_grand_total == $expired_paid) continue;
            $expired_5day_count++;
        }
        $return['expired_in_5days_purchases'] = $expired_5day_count;
          
        return view('dashboard.home', compact('return', 'companies', 'top_company', 'key_array', 'purchase_array', 'sale_array', 'payment_array', 'period', 'expiry_date'));
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
        $sql = "select id from ".$table." where DATE_FORMAT(timestamp,'%Y%m') = DATE_FORMAT( CURDATE( ) ,'%Y%m' ) ".$where;
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
        $sql = "select id from ". $table . " where id > 0 ". $where;
        $orderables = collect(DB::select($sql))->pluck('id')->toArray();
        $return['count'] = count($orderables);
        if($table == 'purchases'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Purchase::class)->sum('subtotal');
            $return['total_paid'] = Payment::whereIn('paymentable_id', $orderables)->where('paymentable_type', Purchase::class)->sum('amount');
        }elseif($table == 'sales'){
            $return['total'] = Order::whereIn('orderable_id', $orderables)->where('orderable_type', Sale::class)->sum('subtotal');
            $return['total_paid'] = Payment::whereIn('paymentable_id', $orderables)->where('paymentable_type',Sale::class)->sum('amount');
        }  
        return $return;
    }

    public function set_pagesize(Request $request){
        $pagesize = $request->get('pagesize');
        if($pagesize == '') $pagesize = 100000;
        $request->session()->put('pagesize', $pagesize);
        return back();
    }

}
