@extends('layouts.master')

@section('editable_content')
    @php \Actions::do_action('pre_content', null, null) @endphp
    <div class="container padding-bottom-3x mb-2">
        <div class="card text-center">
            <div class="card-body padding-top-2x">
                <h3 class="card-title">@lang('corals-ecommerce-mimity::labels.template.checkout_success.success')</h3>
                <p class="card-text">@lang('corals-ecommerce-mimity::labels.template.checkout_success.order_has_been_placed')</p>
                <div class="padding-top-1x padding-bottom-1x">

                    @auth
                        <a href="{{ url('e-commerce/orders/my') }}"
                           class="btn btn-success">@lang('corals-ecommerce-mimity::labels.template.checkout_success.go_my_order')</a>
                    @else
                        <h5 class="text text-info">@lang('corals-ecommerce-mimity::labels.template.checkout_success.order_guest_email_sent',['email'=>$order->billing['billing_address']['email']])</h5>

                    @endauth
                </div>
            </div>
        </div>
    </div>
@stop