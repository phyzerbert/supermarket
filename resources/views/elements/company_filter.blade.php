<form action="" class="form-inline float-right" method="post" id="company_filter_form">
    @csrf
    <label for="company_filter">{{__('page.company')}} : </label>
    <select name="company_id" id="company_filter" class="form-control form-control-sm ml-2">
        @foreach ($companies as $item)
            <option value="{{$item->id}}" @if($company_id == $item->id) selected @endif>{{$item->name}}</option>
        @endforeach
    </select>
</form>