<div class="row">
    <div class="col-md-12">
        @component('components.box')
            {!! CoralsForm::openForm($order,['url'=>url($resource_url.'/'.$order->hashed_id.'/shipping'), 'method'=>'PUT','class'=>'ajax-form','data-page_action'=>"closeModal","data-table"=>'.dataTableBuilder']) !!}
            <div class="row">
                <div class="col-md-6">
                    {!! CoralsForm::select('shipping[status]','Ecommerce::attributes.order.shipping_status', $order_statuses ,false, $order->shipping['status'] ?? '',['class'=>'']) !!}

                </div>
                <div class="col-md-6">
                    {!! CoralsForm::text('shipping[tracking_number]','Ecommerce::attributes.order.shipping_track', false, $order->shipping['tracking_number'] ?? '',['class'=>'']) !!}

                </div>

            </div>
            <div class="row">

                <div class="col-md-12">
                    {!! CoralsForm::text('shipping[label_url]','Ecommerce::attributes.order.shipping_label', false, $order->shipping['label_url'] ?? '',['class'=>'']) !!}
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
