@php \Assets::add(asset(\Theme::url('css/pricingTable.min.css'))) @endphp
@section('css')
    <style>
        .price-box.best-plan .price-header,
        .price-box.best-plan .price-footer {
            background: #0e2d7b;
        }

        .btn-white-border {
            background: transparent;
            color: #fff;
            border: 2px solid #7dba00;
        }

        .btn-white-border:hover {
            background-color: #7dba00;
            color: #0e2d7b;
        }

    </style>
@endsection
@isset($product)
    @if (Request::path() == 'subscriptions/select')
        <a href="{{ url()->current() }}/{{ $product->hashed_id }}">Click Here &rarr;</a>
    @else
        <div class="row">
            @foreach ($product->activePlans as $plan)
                <div class="col-md-4 margin-b-30">
                    <div class="price-box {{ $plan->recommended ? 'best-plan' : '' }}">
                        <div class="price-header">
                            @if ($plan->free_plan || $plan->price == '0.00')
                                <h1>Custom</h1>
                            @elseif($plan->id == 15 || $plan->id == 20)
                                <h1>{{ \Payments::currency($plan->price) }}
                                    <strong><sup style="margin-left: -0.8rem; top: -2rem"><span class="peroid"
                                                style="top: -2rem;"> + Call Usage</span></sup></strong>
                                    <span class="peroid" style="margin-left: -6rem;">{!! $plan->cycle_caption !!}</span>
                                </h1>
                            @else
                                <h1>{{ \Payments::currency($plan->price) }}
                                    <span class="peroid">{!! $plan->cycle_caption !!}</span>
                                </h1>
                            @endif
                            <h4>{{ $plan->name }}</h4>
                        </div>
                        <ul class="list-unstyled price-features">
                            @foreach ($product->activeFeatures as $feature)
                                @if ($plan_feature = $plan
            ->features()
            ->where('feature_id', $feature->id)
            ->first())
                                    <li>
                                        @if (!empty($plan_feature->pivot->plan_caption))
                                            {{ $plan_feature->pivot->plan_caption }}
                                        @else
                                            @if ($feature->type == 'boolean')
                                                @if ($plan_feature->pivot->value)
                                                    <i class="fa fa-check"></i>
                                                @endif
                                            @else
                                                {{ $plan_feature->pivot->value }} {{ $feature->unit }}
                                            @endif
                                            {{ $feature->caption }}
                                        @endif
                                    </li>
                                @else
                                    <li>
                                        <i class="fa fa-times"></i>
                                        {{ $feature->caption }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="price-footer">
                            @if (user() && user()->subscribed(null, $plan->id))
                                <a href="#"
                                    class="btn btn-rounded {{ $plan->recommended ? 'btn-white-border' : 'btn-dark-border' }}">
                                    @lang('corals-admin::labels.pricing.current_package')
                                </a>
                                <br />
                                <span
                                    style="color: gray">{{ user()->currentSubscription(null, $plan->id)->ends_at ? 'ends at: ' . format_date_time(user()->currentSubscription(null, $plan->id)->ends_at) : '' }}</span>
                            @else
                                <a class="btn btn-rounded {{ $plan->recommended ? 'btn-white-border' : 'btn-dark-border' }}"
                                    href="{{ url('subscriptions/checkout/' . $plan->hashed_id) }}">
                                    @lang('corals-admin::labels.pricing.subscribe_now')
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!--/col-->
            @endforeach
        </div>
    @endif
@else
    <p class="text-center text-danger"><strong>@lang('corals-admin::labels.pricing.product_can_not_found')</strong></p>
@endisset

