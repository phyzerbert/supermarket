@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-money"></i> {{__('page.currency_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.currency')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
                $rate_bolivar = \App\Models\Currency::find(1)->rate;
                $rate_dollar = \App\Models\Currency::find(2)->rate;
                $rate_euro = \App\Models\Currency::find(3)->rate;
            @endphp

            <div class="card card-body card-fill p-md-5 ">
                <form action="{{route('currency.save')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Bolivar</h4>
                            <input type="text" class="form-control" name="rate_bolivar" value="{{$rate_bolivar}}" />
                        </div>
                        <div class="col-md-4">
                            <h4>Dollar</h4>
                            <input type="text" class="form-control" name="rate_dollar" value="{{$rate_dollar}}" />
                        </div>
                        <div class="col-md-4">
                            <h4>Euro</h4>
                            <input type="text" class="form-control" name="rate_euro" value="{{$rate_euro}}" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-2"></i>{{__('page.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- <div class="card card-body card-fill">
                <div class="">
                    @if ($role == 'admin')
                        <button type="button" class="btn btn-success btn-sm float-right mg-b-5" id="btn-add"><i class="icon ion-plus mg-r-2"></i>{{__('page.add_new')}}</button>
                    @endif
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr class="bg-blue">
                                <th width="40">#</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.rate')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="rate" data-value='{{$item->rate}}'>{{number_format($item->rate, 2)}}</td>
                                    <td class="py-1">
                                        <a href="#" class="btn btn-sm btn-primary btn-icon mr-1 btn-edit" data-id="{{$item->id}}"><div><i class="fa fa-edit"></i></div></a>
                                        <a href="{{route('currency.delete', $item->id)}}" class="btn btn-sm btn-danger btn-icon btn-confirm" data-id="{{$item->id}}"><div><i class="fa fa-trash-o"></i></div></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" align="center">{{__('page.no_data')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>  
                </div>
            </div>
        </div>                
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_currency')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('currency.create')}}" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.rate')}}</label>
                            <input class="form-control rate" type="text" name="rate" placeholder="{{__('page.rate')}}">
                        </div>
                    </div>    
                    <div class="modal-footer">
                        <button type="submit" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_currency')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('currency.edit')}}" id="edit_form" method="post">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.rate')}}</label>
                            <input class="form-control rate" type="text" name="rate" placeholder="{{__('page.rate')}}">
                        </div>
                    </div>  
                    <div class="modal-footer">
                        <button type="submit" id="btn_update" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@endsection

@section('script')
<script>
    $(document).ready(function () {
        
        $("#btn-add").click(function(){
            $("#create_form input.form-control").val('');
            $("#create_form .invalid-feedback strong").text('');
            $("#addModal").modal();
        });

        $(".btn-edit").click(function(){
            let id = $(this).data("id");
            let name = $(this).parents('tr').find(".name").text().trim();
            let rate = $(this).parents('tr').find(".rate").data('value');
            $("#edit_form input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .name").val(name);
            $("#editModal .rate").val(rate);
            $("#editModal").modal();
        });

    });
</script>
@endsection
