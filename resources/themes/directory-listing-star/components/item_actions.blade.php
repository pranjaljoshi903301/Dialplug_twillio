@if(!empty($actions))
    <div class="hover-ul">
        <a href="#"
           class="listar-helpful btn btn-sm flat-btn" >
            @lang('corals-directory-listing-star::labels.actions')
        </a>
        <ul class="sub-menu-action">
            @foreach($actions as $action)
                <li>
                    @php
                        $dataAttribute = [];
                            foreach($action['data']??[] as $key=>$data){
                            $dataAttribute['data-'.$key]=$data;
                            }
                    @endphp
                    <a target="{{ $action['target']??'_self' }}" href="{{ $action['href'] }}"
                       style="padding: 5px 15px "
                       class="{{ $action['class'] ?? '' }}" {!! \Html::attributes($dataAttribute) !!} >
                        <i class="{{ $action['icon']?? '' }}"></i> {!! $action['label'] !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
