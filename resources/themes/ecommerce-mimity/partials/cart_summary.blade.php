<div class="modal-header">
    <h5 class="modal-title" id="cartModalLabel">@lang('corals-ecommerce-mimity::labels.template.cart.product')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if(\ShoppingCart::count() > 0)

        @foreach($items = \ShoppingCart::getItems() as $item)
            <div class="media"  >
                <a href="{{ url('shop', [$item->id->product->slug]) }}"><img src="{{ $item->id->image }}" width="50"
                                                                             height="50"
                                                                             alt="Products"></a>
                <div class="media-body">
                    <a href="{{ url('shop', [$item->id->product->slug]) }}">
                        {!! $item->id->product->name !!}</a>
                    <div class="input-spinner input-spinner-sm">
                        <input class="form-control form-control-sm cart-quantity" min="1" max="999"
                               type="number"
                               name="quantity"
                               data-id="{{ $item->rowId }}"
                               {!! $item->id->allowed_quantity>0?('max="'.$item->id->allowed_quantity.'"'):'' !!} value="{{ $item->qty }}">
                        @if($item->id->allowed_quantity != 1)
                            <form action="{{ url('cart/quantity', [$item->getHash()]) }}" method="POST"
                                  class="ajax-form form-inline" data-page_action="updateCart">
                                {{ csrf_field() }}
                                <div class="btn-group-vertical">
                                    <a href="{{ url('cart/quantity', [$item->getHash()]) }}"
                                       data-style="zoom-in"
                                       title="Add One" data-action="post" data-page_action="updateCart"
                                       data-request_data='@json(["action"=>"increaseQuantity"])'
                                       class="btn btn-light">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="btn btn-light" href="{{ url('cart/quantity', [$item->getHash()]) }}"
                                       title="Remove One" data-action="post" data-style="zoom-in"
                                       data-request_data='@json(["action"=>"decreaseQuantity"])'
                                       data-page_action="updateCart">
                                        <i class="fa fa-chevron-down"></i>
                                    </a>
                                </div>
                            </form>
                        @else
                            <input class="form-control form-control-sm"
                                   value="1"
                                   disabled/>
                        @endif
                    </div>
                    x <span class="price"
                            id="item-total-{{$item->getHash()}}">
                    {{ \Payments::currency($item->qty * $item->price) }}
                </span>

                    <a class="close" href="{{ url('cart/quantity', [$item->getHash()]) }}"
                       data-action="post" data-style="zoom-in"
                       data-request_data='@json(["action"=>"removeItem"])'
                       data-page_action="updateCart"
                       data-toggle="tooltip"
                       title="Remove item">
                        <i class="icon-x"></i>
                        <i class="fa fa-trash-o"></i>
                    </a>
                </div>
            </div>
        @endforeach
        <div class="modal-footer">
            <div class="box-total">
                <h4>@lang('corals-ecommerce-mimity::labels.template.cart.subtotal') <span class="price"
                                                                                          id="total">{{ ShoppingCart::subTotal() }}</span>
                </h4>
                <a href="{{ url('cart') }}"
                   class="btn btn-success">@lang('corals-ecommerce-mimity::labels.template.cart.view_cart')</a>
            </div>
        </div>
    @else

        <b>@lang('corals-ecommerce-mimity::labels.template.cart.have_no_item_cart')</b>
    @endif
</div>
