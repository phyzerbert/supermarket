@php
    $page = config('site.page');
    $user = Auth::user();
    $role = $user->role->slug;
@endphp
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div class="user-details">
            <div class="pull-left">
                <img src="@if (Auth::user()->picture != ''){{asset(Auth::user()->picture)}} @else {{asset('images/avatar128.png')}} @endif" class="thumb-md rounded-circle" alt="">
            </div>
            <div class="user-info">
                <div class="">
                    @if($user->first_name || $user->last_name)
                        {{$user->first_name}} {{$user->last_name}}
                    @else
                        {{$user->name}}
                    @endif
                </div>                
                <p class="text-muted m-0">{{$user->role->name}}</p>
            </div>
        </div>
        
        <div id="sidebar-menu">
            <ul>
                @if ($role != 'buyer')
                    <li class="@if($page == 'home') active @endif">
                        <a href="{{route('home')}}" class="waves-effect @if($page == 'home') active @endif"><i class="fa fa-dashboard"></i><span> {{__('page.dashboard')}} </span></a>
                    </li>
                @endif
                
                @if ($role != 'buyer')
                    {{-- Purchase --}}
                    @php
                        $purchase_items = ['purchase', 'purchase_list', 'purchase_create'];
                    @endphp

                    <li class="has_sub">
                        <a href="#" class="waves-effect @if($page == in_array($page, $purchase_items)) active subdrop @endif"><i class="fa fa-sign-in"></i><span> {{__('page.purchases')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                        <ul class="list-unstyled">
                            <li class="@if($page == 'purchase_list') active @endif"><a href="{{route('purchase.index')}}" class="@if($page == 'purchase_list') active @endif">{{__('page.purchases_list')}}</a></li>
                            @if($user->hasRole('user') || $user->hasRole('secretary'))
                                <li class="@if($page == 'purchase_create') active @endif"><a href="{{route('purchase.create')}}" class="@if($page == 'purchase_create') active @endif">{{__('page.add_purchase')}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @if($role != 'secretary')
                        {{-- Sale --}}
                        @php
                            $sale_items = ['sale', 'sale_list', 'sale_create'];
                        @endphp
                        <li class="has_sub">
                            <a href="#" class="waves-effect @if($page == in_array($page, $sale_items)) active subdrop @endif"><i class="fa fa-sign-out"></i><span> {{__('page.sales')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled">
                                <li class="@if($page == 'sale_list') active @endif"><a href="{{route('sale.index')}}" class="@if($page == 'sale_list') active @endif">{{__('page.sales_list')}}</a></li>
                                @if($user->hasRole('user') || $user->hasRole('secretary'))
                                    <li class="@if($page == 'sale_create') active @endif"><a href="{{route('sale.create')}}" class="@if($page == 'sale_create') active @endif">{{__('page.add_sale')}}</a></li>
                                @endif
                            </ul>
                        </li>
                        @php
                            if($user->company){
                                $number_of_pending_purchases = $user->company->purchases()->where('status', 0)->count();
                                $number_of_pending_payments = $user->company->purchases()->where('status', 0)->count();
                            }else{                                
                                $number_of_pending_purchases = \App\Models\Purchase::where('status', 0)->count();
                                $number_of_pending_payments = \App\Models\Payment::where('status', 0)->count();
                            }
                        @endphp
                        <li class="@if($page == 'pending_purchases') active @endif">
                            <a href="{{route('purchase.pending_purchases')}}" 
                                class="waves-effect @if($page == 'pending_purchases') active @endif @if($number_of_pending_purchases > 0) text-danger @endif"
                            >
                                <i class="fa fa-filter"></i><span> {{__('page.pending_purchases')}} </span>
                            </a>
                        </li>
                        <li class="@if($page == 'pending_payments') active @endif">
                            <a href="{{route('payment.pending_payments')}}" 
                                class="waves-effect @if($page == 'pending_payments') active @endif @if($number_of_pending_payments > 0) text-danger @endif"
                            >
                                <i class="fa fa-flask"></i><span> {{__('page.pending_payments')}} </span>
                            </a>
                        </li>
                    @endif
                    <li class="@if($page == 'product') active @endif">
                        <a href="{{route('product.index')}}" class="waves-effect @if($page == 'product') active @endif"><i class="fa fa-cube"></i><span> {{__('page.product')}} </span></a>
                    </li>
                @endif
                {{-- Pre Order --}}
                @php
                    $pre_order_items = ['pre_order', 'pre_order_list', 'pre_order_create'];
                @endphp

                <li class="has_sub">
                    <a href="#" class="waves-effect @if($page == in_array($page, $pre_order_items)) active subdrop @endif"><i class="fa fa-paper-plane-o"></i><span> {{__('page.purchase_orders')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="@if($page == 'pre_order_list') active @endif"><a href="{{route('pre_order.index')}}" class="@if($page == 'pre_order_list') active @endif">{{__('page.purchase_orders')}}</a></li>
                        @if($role != 'admin')
                            <li class="@if($page == 'pre_order_create') active @endif"><a href="{{route('pre_order.create')}}" class="@if($page == 'pre_order_create') active @endif">{{__('page.add_purchase_order')}}</a></li>
                        @endif
                    </ul>
                </li>

                <li class="@if($page == 'received_order') active @endif">
                    <a href="{{route('received_order.index')}}" class="waves-effect @if($page == 'received_order') active @endif"><i class="fa fa-chain"></i><span> {{__('page.received_orders')}} </span></a>
                </li>

                @if($role != 'buyer')

                    @php
                        $report_items = [
                            'overview_chart', 
                            'company_chart', 
                            'store_chart', 
                            'product_quantity_alert', 
                            'product_expiry_alert', 
                            'expired_purchases_report',
                            'products_report', 
                            'categories_report', 
                            'sales_report', 
                            'purchases_report', 
                            'daily_sales', 
                            'monthly_sales', 
                            'payments_report', 
                            'customers_report', 
                            'suppliers_report', 
                            'users_report',
                        ];
                    @endphp

                    <li class="has_sub">
                        <a href="#" class="waves-effect @if($page == in_array($page, $report_items)) active subdrop @endif"><i class="fa fa-file-text-o"></i><span> {{__('page.reports')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                        <ul class="list-unstyled">
                            <li class="@if($page == 'overview_chart') active @endif"><a href="{{route('report.overview_chart')}}" class="@if($page == 'overview_chart') active @endif">{{__('page.overview_chart')}}</a></li>
                            <li class="@if($page == 'company_chart') active @endif"><a href="{{route('report.company_chart')}}" class="@if($page == 'company_chart') active @endif">{{__('page.company_chart')}}</a></li>
                            <li class="@if($page == 'store_chart') active @endif"><a href="{{route('report.store_chart')}}" class="@if($page == 'store_chart') active @endif">{{__('page.store_chart')}}</a></li>
                            <li class="@if($page == 'product_quantity_alert') active @endif"><a href="{{route('report.product_quantity_alert')}}" class="@if($page == 'product_quantity_alert') active @endif">{{__('page.product_quantity_alert')}}</a></li>
                            <li class="@if($page == 'product_expiry_alert') active @endif"><a href="{{route('report.product_expiry_alert')}}" class="@if($page == 'product_expiry_alert') active @endif">{{__('page.product_expiry_alert')}}</a></li>
                            <li class="@if($page == 'expired_purchases_report') active @endif"><a href="{{route('report.expired_purchases_report')}}" class="@if($page == 'expired_purchases_report') active @endif">{{__('page.expired_purchases_report')}}</a></li>
                            <li class="@if($page == 'products_report') active @endif"><a href="{{route('report.products_report')}}" class="@if($page == 'products_report') active @endif">{{__('page.product_report')}}</a></li>
                            <li class="@if($page == 'categories_report') active @endif"><a href="{{route('report.categories_report')}}" class="@if($page == 'categories_report') active @endif">{{__('page.category_report')}}</a></li>
                            <li class="@if($page == 'sales_report') active @endif"><a href="{{route('report.sales_report')}}" class="@if($page == 'sales_report') active @endif">{{__('page.sales_report')}}</a></li>
                            <li class="@if($page == 'purchases_report') active @endif"><a href="{{route('report.purchases_report')}}" class="@if($page == 'purchases_report') active @endif">{{__('page.purchases_report')}}</a></li>
                            <li class="@if($page == 'payments_report') active @endif"><a href="{{route('report.payments_report')}}" class="@if($page == 'payments_report') active @endif">{{__('page.payments_report')}}</a></li>
                            <li class="@if($page == 'income_report') active @endif"><a href="{{route('report.income_report')}}" class="@if($page == 'income_report') active @endif">{{__('page.income_report')}}</a></li>
                            <li class="@if($page == 'customers_report') active @endif"><a href="{{route('report.customers_report')}}" class="@if($page == 'customers_report') active @endif">{{__('page.customers_report')}}</a></li>
                            <li class="@if($page == 'suppliers_report') active @endif"><a href="{{route('report.suppliers_report')}}" class="@if($page == 'suppliers_report') active @endif">{{__('page.suppliers_report')}}</a></li>
                            <li class="@if($page == 'users_report') active @endif"><a href="{{route('report.users_report')}}" class="@if($page == 'users_report') active @endif">{{__('page.users_report')}}</a></li>
                        </ul>
                    </li>

                    @php
                        $people_items = ['user', 'customer', 'supplier'];
                    @endphp
                    <li class="has_sub">
                        <a href="#" class="waves-effect @if($page == in_array($page, $people_items)) active subdrop @endif"><i class="fa fa-user"></i><span> {{__('page.people')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                        <ul class="list-unstyled">
                            @if($role == 'admin')
                                <li class="@if($page == 'user') active @endif"><a href="{{route('users.index')}}" class="@if($page == 'user') active @endif">{{__('page.user')}}</a></li>
                            @endif
                            <li class="@if($page == 'customer') active @endif"><a href="{{route('customer.index')}}" class="@if($page == 'customer') active @endif">{{__('page.customer')}}</a></li>
                            <li class="@if($page == 'supplier') active @endif"><a href="{{route('supplier.index')}}" class="@if($page == 'supplier') active @endif">{{__('page.supplier')}}</a></li>
                        </ul>
                    </li>

                    @php
                        $setting_items = ['category', 'store', 'company', 'tax_rate', 'account', 'currency'];
                    @endphp

                    <li class="has_sub">
                        <a href="#" class="waves-effect @if($page == in_array($page, $setting_items)) active subdrop @endif"><i class="fa fa-cog"></i><span> {{__('page.setting')}} </span><span class="pull-right"><i class="md md-add"></i></span></a>
                        <ul class="list-unstyled">
                            <li class="@if($page == 'currency') active @endif"><a href="{{route('currency.index')}}" class="@if($page == 'currency') active @endif">{{__('page.currency')}}</a></li>
                            <li class="@if($page == 'account') active @endif"><a href="{{route('account.index')}}" class="@if($page == 'account') active @endif">{{__('page.account')}}</a></li>
                            <li class="@if($page == 'company') active @endif"><a href="{{route('company.index')}}" class="@if($page == 'company') active @endif">{{__('page.company')}}</a></li>
                            <li class="@if($page == 'store') active @endif"><a href="{{route('store.index')}}" class="@if($page == 'store') active @endif">{{__('page.store')}}</a></li>
                            <li class="@if($page == 'tax_rate') active @endif"><a href="{{route('tax_rate.index')}}" class="@if($page == 'tax_rate') active @endif">{{__('page.tax_rate')}}</a></li>
                        </ul>
                    </li>
                @endif

                <li class="@if($page == 'concurrent_payments') active @endif">
                    <a href="{{route('concurrent_payments')}}" class="waves-effect @if($page == 'concurrent_payments') active @endif"><i class="fa fa-cube"></i><span> {{__('page.concurrent_payments')}} </span></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>