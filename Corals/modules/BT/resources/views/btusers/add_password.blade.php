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
            {{ 'Add Password' }}
        @endslot
        @slot('breadcrumb')
            {{-- {{ Breadcrumbs::render('bt_user_create_edit') }} --}}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                <form method="POST" action="{{ url('') }}/bt_users/{{ $bitrixtelephony->hashed_id }}/storePassword">
                    {{ csrf_field() }}
                    <div class="row">
                        {{-- Dialplug Fields --}}
                        <div class="col-md-12">
                            {!! CoralsForm::password('password', 'Password', true) !!}
                            {!! CoralsForm::password('confirm_password', 'Confirm Password', true) !!}
                        </div>
                        {{-- Dialplug Fields End --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group  text-right">
                                <button class="btn btn-success ladda-button" type="submit" data-style="expand-right">
                                    <span class="ladda-label"><i class="fa fa-save"></i> Add Password</span>
                                    <span class="ladda-spinner"></span>
                                </button>&nbsp;&nbsp;
                                <a class="btn btn-warning" href="{{ url('') }}/bt_users/"><i class="fa fa-times"></i>
                                    Cancel</a>
                            </div>

                            {{-- {!! CoralsForm::formButtons() !!}
                            --}}
                        </div>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
@endsection

@section('js')
@endsection

