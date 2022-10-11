<div class="page-title">
    <div class="container clearfix">
        <div class="float-left float-xs-none">
            @if(isset($content))
                {!! $content !!}
            @elseif(isset($item))
                <h1 class="opacity-40 center">{{$item->title}}</h1>
            @elseif(isset($title))
                <h1>{{$title}}</h1>
            @endif
            @if(isset($location))
                <h4 class="location">
                    <a href="{{url('products?location='.$product->getLocationSlug()) }}">{!! $location ?? null !!}</a>
                </h4>
            @endif
        </div>
        @if(isset($price))
            <div class="float-right float-xs-none price">
                <div class="number">{{$price ?? null}}</div>
            </div>
        @endif
    </div>
    <!--end container-->
</div>
