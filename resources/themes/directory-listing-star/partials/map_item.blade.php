<div class="listar-infoholder">
    <figure class="listar-featuredimg"><img src="{{$locationImg}}" alt="Silvana Hoggatt"></figure>
    <div class="listar-companycontent"><h3><a href="{{$locationURL}}">{{$locationTitle}}</a>
            <i class="icon-checkmark listar-postverified listar-themetooltip" data-toggle="tooltip"
               data-placement="top" title="" data-original-title="Verified"></i></h3>
        <div class="listar-review">
            @include('partials.components.rating',['rating'=> $locationStarRating,'rating_count'=>null])
            <em>({{$locationPhone}})</em>
        </div>
        <div class="listar-themepostfoot-custom"><a class="listar-custom" href="javascript:void(0);"><i
                        class="icon-icons74 custom-map"></i><em class="custom-map">{{$locationAddress}}</em></a>
            <div class="listar-postbtns" id="custom-padding">
                @foreach($locationCategory as $category)
                    <a class="custom-map" href="{{ url('listings?category='.$category) }}">
                        {!! $category !!}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>