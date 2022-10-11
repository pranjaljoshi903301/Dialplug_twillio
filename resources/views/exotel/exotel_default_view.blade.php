@extends('exotel.layout')

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
    
    <span class="text-style heading">DialPlug Exotel Click-to-Call</span>                
    <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{ $subscriber_id }}" required>

    <div class="box">                                  
        <div class="heading">
            <button class="btn" onclick="show(1,this)">    
                <h4 class="text-style">General Details</h4>
            </button>
            <button class="btn" onclick="show(2,this)">    
                <h4 class="text-style">Agent Detail</h4>
            </button>
            <button class="btn" onclick="show(0,this)">    
                <h4 class="text-style">Incoming & Outgoing Configuration</h4>                    
            </button>
        </div>                            
    </div>

    <div class="box" id="data">                                  
    </div>

</div>

@endsection