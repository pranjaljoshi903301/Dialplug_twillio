@extends('layouts.crud.create_edit')

@php
$isValid = false;
foreach (user()->roles as $role) {
	if($role->name == 'operations') {
		$isValid = true;
	}
}
@endphp

@section('css')
		<style>

				.video-instruction {
						width: 100%;
						height: 100%;
				}
				.fa-clipboard {
					cursor: pointer
				}
				.fa-clipboard:hover {
					opacity: 0.7;
				}
		</style>
		<script>
			const copyToClipboard = () => {
				const handlerAddress = document.getElementById('handler-address');
				const el = document.createElement('textarea');
				el.value = handlerAddress.textContent;
				el.setAttribute('readonly', '');
				el.style.position = 'absolute';
				el.style.left = '-9999px';
				document.body.appendChild(el);
				el.select();
				document.execCommand('copy');
				document.body.removeChild(el);
			};
		</script>
@endsection

@section('content_header')
		@component('components.content_header')
				@slot('page_title')
						{{ $title_singular }}
				@endslot
				@slot('breadcrumb')
						{{ Breadcrumbs::render('bt_bitrixtelephony_create_edit') }}
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
												<video class="video-instruction" controls src="
														@php
																echo url('/bitrix_videos/creating_webhooks.mp4');
														@endphp"
												>
												</video>
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

										{{-- Dialplug Fields End --}}
										<div class="col-md-6">
											<strong>Step 1</strong>: Create an Inbound Webhook in your Bitrix24 instance
											<ol>
													<li>Bitrix24 > Developer resources</li>
													<li>Common use cases > Other</li>
													<li>Select Inbound Webhook</li>
													<li>Name : <em>Dialplug_Inbound</em></li>
													<li>Select Assign permissions : </li>
													<ul>
															<li>Chat and Notifications</li>
															<li>Users (user)</li>
															<li>Telephony (telephony)</li>
															<li>Telephony (outbound calls) (call)</li>
															<li>CRM (crm)</li>
													</ul>
													<li>Save</li>
													<li>Copy Webhook to call REST API and paste it into Webhook URL Box</li>
											</ol>
												{{-- <strong>Step 2</strong>: Paste REST call example URL in WebhookURL box. --}}
												@if (isSuperUser() || $isValid)
														{!! CoralsForm::text('user_id', 'User ID', true) !!}
												 @endif
												{!! CoralsForm::text('webhook_url', 'WebHook URL', true, $bitrixtelephony->webhook_url, ['help_text' => 'eg: https://bitrix.yourdomain.com/rest/1/nasjn3y6ehbhas/']) !!}
										</div>
										<div class="col-md-6">
											<strong>Step 2</strong>: Create an Outbound Webhook in your Bitrix24 instance
											<ol>
													<li>Bitrix24 > Developer resources</li>
													<li>Common use cases > Other</li>
													<li>Select Outbound Webhook</li>
													<li>Handler Address : <br>
														@php
															echo "<span><em id='handler-address'>https://" . strtolower(user()->name) . '-' . user()->id . ".dialplug.com/admin/ajax.php?module=bitrix_integration&command=outcallfrombitrix</em>  <i class='fa fa-clipboard' onclick='copyToClipboard()' aria-hidden='true'></i><span>";
														@endphp
													</li>
													<li>Name : <em>Dialplug_Outbound</em></li>
													<li>Select Events : </li>
													<ul>
															<li>External phone call start (ONEXTERNALCALLSTART)</li>
													</ul>
													<li>Save</li>
													<li>Click on Create Config</li>
											</ol>
											{!! CoralsForm::formButtons() !!}
											@if ((isSuperUser()|| $isValid) && $bitrixtelephony->webhook_url)
											    {!! CoralsForm::text('phone_number', 'Phone Number') !!}
											@endif
										</div>
										{{-- Dialplug Fields End --}}

								</div>

								{!! CoralsForm::customFields($bitrixtelephony) !!}

								<div class="row">
										<div class="col-md-12">

										</div>
								</div>
								{!! CoralsForm::closeForm($bitrixtelephony) !!}
						@endcomponent
				</div>
		</div>
@endsection

@section('js')
@endsection
