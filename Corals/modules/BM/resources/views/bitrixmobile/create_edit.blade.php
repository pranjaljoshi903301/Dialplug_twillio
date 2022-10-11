@extends('layouts.crud.create_edit')

@section('css')
@endsection

@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot
        @slot('breadcrumb')
            {{ Breadcrumbs::render('bm_bitrixmobile_create_edit') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                {!! CoralsForm::openForm($bitrixmobile) !!}
                <div class="row">

                    {{-- BM Fields End --}}
                    <div class="col-md-6">
                        @if (isSuperUser())
                            {!! CoralsForm::text('user_id', 'User ID', true) !!}
                         @endif
                        {!! CoralsForm::text('mobile_number', 'Mobile Number',true, $bitrixmobile->mobile_number, ['help_text' => 'eg: +918888888888']) !!}
                        {!! CoralsForm::text('webhook_url', 'WebHook URL', true, $bitrixmobile->webhook_url, ['help_text' => 'eg: https://bitrix.yourdomain.com/rest/1/nasjn3y6ehbhas/']) !!}
                    </div>
                    <div class="col-md-6">
                    </div>
                    {{-- BM Fields End --}}
                    
                </div>

                {!! CoralsForm::customFields($bitrixmobile) !!}

                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>
                {!! CoralsForm::closeForm($bitrixmobile) !!}
            @endcomponent
        </div>
    </div>
@endsection

@section('js')
@endsection