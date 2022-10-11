@extends('layouts.crud.create_edit')
@section('content_header')
<section class="content-header">
    <h1>
        Manage Details[{{$bitrixmobile->email}}]
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/bm_config">Users</a></li>
        <li class="breadcrumb-item active">Manage Details</li>
    </ol>
</section>
@endsection

@section('content')
@parent
<div class="row">
    <div class="col-md-12">
        @component('components.box')   
            <form method="post" action="{{url('/bm_config/'.$bitrixmobile->hashed_id.'/update_details')}}">@csrf                                        
                {{ csrf_field() }}
                <div class="row">
                    {{-- Dialplug Fields --}}
                    <div class="col-md-12">
                        {!! CoralsForm::text('webhook_url', 'Webhook', true,$bitrixmobile->webhook_url) !!}                    
                        <button class="btn btn-success ladda-button" type="submit" data-style="expand-right">
                            <span class="ladda-label"><i class="fa fa-save"></i> Update Webhook</span>
                            <span class="ladda-spinner"></span>
                        </button>&nbsp;&nbsp;
                    </div>
                    {{-- Dialplug Fields End --}}
                </div>
            </form>     <hr>
            @php
                $agent_arr = $bitrixmobile->agent_detail;
            @endphp                     
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Agent Email</th>
                        <th>IMEI</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($agent_arr!=null)
                        @php $agent_arr=json_decode($agent_arr,true); $i=0; @endphp
                        @foreach($agent_arr as $agent)                        
                            @php $i++; @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $agent['email'] }}</td>
                                <td>{{ $agent['imei'] }}</td>
                                <td>{{ date('d M, Y',strtotime($agent['created_at'])) }}</td>
                                <td>
                                    <form method="post" action="{{url('/bm_config/'.$bitrixmobile->hashed_id.'/update_details')}}">@csrf                                        
                                        <input type="hidden" value="{{ $agent['imei'] }}" name="imei" />
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="10">No matching records found</td>
                    </tr>                                                
                    @endif
                </tbody>
                
            </table>
        </form>
        @endcomponent
    </div>
</div>
@endsection
