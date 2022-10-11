@isset($product)
    <div class="d-flex justify-content-around">
        @foreach($product->activePlans as $plan)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="card-title">
                            {{ $plan->name }}
                        </h3>
                        <h4 class="card-subtitle mb-2 text-primary">
                            @if($plan->free_plan)
                                {{  \Payments::currency(0.00) }}
                            @else
                                {{  \Payments::currency($plan->price) }} {!! $plan->cycle_caption  !!}
                            @endif
                        </h4>
                        @if($plan->recommended)
                            <h6 class="card-subtitle mb-2 text-muted">
                                <span class="badge badge-pill badge-dark">Recommended</span>
                            </h6>
                        @endif
                        <div class="card-text my-3">
                            <ul class="list-unstyled">
                                @foreach($product->activeFeatures as $feature)
                                    @if($plan_feature = $plan->features()->where('feature_id',$feature->id)->first())
                                        <li>
                                            @if(!empty($plan_feature->pivot->plan_caption))
                                                {{ $plan_feature->pivot->plan_caption }}
                                            @else
                                                @if($feature->type=="boolean")
                                                    @if($plan_feature->pivot->value)
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                @else
                                                    {{$plan_feature->pivot->value }} {{$feature->unit }}
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
                        </div>
                        @if(user() && user()->subscribed(null, $plan->id))
                            <a href="#"
                               class="btn btn-info {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}">
                                @lang('corals-classified-master::labels.pricing.current_package')
                            </a>
                            <br/>
                            {{ user()->currentSubscription(null, $plan->id)->ends_at?('ends at: '.format_date_time(user()->currentSubscription(null, $plan->id)->ends_at)):'' }}
                        @else
                            <a class="btn btn-success {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}"
                               href="{{ url('subscriptions/checkout/'.$plan->hashed_id) }}">
                                @lang('corals-classified-master::labels.pricing.subscribe_now')
                            </a>
                        @endif
                    </div>
                </div>
            </div><!--/col-->
        @endforeach
    </div>
@else
    <p class="text-center text-danger">
        <strong>@lang('corals-classified-master::labels.pricing.product_cannot_found')</strong></p>
@endisset
