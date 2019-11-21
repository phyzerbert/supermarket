@php
    $role = Auth::user()->role->slug;
@endphp
<form action="" method="POST" class="form-inline float-left" id="searchForm">
    @csrf
    <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="code" id="search_code" value="{{$code}}" placeholder="{{__('page.product_code')}}">
    <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="name" id="search_name" value="{{$name}}" placeholder="{{__('page.product_name')}}">
    <select class="form-control form-control-sm mr-sm-2 mb-2" name="category_id" id="search_category">
        <option value="" hidden>{{__('page.select_category')}}</option>
        @foreach ($categories as $item)
            <option value="{{$item->id}}" @if ($category_id == $item->id) selected @endif>{{$item->name}}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
    <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
</form>