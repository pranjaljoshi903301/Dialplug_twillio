@if(\ShoppingCart::count() > 0)
    <div class="widget-cart">
        @foreach($items = \ShoppingCart::getItems() as $item)
            <div   class="entry">
                <div class="entry-thumb">
                    <a href="{{ url('shop', [$item->id->product->slug]) }}"><img src="{{ $item->id->image }}"
                                                                                 alt="Product">
                    </a>
                </div>
                <div class="entry-content">
                    <h4 class="entry-title"><a
                                href="{{ url('shop', [$item->id->product->slug]) }}">{!! $item->id->product->name !!}</a>
                    </h4><span class="entry-meta">
               @lang('corals-ecommerce-ultimate::labels.template.cart.price')
                : {{ \Payments::currency($item->qty * $item->price) }}</span>
                    <span class="entry-meta">
                @lang('corals-ecommerce-ultimate::labels.template.cart.quantity') : {{ $item->qty }}</span>
                </div>
                <div class="entry-delete">
                    <a href="{{ url('cart/quantity', [$item->getHash()]) }}"
                       data-action="post" data-style="zoom-in"
                       data-request_data='@json(["action"=>"removeItem"])'
                       data-page_action="updateCart"
                       data-toggle="tooltip"
                       title="Remove item">
                        <i class="icon-x"></i>
                    </a>
                </div>
            </div>
        @endforeach
        <div class="text-right">
            <p class="text-gray-dark py-2 mb-0"><span class="text-muted"
                                                      id="cart-header-total">
            @lang('corals-ecommerce-ultimate::labels.template.cart.subtotal') : </span>{{ ShoppingCart::subTotal() }}
            </p>
        </div>
        <div class="d-flex">
            <div class="pr-2 w-50"><a class="btn btn-secondary btn-sm btn-block mb-0" href="{{ url('cart') }}">
                    @lang('corals-ecommerce-ultimate::labels.template.cart.view_cart')
                </a>
            </div>
            <div class="pl-2 w-50"><a class="btn btn-primary btn-sm btn-block mb-0" href="{{ url('checkout') }}">
                    @lang('corals-ecommerce-ultimate::labels.template.cart.checkout')
                </a>
            </div>
        </div>
    </div>
@else

    <b>@lang('corals-ecommerce-ultimate::labels.template.cart.have_no_item_cart')</b>
@endif