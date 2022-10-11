@isset($product)
    <div class="row">
        @foreach($product->activePlans as $plan)
            <div class="col-md-6 col-lg-4 col-sm-12 margin-b-30">
                <div class="price-box {{ $plan->recommended?'best-plan':'' }}">
                    <div class="price-header">
                        @if($plan->free_plan)
                            <h1>{{  \Payments::currency(0.00) }}</h1>
                        @else
                        @php
                            if($plan->price == '0.00') {
                                echo "<h1 style='margin-bottom: 2rem;'>CUSTOM</h1>";
                            } else {
                        @endphp
                                <h1>{{  \Payments::currency($plan->price) }}
                        @php
                            }
                        @endphp
                            {{--<h1>{{  \Payments::currency($plan->price) }}--}}
                                @php
                                    if($plan->id == 15 || $plan->id == 20) {
                                        echo "<strong><sup style='margin-left: -0.8rem; top: -2rem'><span class='peroid' style='top: -2rem;'> + Call Usage</span></sup></strong>";
                                        echo "<span class='peroid' style='margin-left: -3rem'>$plan->cycle_caption</span></h1>";
                                    } else if($plan->price == '0.00') {
                                            echo "";
                                    } else {
	                                echo "<span class='peroid'>$plan->cycle_caption</span></h1>";
				    }
				@endphp
                        @endif
                        <h4>{{ $plan->name }}</h4>
                    </div>
		    {{--<span>{{ $plan->description }}</span>
		    <hr style="margin-top: 0;margin-bottom: 0;">--}}
                    <ul class="list-unstyled price-features">
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
                    <div class="price-footer">
                        {{-- @if(user() && user()->subscribed(null, $plan->id))
                            <a href="#"
                               class="btn btn-rounded {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}">
                                @lang('corals-saas::labels.current_package')
                            </a>
                            <br/>
                            {{ user()->currentSubscription(null, $plan->id)->ends_at?('ends at: '.format_date_time(user()->currentSubscription(null, $plan->id)->ends_at)):'' }}
                        @else
                            <a class="btn btn-rounded {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}"
                               href="{{ url('subscriptions/checkout/'.$plan->hashed_id) }}">
                                @lang('corals-saas::labels.subscribe_now')
                            </a>
                        @endif --}}
			@if(user() && user()->subscribed(null, $plan->id))
                          <a href="#"
                            class="btn btn-rounded {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}">
                              @lang('corals-saas::labels.current_package')
                          </a>
                          <br/>
                          {{ user()->currentSubscription(null, $plan->id)->ends_at?('ends at: '.format_date_time(user()->currentSubscription(null, $plan->id)->ends_at)):'' }}
                        @elseif (user())
                          <a class="btn btn-rounded {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}"
                            href="{{ url('subscriptions/checkout/'.$plan->hashed_id) }}">
                              @lang('corals-saas::labels.subscribe_now')
                          </a>
                        @else
                          <a class="btn btn-rounded {{ $plan->recommended?'btn-white-border':'btn-dark-border' }}"
                            href="{{ url('register') }}">
                              @lang('Register Now')
                          </a>
                        @endif
                    </div>
                </div>
            </div><!--/col-->
        @endforeach
    </div>
@else
    <p class="text-center text-danger"><strong>@lang('corals-saas::labels.product_cannot_found')</strong></p>
@endisset
