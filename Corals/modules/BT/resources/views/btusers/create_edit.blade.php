@extends('layouts.crud.create_edit')

@section('css')
    <style>

        .video-instruction {
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot
        @slot('breadcrumb')
            {{ Breadcrumbs::render('bt_user_create_edit') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                <div class="row">
                    
                    <div class="col-md-12">
                        <video class="video-instruction" controls src="@php
                        echo url('/bitrix_videos/creating_and_setting_extension.mp4');
                    @endphp"></video>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                {!! CoralsForm::openForm($bitrixtelephony) !!}
                <div class="row">
                    {{-- Dialplug Fields --}}
                    <div class="col-md-6 instructions">
                            <p><strong>Step 1:</strong> Create Users/Agent in your Bitrix24 instance</p>
                            <ol>
                                <li>Bitrix24 > Telephony</li>
                                <li>Configure telephony > Telephony users</li>
				                <li>Double click on a Agent/Employee to configure</li>
				                <ul>
					                <li>Set Any Extension Number</li>
					                <li>Select Number for outgoing calls as <em>Dialplug_Outbound</em></li>
				                </ul>
                                <li>Reload Dialplug Dashboard and Select Agent</li>
                            </ol>
                    </div>
                        <div class="col-md-6">
                            <p><strong>Step 2:</strong> After Step 1 is done, you would see list of Agents here, and then select users to be part of subscription</p>

                            @if (isSuperUser())
                {!! CoralsForm::text('user_id', '', true,$bitrixtelephony->user_id,['class'=>'hidden']) !!}
                @endif

                @php
                $exist_users =
                DB::table('bt_users')->where('user_id',$bitrixtelephony->user_id)->pluck('bitrix_user_name')->toArray();
                $bitrixtelephony->users = array_diff($bitrixtelephony->users,$exist_users);
                @endphp
                            {!! CoralsForm::select('bitrix_user_id', 'Select Agent', $bitrixtelephony->users, true, $bitrixtelephony->bitrix_user_id, ['help_text' => 'If your agent/employee is not listed here, please confirm extension in bitrix24 or Webhook URL.'], 'select') !!}
                        @if (!$bitrixtelephony->default_user || $bitrixtelephony->default_user_id != null && $bitrixtelephony->id == $bitrixtelephony->default_user_id)
                            {!! CoralsForm::checkbox('is_default', 'Set as Default', $bitrixtelephony->is_default??false, null, ['help_text' => 'Default Agent will receive initial calls. <br>After the initial call, <strong>Responsible Person</strong> can be assigned to Leads/Deals/Contacts']) !!}
                        @endif
                    </div>
                    {{-- Dialplug Fields End --}}
                </div>
                {!! CoralsForm::customFields($bitrixtelephony) !!}

                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>
                {!! CoralsForm::closeForm($bitrixtelephony) !!}
            @endcomponent
        </div>
    </div>
@endsection

@section('js')
@endsection
