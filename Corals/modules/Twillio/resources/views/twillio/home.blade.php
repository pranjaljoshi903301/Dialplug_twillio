@extends('Twillio::twillio.layout')

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
        "twillio_sid": twillio_sid.value,
        "twillio_api_key": twillio_api_key.value,
        "twillio_api_token": twillio_api_token.value,        
        "store_twillio_details": true
    };

    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "/api/v1/twillio/store-details",
        method: 'post',
        data: sending_data,
        success: function (data) {
            console.log("Error: "+JSON.stringify(data));
            if(data=="Twillio"){
                twillio_sid.classList.add("validation-failed");
                twillio_api_key.classList.add("validation-failed");
                twillio_api_token.classList.add("validation-failed");
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
        <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{$twillio_detail->id}}">        
        <small class="float-right">for twillio telephony - find your api credentials <a href="https://my.twillio.com/apisettings/site#api-credentials" target="_blank">click here</a></small>
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="text-style" for="">Telephony SID *</label>        
            <input type="text" class="form-control" id="twillio_sid" name="twillio_sid" value="{{$twillio_detail->twillio_sid}}" required>                          
        </div>
        <div class="form-group">
            <label class="text-style" for="">Telephony API Key *</label>        
            <input type="text" class="form-control" id="twillio_api_key" name="twillio_api_key" value="{{$twillio_detail->twillio_api_key}}" required>              
        </div>
        <div class="form-group">
            <label class="text-style" for="">Telephony API Token *</label>        
            <input type="text" class="form-control" id="twillio_api_token" name="twillio_api_token" value="{{$twillio_detail->twillio_api_token}}" required>              
        </div>            
        <div class="form-group">
            <button onclick="submit_step1_btn()" id="step1_btn" class="btn btn_save">
                @if($twillio_detail->twillio_sid) UPDATE @else SAVE @endif                
            </button>
            @if($twillio_detail->twillio_sid)
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