{!! Form::open( ['url' => url($urlPrefix.'checkout/step/cart-details'),'method'=>'POST', 'class'=>'ajax-form','id'=>'checkoutForm']) !!}
@isset($order)
    <input type="hidden" name="order" value="{{ $order->hashed_id }}">
@endisset
<div class="row">
    <div class="col-md-12 shopping-cart">
        <div class="table-responsive">
            <table class="table color-table info-table table table-hover table-striped table-condensed">
                <thead>
                <tr>
                    <th class="table-image"></th>
                    <th>@lang('Ecommerce::labels.cart.product')</th>
                    <th>@lang('Ecommerce::labels.order.item.type')</th>
                    <th>@lang('Ecommerce::labels.cart.quantity')</th>
                    <th>@lang('Ecommerce::labels.cart.price')</th>
                </tr>
                </thead>
                <tbody>
                @foreach (ShoppingCart::getItems() as $item)
                    <tr id="item-{{$item->getHash()}}" class="product-item m-t-0">
                        <td class="table-image">
                            <a href="{{ url('shop', [$item->id->product->slug]) }}" target="_blank">
                                <img src="{{ $item->id->image }}" alt="SKU Image"
                                     class="img-rounded img-responsive" width="50"></a>
                        </td>
                        <td>
                            <h5 class="product-title">
                                <a target="_blank"
                                   href="{{url('shop', [$item->id->product->slug])}}">{{ $item->id->product->name }}
                                    [{{ $item->id->code }}]</a>
                            </h5>
                            {!! $item->id->present('options')  !!}

                            {!! \Actions::do_action('post_cart_item_details', $item) !!}
                        </td>
                        <td><b>@lang('Ecommerce::labels.order.product')</b></td>
                        <td><b>{{ $item->qty }}</b></td>
                        <td id="item-total-{{$item->getHash()}}">{{ \Payments::currency($item->qty * $item->price) }}</td>
                    </tr>
                @endforeach
                @foreach (ShoppingCart::getFees() as $fee_name => $fee)
                    <tr i class="product-item m-t-0">
                        <td>

                        </td>
                        <td>
                            <h5 class="product-title">
                                <a target="_blank"
                                   href="#">{{ $fee_name }}
                                </a>
                            </h5>

                        </td>
                        <td><b>@lang('Ecommerce::labels.order.'.strtolower($fee->type))</b></td>

                        <td><b>{{ $item->options['qty'] ?? '' }}</b></td>
                        <td>{{ \Payments::currency($fee->amount ) }}</td>
                    </tr>
                @endforeach
                @foreach (ShoppingCart::getCoupons() as $coupon_name => $coupon)
                    <tr i class="product-item m-t-0">
                        <td>

                        </td>
                        <td>
                            <h5 class="product-title">
                                <a target="_blank"
                                   href="#">{{ $coupon_name }}
                                </a>
                            </h5>

                        </td>
                        <td><b>@lang('Ecommerce::labels.order.discount')</b></td>

                        <td><b>1</b></td>
                        <td>{{ $coupon->displayValue() }}</td>
                    </tr>
                @endforeach
                @if(\ShoppingCart::taxTotal(false))
                <tr>
                    <td class=""></td>
                    <td></td>
                    <td></td>
                    <td class="small-caps table-bg"
                        style="text-align: right">@lang('Ecommerce::labels.checkout.tax')</td>
                    <td id="tax_total">{{ \ShoppingCart::taxTotal() }}</td>
                </tr>
                @endif
                <tr>
                    <td class="table-image"></td>
                    <td></td>
                    <td></td>
                    <td class="small-caps table-bg"
                        style="text-align: right">@lang('Ecommerce::labels.cart.sub_total')</td>
                    <td id="sub_total">{{ ShoppingCart::total() }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>
            @lang('Ecommerce::labels.cart.have_coupon')
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {!! CoralsForm::text('coupon_code','Ecommerce::attributes.coupon.coupon_code',false) !!}
    </div>
</div>
<style>
    .sw-toolbar-bottom{
        display: block;
    }
</style>

{!! Form::close() !!}

<script type="application/javascript">



    $(document).ready(function () {
        $('.sw-toolbar-bottom').show();

    });
</script>