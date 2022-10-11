@extends('layouts.master')

@section('css')
    <style type="text/css">
        .table > thead > tr > th,
        .table > tbody > tr > th,
        .table > tfoot > tr > th,
        .table > thead > tr > td,
        .table > tbody > tr > td,
        .table > tfoot > tr > td {
            vertical-align: middle;
        }

        .dropdown-menu {
            left: -180px;
        }
    </style>
@endsection

@section('title', $title)

@section('actions')
    @if(!empty($dataTable->bulkActions()))
        {!! $dataTable->bulkActions() !!}
    @endif

    @if(!empty($dataTable->filters()))
        {!! CoralsForm::link('#'.$dataTable->getTableAttributes()['id'].'_filtersCollapse','<i class="fa fa-filter"></i>',['class'=>'btn btn-info','data'=>['toggle'=>"collapse"]]) !!}
    @endif
    @unless(isset($hideCreate))
        {!! CoralsForm::link(url($resource_url.'/create'), trans('Corals::labels.create'),['class'=>'btn btn-success']) !!}
    @endunless
@endsection

@section('content')
     @if ($resource_url == 'bt_config')
@component('components.box',['box_class'=>'box-primary'])
    <ul>
    <li>
    <p class="text-info m-t-10">
	<strong>Phone Numbers</strong> will be used for <strong>Incoming</strong> and <strong>Outgoing</strong> calls.
    </p>
    </li>
    <li>
    <p class="text-info">
      If your Setup Status is under review, then it will be activated in next 24 hours. (In the meanwhile, please proceed with creating <strong><a href='/bt_users'>Telephony Users</a></strong>.)
    </p>
    </li>
    </ul>
@endcomponent
  @elseif ($resource_url == 'bt_users')
@component('components.box',['box_class'=>'box-primary'])
  <p class="text-info">
    If your Sync Status is under review, then it will be approved once Bitrix Telephony > <a href='/bt_config'>Configuration</a> > Setup Status is <strong>Successful</strong>
  </p>
@endcomponent
  @endif 
    <div class="row">
        <div class="col-md-12">
            @component('components.box',['box_class'=>'box-primary'])
                @if(!empty($dataTable->filters()))
                    <div id="{{ $dataTable->getTableAttributes()['id'] }}_filtersCollapse"
                         class="filtersCollapse collapse">
                        <br/>
                        {!! $dataTable->filters() !!}
                    </div>
                @endif
                <div class="table-responsive m-t-10" style="min-height: 350px;padding-bottom: 20px;">
                    {!! $dataTable->table(['class' => 'table table-hover table-striped table-condensed dataTableBuilder','style'=>'width:100%;']) !!}
                </div>
            @endcomponent
        </div>
    </div>
@endsection

@section('js')

    {!! $dataTable->assets() !!}
    {!! $dataTable->scripts() !!}
@endsection
