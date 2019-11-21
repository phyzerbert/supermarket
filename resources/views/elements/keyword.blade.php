<form action="" class="form-inline float-right" method="post" id="keyword_filter_form">
    @csrf    
    <input type="text" name="keyword" id="keyword_filter" value="{{$keyword}}" class="form-control form-control-sm" placeholder="Keyword" />
</form>