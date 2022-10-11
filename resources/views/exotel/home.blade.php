@extends('exotel.layout')

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
        "subscriber_id": -1,
        "email": email.value,
        "webhook": webhook.value,
        "exotel_sid": exotel_sid.value,
        "exotel_api_key": exotel_api_key.value,
        "exotel_api_token": exotel_api_token.value,
        "bitrix_domain": bitrix_domain.value,
        "store_exotel_details": true
    };

    var el = document.getElementById('subscriber_id'); 
    if(el!=null) sending_data.subscriber_id=el.value;

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
            if(data=="Webhook"){   
                webhook.classList.add("validation-failed");
                step1_btn.innerHTML = "Submit";
                step1_btn.disabled = false;
            }
            else if(data=="Exotel"){
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
    
    <span class="text-style heading">DialPlug Exotel Click-to-Call</span>            
    <input type="hidden" id="bitrix_domain" name="bitrix_domain" value="{{ $bitrix_domain }}" required>

    <div class="box">                          
        <div class="heading">
            <h4 class="text-style">Exotel Details</h4>
        </div>
        @if(isset($exotel_detail))
            <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{$exotel_detail['id']}}">
        @endif
        <div class="form-group">              
            <label class="text-style" for="">Your Email *</label>        
            <input type="email" class="form-control" id="email" name="email" value="@if(isset($exotel_detail)){{$exotel_detail['subscriber_email']}}@endif" @if(isset($exotel_detail)) disabled @endif required>              
        </div>
        <div class="form-group">
            <label class="text-style" for="">Your inbound webhook *</label>        
            <input type="text" class="form-control" id="webhook" name="webhook" value="@if(isset($exotel_detail)){{$exotel_detail['webhook']}}@endif" required>              
        </div>
        <hr>        
        <h4 class="text-style">API Details:</h4>
        <small class="float-right">find your api credential: <a href="https://my.exotel.com/apisettings/site#api-credentials" target="_blank">click here</a></small>        
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="text-style" for="">Exotel SID *</label>        
            <input type="text" class="form-control" id="exotel_sid" name="exotel_sid" value="@if(isset($exotel_detail)){{$exotel_detail['exotel_sid']}}@endif" required>                          
        </div>
        <div class="form-group">
            <label class="text-style" for="">Exotel API Key *</label>        
            <input type="text" class="form-control" id="exotel_api_key" name="exotel_api_key" value="@if(isset($exotel_detail)){{$exotel_detail['exotel_api_key']}}@endif" required>              
        </div>
        <div class="form-group">
            <label class="text-style" for="">Exotel API Token *</label>        
            <input type="text" class="form-control" id="exotel_api_token" name="exotel_api_token" value="@if(isset($exotel_detail)){{$exotel_detail['exotel_api_token']}}@endif" required>              
        </div>            
        <div class="form-group">
            <button onclick="submit_step1_btn()" id="step1_btn" class="btn btn_save">
                @if(isset($exotel_detail)) UPDATE @else SAVE @endif                
            </button>
            @if(isset($exotel_detail))
            <button onclick="this.innerHTML=`<i class='fa fa-spinner fa-spin'></i>`; location.reload(true);" class="btn btn-cancel">
                Cancel
            </button>
            @endif
        </div>
    </div>                    

</div>                    

@endsection