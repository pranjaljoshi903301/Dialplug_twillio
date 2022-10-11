@extends('Twillio::twillio.layout')

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
    
    <span class="text-style heading">DialPlug – Bitrix24 Telephony</span>                
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
                    <h4>Bitrix Configuration [for outgoing calls]</h4><hr>
                    <span>Choose DialPlug – Bitrix24 Telephony Application for All Outgoing Calls</span> 
                    <ol>
                        <li>Go to Telephony in Bitrix24 instance</li>
                        <li>Click <b>Configure Telephony</b> and select <b>Telephony Settings</b></li>
                        <li>In Configure default numbers choose <b>DialPlug – Bitrix24 Telephony</b> as the Default number for outgoing calls</li>
                    </ol>                    
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white p-4">
                    <h4>Twillio Configuration [for incoming calls]</h4><hr>
                    <span>Create an Callback URL in your Telephony instance</span><br>
                    <span>Example: Twillio as Telephony Instance</span>
                    <ol>
                        <li>Admin > App Bazzar [<a href="https://my.twillio.com/apps#installed-apps" target="_blank">click here</a>]</li>
                        <li>Under CALL SETUP > Edit Call App</li>
                        <li>For every Connect Applets</li>
                        <ul>
                            <li>Paste this url under <b>Create popup</b> : <button class="btn" onclick="copy_to_clipboard('popup_url')"><i class="fa fa-copy"></i></button><br>
                                <i id="popup_url">https://dialplug.com/api/v1/twillio/incoming-click-to-call?bitrix_domain={{$bitrix_domain}}</i>
                            </li>                        
                        </ul>                        
                        <li>Save</li>            
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection