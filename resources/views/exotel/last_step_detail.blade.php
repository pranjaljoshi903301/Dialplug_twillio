@extends('exotel.layout')

@section('content')

<script>
function copy_to_clipboard(containerid){

    if (window.getSelection) {
        if (window.getSelection().empty) { // Chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) { // Firefox
            window.getSelection().removeAllRanges();
        }
    } else if (document.selection) { // IE?
        document.selection.empty();
    }

    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("copy");
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
        document.execCommand("copy");
    }
    
}
</script>

<div class="container-fluid pt-2">
    
    <span class="text-style heading">DialPlug Exotel Click-to-Call</span>            
    <input type="hidden" id="bitrix_domain" name="bitrix_domain" value="{{ $bitrix_domain }}" required>

    <div class="box">                          
        <div class="heading">
            <h4 class="text-style">Incoming & Outgoing Configuration</h4>
            <button class="btn btn_save float-right" onclick="this.innerHTML=`<i class='fa fa-spinner fa-spin'></i>`; location.reload(true);">FINISH</button>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="bg-white p-4">
                    <span>Create an Outbound Webhook in your Bitrix24 instance</span>
                    <ol>
                        <li>Bitrix24 > Developer resources</li>
                        <li>Common use cases > Other</li>
                        <li>Select Outbound Webhook</li>
                        <li>Handler Address : <button class="btn" onclick="copy_to_clipboard('outbound_webhook')"><i class="fa fa-copy"></i></button><br>
                            <i id="outbound_webhook">https://dialplug.com/api/v1/exotel/click-to-call</i>
                        </li>
                        <li>Name : <i>Dialplug_Outbound</i></li>
                        <li>Select Events :
                            <ul>
                                <li>External phone call start (ONEXTERNALCALLSTART)</li>
                            </ul>
                        </li>
                        <li>Save</li>            
                    </ol>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white p-4">
                    <span>Create an Callback URL in your Exotel instance</span>
                    <ol>
                        <li>Admin > App Bazzar</li>
                        <li>Under CALL SETUP > Edit Call App</li>
                        <li>For every Connect Applets</li>
                        <li>Create popup... : <button class="btn" onclick="copy_to_clipboard('popup_url')"><i class="fa fa-copy"></i></button><br>
                            <i id="popup_url">https://dialplug.com/api/v1/exotel/incoming-click-to-call?bitrix_domain={{$bitrix_domain}}</i>
                        </li>                        
                        <li>Save</li>            
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection