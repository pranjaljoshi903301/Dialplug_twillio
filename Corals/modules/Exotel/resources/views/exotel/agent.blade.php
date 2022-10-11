@extends('Exotel::exotel.layout')

@section('content')

<script>

function next_step(){
    location.reload(true);
}

function save_agent_detail(index,btn){
    agentphone = document.getElementById('agentphone_'+index).value;
    if (agentphone.length < 1) {
        document.getElementById('agentphone_'+index).classList.add("validation-failed");
        return;
    }else{
        document.getElementById('agentphone_'+index).classList.remove("validation-failed");
    }
    
    exophones = document.getElementById('exophones_'+index).value;
    if (exophones.length < 1) {
        document.getElementById('exophones_'+index).classList.add("validation-failed");
        return;
    }else{
        document.getElementById('exophones_'+index).classList.remove("validation-failed");
    }

    agentid = document.getElementById('agentid_'+index).value;        
    agentemail = document.getElementById('agentemails_'+index).value;            
    bitrix_domain = document.getElementById('bitrix_domain').value;        
    subscriber_id = document.getElementById('subscriber_id').value;        
    btn.innerHTML = "<i class='fa fa-spinner fa-spin'></i>"; 
    btn.disabled=true;

    var sending_data = {
        'agent_id': agentid, 
        'agent_email': agentemail, 
        'agent_phone': agentphone, 
        'exophone': exophones,
        'bitrix_domain': bitrix_domain,
        'subscriber_id': subscriber_id,
        "store_exotel_agent_details": true
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
            btn.innerHTML = "Saved";
            btn.disabled=false;
            document.getElementById('agentstatus_'+index).innerHTML="Registered";
        },          
        error: function (data) {
            console.log(data);
        }
    });

}

</script>

<div class="container-fluid pt-2">
    
    <span class="text-style heading">DialPlug – Bitrix24 Telephony</span>            
    <input type="hidden" id="bitrix_domain" name="bitrix_domain" value="{{ $bitrix_domain }}" required>
    <input type="hidden" id="subscriber_id" name="subscriber_id" value="{{ $subscriber_id }}" required>

    <div class="box">                          
        <div class="heading">
            <h4 class="text-style">Telephony Agents Details</h4>
            <button class="btn btn_save float-right" onclick="this.innerHTML=`<i class='fa fa-spinner fa-spin'></i>`; location.reload(true);">Next</button>
            <div class="clearfix"></div>
        </div>                                                
        <table id="datatable" class="table table-hover bg-white">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Agent Email</th>
                    <th>Extension</th>
                    <th>CallerId</th>                  
                    <th>Agent Phone</th>              
                    <th>Status</th>              
                    <th>Action</th>                      
                </tr>
            </thead>
            <tbody>                
                @php $i=0; @endphp
                @foreach($bitrix_agents as $agent)
                @php $i++; @endphp
                <tr>
                    <td>{{$i}}<input type="hidden" value="{{$agent['ID']}}" name="agentid_{{$i}}" id="agentid_{{$i}}"></td>
                    <td>{{$agent['EMAIL']}}<input type="hidden" value="{{$agent['EMAIL']}}" name="agentemails_{{$i}}" id="agentemails_{{$i}}"></td>
                    <td>{{$agent['EXTENSION']}}</td>
                    <td>
                        <select name="exophones_{{$i}}" id="exophones_{{$i}}" class="form-control">
                            <option value="">Select CallerId</option>
                            @foreach($exophones as $exophone)                        
                                @if($exophone==$agent["EXOPHONE"])
                                <option value="{{$exophone}}" selected>{{$exophone}}</option>
                                @else
                                <option value="{{$exophone}}">{{$exophone}}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td>{{$agent['agent_phone']}}
                        <input type="text" value="{{$agent['agent_phone']}}" class="form-control d-none" name="agentphone_{{$i}}" id="agentphone_{{$i}}" placeholder="Agent Phone" readonly>
                    </td>                  
                    <td><span id="agentstatus_{{$i}}">@if($agent['status']) Registered @else Not Registered @endif</span></td>
                    <td>                  
                        <div class="form-group">
                            <button onclick="save_agent_detail({{$i}},this)" class="btn btn_save">                            
                                @if($agent['status']) UPDATE @else SAVE @endif
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach                
            </tbody>              
            <tfoot>
                <tr>
                    <th>S.No</th>
                    <th>Agent Email</th>
                    <th>Extension</th>
                    <th>Exophone</th>                  
                    <th>Agent Phone</th>              
                    <th>Status</th>              
                    <th>Action</th>                      
                </tr>
            </tfoot>
        </table>
    </div>                    

</div>                    

@endsection