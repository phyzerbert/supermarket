@php
    $role = Auth::user()->role->slug;
@endphp
<form action="" method="POST" class="form-inline top-search-form float-left" id="searchForm">
    @csrf
    <input type="hidden" name="sort_by_date" value="{{$sort_by_date}}" id="search_sort_date" />
    @if ($role == 'admin')    
        <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
            <option value="" hidden>{{__('page.select_company')}}</option>
            @foreach ($companies as $item)
                <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
            @endforeach        
        </select>
    @endif
    <select class="form-control form-control-sm mr-sm-2 mb-2" name="store_id" id="search_store">
        <option value="" hidden>{{__('page.select_store')}}</option>
        @foreach ($stores as $item)
            <option value="{{$item->id}}" @if ($store_id == $item->id) selected @endif>{{$item->name}}</option>
        @endforeach        
    </select>
    <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="reference_no" id="search_reference_no" value="{{$reference_no}}" placeholder="{{__('page.reference_no')}}">
    <select class="form-control form-control-sm mr-sm-2 mb-2 select2-show-search" name="supplier_id" id="search_supplier" data-placeholder="{{__('page.select_supplier')}}">
        <option value="">{{__('page.select_supplier')}}</option>
        @foreach ($suppliers as $item)
            <option value="{{$item->id}}" @if ($supplier_id == $item->id) selected @endif>{{$item->company}}</option>
        @endforeach
    </select>
    <input type="text" class="form-control form-control-sm mx-sm-2 mb-2" name="period" id="period" autocomplete="off" value="{{$period}}" placeholder="{{__('page.purchase_date')}}">
    <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
    <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
</form>