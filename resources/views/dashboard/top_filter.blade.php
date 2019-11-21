<form action="" class="form-inline float-right" method="post" id="top_filter_form">
    @csrf
    <div class="form-group">
        <label for="top_company_filter">{{__('page.company')}} : </label>
        <select name="top_company" id="top_company_filter" class="form-control form-control-sm ml-2">
            @foreach ($companies as $item)
                <option value="{{$item->id}}" @if($top_company == $item->id) selected @endif>{{$item->name}}</option>
            @endforeach
        </select>
    </div>
</form>