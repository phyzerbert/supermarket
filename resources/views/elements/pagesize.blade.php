@php
    $pagesize = session('pagesize');
    if(!$pagesize){$pagesize = 15;}
@endphp     
<form class="form-inline ml-sm-3 float-left mb-2" action="{{route('set_pagesize')}}" method="post" id="pagesize_form">
    @csrf
    <label for="pagesize" class="control-label">{{__('page.show')}} :</label>
    <select class="form-control form-control-sm mx-sm-2" name="pagesize" id="pagesize">
        <option value="15" @if($pagesize == '15') selected @endif>15</option>
        <option value="50" @if($pagesize == '50') selected @endif>50</option>
        <option value="100" @if($pagesize == '100') selected @endif>100</option>
        <option value="200" @if($pagesize == '200') selected @endif>200</option>
        <option value="100" @if($pagesize == '500') selected @endif>500</option>
        <option value="" @if($pagesize == '100000') selected @endif>All</option>
    </select>
</form>