<div class="listar-innerbanner">
    <div class="listar-parallaxcolor listar-innerbannerparallaxcolor" data-scrollax-parent="true">
        <div class="bg par-elem "
             data-bg="{{ isset($item) ? (\CMS::getContentFeaturedImage($item)??\Theme::url('/images/bg/header-bg.jpg')):\Theme::url('/images/bg/header-bg.jpg')  }}"
             data-scrollax="properties: { translateY: '30%' }"></div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="listar-innerbannercontent">
                        @if(isset($content))
                            {!! $content !!}
                        @else
                            <div class="listar-pagetitle">
                                <h1>{!! optional($item)->title  !!}</h1>
                            </div>
                        @endif
                        <ol class="listar-breadcrumb">
                            <li class="listar-active"><p class="custom-size">{!! $item->title  !!}</p></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>