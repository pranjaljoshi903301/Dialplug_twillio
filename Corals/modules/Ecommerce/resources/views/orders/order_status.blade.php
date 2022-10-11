<div class="row">
    <div class="col-md-12">
        @component('components.box')
            {!! CoralsForm::openForm($order,['url'=>url($resource_url.'/'.$order->hashed_id.'/status'), 'method'=>'PUT','class'=>'ajax-form','data-page_action'=>"closeModal","data-table"=>'.dataTableBuilder']) !!}
            <div class="row">
                <div class="col-md-12">
                    {!! CoralsForm::select('status','Ecommerce::attributes.order.status_order', $order_statuses ,true) !!}
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! CoralsForm::formButtons(trans('Corals::labels.save',['title' => $title_singular]), [], ['show_cancel' => false])  !!}
                </div>
            </div>
            {!! CoralsForm::closeForm($order) !!}
        @endcomponent
    </div>
</div>
