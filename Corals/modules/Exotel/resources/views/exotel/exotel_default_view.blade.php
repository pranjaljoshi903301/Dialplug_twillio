@extends('Exotel::exotel.layout')

@section('content')

<script>
function show(i,btn){
    btn.innerHTML = "<i class='fa fa-spinner fa-spin'></i>";         
    var sending_data = {
        'set_session': i, 
        'subscriber_id': subscriber_id.value
    }
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "/api/v1/exotel/store-details",
        method: 'post',
        data: sending_data,
        success: function (data) {            
            location.reload(true);
        },          
        error: function (data) {
            console.log(data);
        }
    });    
}
</script>

<div class="container-fluid pt-2">
    
    <span class="text-style heading">
        DialPlug – Bitrix24 Telephony
        <small class="float-right">
            Status: <b>@if($subscription_status) Active @else Not Active @endif</b> 
            <!-- <small class="text-success">Free Trial(1/10 Days)</small> -->
        </small>
    </span>
    <div class="clearfix"></div>
    <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{ $subscriber_id }}" required>

    <div class="box">                                          
        <div class="heading">
            <ul>
                <li>
                    <a  onclick="show(1,this)"> <b class="text-style">Telephony Details</b></a><hr>
                </li>
                <li>
                    <a  onclick="show(2,this)"> <b class="text-style">Agent Details</b></a><hr>
                </li>
                <li>
                    <a  onclick="show(0,this)"> <b class="text-style">Incoming & Outgoing Configuration</b></a><hr>
                </li>
            </ul>
        </div>                            
    </div>

    <div class="box" id="data">                                  
    </div>

</div>

<style>
    .heading ul{
        list-style: none;
        display: contents;
    }
    .heading a:hover{
        border-bottom: solid;        
    }
    .heading a{
        cursor: pointer;
    }
    .heading li{
        margin-top: 1.2rem;
    }
</style>

<script src="//api.bitrix24.com/api/v1/"></script>
<script>
    try{
        BX24.install(function(){
            BX24.installFinish();
        });
    }catch(e){ console.log(e) }
</script>

@endsection