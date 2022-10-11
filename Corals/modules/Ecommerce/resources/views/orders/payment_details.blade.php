<div class="row">
    <div class="col-md-12">
        @component('components.box')
            {!! CoralsForm::openForm($order,['url'=>url($resource_url.'/'.$order->hashed_id.'/payment'), 'method'=>'PUT','class'=>'ajax-form','data-page_action'=>"closeModal","data-table"=>'.dataTableBuilder']) !!}
            <div class="row">
                <div class="col-md-6">
                    {!! CoralsForm::select('billing[payment_status]','Ecommerce::attributes.order.payment_status', $payment_statuses , false, $order->billing['payment_status'] ?? '',['class'=>'']) !!}
                </div>
                <div class="col-md-6">
                    {!! CoralsForm::text('billing[payment_reference]','Ecommerce::attributes.order.payment_reference', false, $order->billing['payment_reference'] ?? '',['class'=>'']) !!}
                    {!! CoralsForm::text('billing[gateway]','Ecommerce::attributes.order.payment_method', false, $order->billing['gateway'] ?? '',['class'=>'']) !!}
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
