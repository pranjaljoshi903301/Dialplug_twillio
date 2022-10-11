@if($status['products_count']['limit_reached'] || $status['feature_products_count']['limit_reached'])
    <div class="alert alert-warning" role="alert">
        <strong>Warning!</strong> <br/>
        @if($status['products_count']['limit_reached'])
            {!! $status['products_count']['message'] !!}
            <br/>
        @endif
        @if($status['feature_products_count']['limit_reached'])
            {!! $status['feature_products_count']['message'] !!}
            <br/>
        @endif
        @lang('Classified::messages.upgrade_or_contact_us', ['contact_us'=>url('contact-us')])
    </div>
@endif