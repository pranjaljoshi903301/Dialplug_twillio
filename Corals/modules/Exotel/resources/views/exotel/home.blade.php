@extends('Exotel::exotel.layout')

@section('content')

<script>

function form_validation(){
    
    var input = $(".form-control");        
    $(input).each(function(index) {
        var inputValue = $(this).val();
        var inputLength = inputValue.length;
        if (inputLength < 1) {
            input[index].classList.add("validation-failed");
        }else{
            input[index].classList.remove("validation-failed");
        }
    });
    if ($("input").hasClass("validation-failed")) return false;
    return true;
}

function submit_step1_btn(){        
    
    if(!form_validation()){
        return;
    }
    
    step1_btn.innerHTML = "<i class='fa fa-spinner fa-spin'></i>"
    step1_btn.disabled = true;
        
    var sending_data = {
        "subscriber_id": subscriber_id.value,                
        "exotel_sid": exotel_sid.value,
        "exotel_api_key": exotel_api_key.value,
        "exotel_api_token": exotel_api_token.value,        
        "store_exotel_details": true
    };

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
            console.log("Error: "+JSON.stringify(data));
            if(data=="Exotel"){
                exotel_sid.classList.add("validation-failed");
                exotel_api_key.classList.add("validation-failed");
                exotel_api_token.classList.add("validation-failed");
                step1_btn.innerHTML = "Submit";
                step1_btn.disabled = false;
            }
            else{
                location.reload(true);
            }            
        },          
        error: function (data) {
            console.log("Error: "+data);
        }
    });


}

</script>

<div class="container-fluid pt-2">
    
    <span class="text-style heading">DialPlug – Bitrix24 Telephony</span>                

    <div class="box">                          
        <div class="heading">
            <h4 class="text-style">Telephony Details</h4>
        </div>        
        <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{$exotel_detail->id}}">        
        <small class="float-right">for exotel telephony - find your api credentials <a href="https://my.exotel.com/apisettings/site#api-credentials" target="_blank">click here</a></small>
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="text-style" for="">Telephony SID *</label>        
            <input type="text" class="form-control" id="exotel_sid" name="exotel_sid" value="{{$exotel_detail->exotel_sid}}" required>                          
        </div>
        <div class="form-group">
            <label class="text-style" for="">Telephony API Key *</label>        
            <input type="text" class="form-control" id="exotel_api_key" name="exotel_api_key" value="{{$exotel_detail->exotel_api_key}}" required>              
        </div>
        <div class="form-group">
            <label class="text-style" for="">Telephony API Token *</label>        
            <input type="text" class="form-control" id="exotel_api_token" name="exotel_api_token" value="{{$exotel_detail->exotel_api_token}}" required>              
        </div>            
        <div class="form-group">
            <button onclick="submit_step1_btn()" id="step1_btn" class="btn btn_save">
                @if($exotel_detail->exotel_sid) UPDATE @else SAVE @endif                
            </button>
            @if($exotel_detail->exotel_sid)
            <button onclick="this.innerHTML=`<i class='fa fa-spinner fa-spin'></i>`; location.reload(true);" class="btn btn-cancel">
                Cancel
            </button>
            @endif
        </div>
    </div>                    

</div>                    

<script src="//api.bitrix24.com/api/v1/"></script>
<script>
    try{
        BX24.install(function(){
            BX24.installFinish();
        });
    }catch(e){ console.log(e) }
</script>

@endsection