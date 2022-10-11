<div id="dashboard-options-menu" class="side-menu dashboard left closed">
    <svg class="svg-plus">
        <use xlink:href="#svg-plus"></use>
    </svg>
    <div class="side-menu-header">
        <div class="user-quickview">
            @auth
                <a href="{{ url('profile') }}">
                    <div class="outer-ring">
                        <div class="inner-ring"></div>
                        <figure class="user-avatar">
                            <img src="{{user()->picture_thumb }}" alt="{{ auth()->user()->name }}">
                        </figure>
                    </div>
                </a>
            @endauth
            @auth
                <p class="user-name">{{user()->full_name}}</p>
                @php
                    $transactionSummary = \Store::getTransactionsSummary();
                @endphp
                <p class="user-money">{!! Payments::admin_currency( $transactionSummary['balance']) !!}

                    </p>

                @endauth
        </div>
    </div>
    @if(\Settings::get('marketplace_checkout_points_redeem_enable',true) )
        @php $points= \Referral::getPointsBalance(user()) @endphp
        <p class="side-menu-title">
            <a href="{{url('referral/my-referrals')}}"> @lang('admin-dragon::labels.partial.points_spend',['points'=>$points])</a>
        </p>
    @endif

    <ul class="dropdown dark hover-effect interactive">
        @auth
            @php $vendor_role = \Settings::get('marketplace_general_vendor_role', '') @endphp
            @if ($vendor_role  && !user()->hasRole($vendor_role))
                {!! '<a href="' . url('marketplace/store/enroll') . '" class="button  secondary spaced">'.trans('Marketplace::labels.store.become_a_seller').'</a>' !!}
            @endif
        @endauth

        <li class="dropdown-item {{ \Request::is('dashboard')?'active':'' }}">
            <a href="{{ url('dashboard') }}">
                @lang('admin-dragon::labels.partial.dashboard')
            </a>
        </li>
        @include('partials.menu.menu_item', ['menus'=>Menus::getMenu('sidebar','active') ])
    </ul>
    <a href="{{url('logout')}}"
       data-action="logout" class="button  secondary">@lang('admin-dragon::labels.partial.logout')</a>
</div>