@extends('layouts.crud.create_edit')

@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot

        @slot('breadcrumb')
            {{ Breadcrumbs::render('ecommerce_order_create_edit') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    {!! CoralsForm::openForm($order,[]) !!}
    <div class="row">
        <div class="col-md-4">
            @component('components.box',['box_title'=>trans('Ecommerce::labels.order.order_details')])
                {!! CoralsForm::text('order_number','Ecommerce::attributes.order.order_number',true, $order->order_number,['readonly']) !!}

                {!! CoralsForm::select('status', 'Corals::attributes.status', trans(config('ecommerce.models.order.statuses')), true) !!}

                {!! CoralsForm::select('currency', 'Ecommerce::attributes.order.currency', \Payments::getActiveCurrenciesList() , true) !!}
            @endcomponent
        </div>
        <div class="col-md-8">
            @component('components.box',['box_title'=>trans('Ecommerce::labels.order.invoice_details')])
                <div class="row">
                    <div class="col-md-6">
                        {!! CoralsForm::text('invoice[code]','Payment::attributes.invoice.invoice_code',true) !!}
                        {!! CoralsForm::date('invoice[invoice_date]', 'Payment::attributes.invoice.invoice_date',true) !!}
                        {!! CoralsForm::date('invoice[due_date]','Payment::attributes.invoice.due_date', true) !!}
                    </div>
                    <div class="col-md-6">
                        {!! CoralsForm::radio('invoice[status]','Corals::attributes.status',true, get_array_key_translation(config('payment_common.models.invoice.statuses'))) !!}

                        {!! CoralsForm::select('invoice[user_id]','Ecommerce::attributes.order.user_id', [], true, null,
                                  ['class'=>'select2-ajax','data'=>[
                                  'model'=>\Corals\User\Models\User::class,
                                  'columns'=> json_encode(['name','last_name', 'email']),
                                  'selected'=>json_encode([$order->user_id]),
                                  'where'=>json_encode([]),
                                  ]],'select2') !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    @component('components.box')
        <style>
            .column-220{
                width: 220px;
            }
            .column-100{
                width: 100px;
            }
        </style>
        <div class="row" id="items-div" data-url="{{ url('e-commerce/orders/calculate') }}" data-method="post"
             data-page_action="setCalculations">

            {!! Form::hidden('items_currency', $order->currency,['id'=>'items_currency']) !!}

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped text-center" id="order-items-table">
                        <thead>
                        <tr>
                            <th class="column-220">@lang('Ecommerce::labels.order.item.item')</th>
                            <th class="column-100">@lang('Ecommerce::labels.order.item.type')</th>
                            <th class="column-100">@lang('Ecommerce::labels.order.item.unit_price')</th>
                            <th class="column-100">@lang('Ecommerce::labels.order.item.quantity')</th>
                            <th class="column-220">@lang('Ecommerce::labels.order.item.description')</th>
                            <th class="column-220">@lang('Ecommerce::labels.order.item.tax')</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items??[] as $itemTypes)
                            @foreach($itemTypes as $item)
                                <tr data-item_key="{{ $item['type'].'_'.$item['id'] }}"
                                    data-item_type="{{ $item['type'] }}">
                                    <td>
                                        <span>{{ $item['name'] }}</span>
                                        <input type="hidden" name="items[{{ $item['type'] }}][{{ $item['id'] }}][name]"
                                               value="{{ $item['name'] }}">
                                    </td>
                                    <td>
                                        <span>{{ $item['type'] }}</span>
                                        <input type="hidden" name="items[{{ $item['type'] }}][{{ $item['id'] }}][type]"
                                               value="{{ $item['type'] }}">
                                    </td>
                                    <td>
                                        {!! CoralsForm::number("items[{$item['type']}][{$item['id']}][unit_price]",'',false, $item['unit_price'],['class'=>'unit_price', 'min'=>"0", 'step'=>"0.01"]) !!}
                                    </td>
                                    <td>
                                        {!! CoralsForm::number("items[{$item['type']}][{$item['id']}][quantity]",'',false, $item['quantity'],['class'=>'item-quantity', 'min'=>"0", 'step'=>"1"]) !!}
                                    </td>
                                    <td>
                                        {!! CoralsForm::text("items[{$item['type']}][{$item['id']}][description]",'',false,$item['description']) !!}
                                    </td>
                                    <td>
                                        {!! CoralsForm::select('items['.$item['type'].']['. $item['id'].'][taxes][]', '', $taxesList,false,$item['tax_ids'],
                                        ['class'=>'','multiple'=>true],'select2') !!}
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-danger remove-btn" type="button"><i
                                                        class="fa fa-trash-o"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr id="empty-items-row" style="{{ $order->exists?'display:none;':'' }}">
                            <td colspan="7">@lang('Ecommerce::labels.order.empty_items')</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-sm btn-primary" onclick="addNewItemModal('product')"
                                type="button">
                            <i class="fa fa-plus"></i> @lang('Ecommerce::labels.order.add_product')
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="addNewItemModal('fee')" type="button">
                            <i class="fa fa-plus"></i> @lang('Ecommerce::labels.order.add_fee')
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="addNewItemModal('shipping')"
                                type="button">
                            <i class="fa fa-plus"></i> @lang('Ecommerce::labels.order.add_shipping')
                        </button>
                        <div class="form-group">
                            <span data-name="items"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table text-right">
                                <tbody>
                                <tr>
                                    <td colspan="3"><h4>@lang('Ecommerce::labels.order.subtotal')</h4></td>
                                    <td style="width: 100px;"><h4 id="items-subtotal">0</h4></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><h4>@lang('Ecommerce::labels.order.tax_total')</h4></td>
                                    <td style="width: 100px;"><h4 id="items-tax_total">0</h4></td>
                                </tr>
                                <tr>
                                    <td><h4>@lang('Ecommerce::labels.order.discount')</h4></td>
                                    <td style="width: 170px">
                                        {{ CoralsForm::text('discount_properties[code]','', false, $discountItem['code'],['placeholder'=>'Ecommerce::labels.order.discount_name']) }}
                                    </td>
                                    <td style="width: 170px">
                                        {!! CoralsForm::select('discount_properties[type]','',trans('Ecommerce::labels.order.discount_options'),false, $discountItem['type']) !!}
                                    </td>
                                    <td>{{ CoralsForm::number('discount_properties[amount]','', false, $discountItem['amount'],['step'=>0.01,'min'=>0]) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">{!! CoralsForm::button('Ecommerce::labels.order.recalculate',['class'=>'btn btn-sm btn-info','onClick'=>'recalculateOrder()']) !!}</td>
                                    <td><h4>@lang('Ecommerce::labels.order.total')</h4></td>
                                    <td>
                                        <h4 id="items-total">{{ $order->exists?\Payments::currency_convert($order->amount,null,$order->currency,true):0 }}</h4>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! CoralsForm::textarea('invoice[terms]','Payment::attributes.invoice.terms',false, null,['class'=>'ckeditor-simple']) !!}
            </div>
            <div class="col-md-6">
                {!! CoralsForm::textarea('invoice[description]','Payment::attributes.invoice.description',false, null,['class'=>'ckeditor-simple']) !!}
            </div>
        </div>
        {!! CoralsForm::customFields($order, 'col-md-12') !!}
        <div class="row">
            <div class="col-md-12">
                {!! CoralsForm::formButtons('', [], [], [
                'send_invoice'=>[
                        'label'=>'Ecommerce::labels.order.save_send',
                        'type'=>'submit',
                        'attributes'=>[
                            'name'=>'send_invoice',
                            'value'=>'1',
                            'class' => 'btn btn-primary m-r-5 mr-1'
                        ],
                    ]
                ]) !!}
            </div>
        </div>
    @endcomponent
    {!! CoralsForm::closeForm($order) !!}
    @component('components.modal', ['id'=>'add-item-modal', 'header' => trans('Ecommerce::labels.order.add_item_modal_title')])
        <div class="row">
            <div class="col-md-12">
                <div id="add-new-items-modal-fields-fee" style="display: none;">
                    {!! CoralsForm::text('selected_item', 'Ecommerce::labels.order.fee', true,null,['class'=>'selected_item','id'=>'selected_item-fee']) !!}
                    <div class="text-right">
                        {!! CoralsForm::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-success','onClick'=>'addNewItem(\'fee\')']) !!}
                    </div>
                </div>
                <div id="add-new-items-modal-fields-shipping" style="display: none;">
                    {!! CoralsForm::select('selected_item','Ecommerce::labels.order.shipping', [], true, null,
                           ['class'=>'selected_item select2-ajax',
                           'id'=>'selected_item-shipping',
                           'data'=>[
                           'model'=>\Corals\Modules\Ecommerce\Models\Shipping::class,
                           'columns'=> json_encode(['name']),
                           'selected'=>json_encode([]),
                           'select2_parent'=>'#add-item-modal',
                           'result_mapper'=>'array_hashids_encode',
                           ]],'select2') !!}
                    <div class="text-right">
                        {!! CoralsForm::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-success','onClick'=>'addNewItem(\'shipping\')']) !!}
                    </div>
                </div>
                <div id="add-new-items-modal-fields-product" style="display: none;">
                    {!! CoralsForm::select('selected_item','Ecommerce::labels.order.product', [], true, null,
                            ['class'=>'selected_item select2-ajax',
                            'id'=>'selected_item-product',
                            'data'=>[
                            'model'=>\Corals\Modules\Ecommerce\Models\SKU::class,
                            'columns'=> json_encode(['ecommerce_products.name', 'code']),
                            'selected'=>json_encode([]),
                            'join' =>json_encode([
                            'table'=>'ecommerce_products',
                            'type'=>'leftJoin',
                            'on' =>['ecommerce_products.id','ecommerce_sku.product_id']
                            ]),
                            'select2_parent'=>'#add-item-modal',
                            'result_mapper'=>'array_hashids_encode',
                            ]],'select2') !!}
                    <div class="text-right">
                        {!! CoralsForm::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-success','onClick'=>'addNewItem(\'product\')']) !!}
                    </div>
                </div>
            </div>
        </div>
    @endcomponent
@endsection

@section('js')
    @parent
    <script type="text/javascript">
        function getTaxList(item) {
            let taxSelect = $('<select>', {
                class: 'form-control select2-normal',
                multiple: true,
                name: 'items[' + item.type + '][' + item.id + '][taxes][]',
            }).data('placeholder', '{{ trans('Corals::labels.select', ['label' => '']) }}');
            taxSelect.append(new Option());
            let taxes = JSON.parse('{!! json_encode($taxesList) !!}');

            let itemTaxes = item.tax_ids;

            if (itemTaxes) {
                itemTaxes = itemTaxes.map(function (ele) {
                    return parseInt(ele);
                });
            } else {
                itemTaxes = [];
            }

            $.each(taxes, function (index) {
                let option = new Option(taxes[index], index);

                if (itemTaxes.includes(parseInt(index))) {
                    option.selected = true;
                }

                taxSelect.append(option);
            });

            return taxSelect;
        }

        function getRemoveButton() {
            return $('<button>', {
                class: 'btn btn-sm btn-danger remove-btn',
                type: 'button'
            }).html('<i class="fa fa-trash-o"></i>');
        }


        $(document).on('click', '.remove-btn', function () {
            $(this).closest('tr').remove();
            if ($("#order-items-table tbody tr").length === 1) {
                $("#empty-items-row").show();
            } else {
                $("#empty-items-row").hide();
            }
        });

        function getOrderItemRow(item) {
            let itemKey = item.type + '_' + item.id;

            if ($("[data-item_key=" + itemKey + "]").length) {
                let itemQty = $("[data-item_key=" + itemKey + "] .item-quantity");

                itemQty.val(parseInt(itemQty.val()) + 1);
                return;
            }

            let tr = $('<tr>', {
                'data-item_key': itemKey,
                'data-item_type': item.type,
            });

            let td = $('<td>');

            let formGroup = $('<div>', {
                class: 'form-group'
            });

            let typeInput = $('<input>', {
                type: 'hidden',
                name: 'items[' + item.type + '][' + item.id + '][type]',
                value: item.type,
            });

            let nameInput = $('<input>', {
                type: 'hidden',
                name: 'items[' + item.type + '][' + item.id + '][name]',
                value: item.name,
            });

            tr.append(td.clone().append($('<span>').text(item.name)).append(nameInput));
            tr.append(td.clone().append($('<span>').text(item.type)).append(typeInput));

            let unitPrice = $('<input>', {
                type: 'number',
                class: 'form-control unit_price',
                name: 'items[' + item.type + '][' + item.id + '][unit_price]',
                value: item.unit_price,
                min: 0,
                step: 0.01,
            });

            tr.append(td.clone().append(formGroup.clone().append(unitPrice)));

            let quantity = $('<input>', {
                type: 'number',
                class: 'form-control item-quantity',
                min: 0,
                step: 1,
                name: 'items[' + item.type + '][' + item.id + '][quantity]',
                value: 1,
            });

            tr.append(td.clone().append(formGroup.clone().append(quantity)));

            let description = $('<input>', {
                type: 'text',
                class: 'form-control',
                name: 'items[' + item.type + '][' + item.id + '][description]',
                value: item.description,
            });

            tr.append(td.clone().append(formGroup.clone().append(description)));

            tr.append(td.clone().append(formGroup.clone().append(getTaxList(item))));

            tr.append(td.clone().append(formGroup.clone().append(getRemoveButton())));

            $("#empty-items-row").hide();

            return tr;
        }

        function addNewItemModal(type = 'product') {
            $("#add-item-modal").modal('show');
            $("div[id^=add-new-items-modal-fields]").hide();
            $('#add-new-items-modal-fields-' + type).show();
        }

        function addNewItem(type = 'product') {
            let selectedItem = $("#selected_item-" + type);

            let selectedItemObject = {
                id: null,
                name: null,
                unit_price: 0,
                description: null,
                type: ucfirst(type),
                tax_ids: null,
            };

            if (selectedItem.val()) {
                switch (type) {
                    case 'product':
                        $.get('{{ url(config('ecommerce.models.product.resource_url')) }}/sku/' + selectedItem.val(), function (data) {
                            selectedItemObject.id = data.id;
                            selectedItemObject.name = data.name;
                            selectedItemObject.unit_price = data.price;
                            selectedItemObject.description = data.description;
                            selectedItemObject.tax_ids = data.tax_ids;

                            appendTableBodyRow(selectedItemObject);
                        }, "json");
                        break;
                    case 'shipping':
                        $.get('{{ url(config('ecommerce.models.shipping.resource_url')) }}/' + selectedItem.val(), function (data) {
                            selectedItemObject.id = data.id;
                            selectedItemObject.name = data.name;
                            selectedItemObject.unit_price = data.price;
                            selectedItemObject.description = data.description;

                            appendTableBodyRow(selectedItemObject);
                        }, "json");
                        break;
                    case 'fee':
                        selectedItemObject.id = $('[data-item_type=Fee]').length;
                        selectedItemObject.name = selectedItem.val();

                        appendTableBodyRow(selectedItemObject);
                        break;
                }
            } else {
                themeNotify({
                    message: '{{ trans('Ecommerce::exception.order.item_selection_is_required') }}',
                    level: 'error'
                });
            }
        }

        function appendTableBodyRow(selectedItemObject) {
            let tableBody = $('#order-items-table tbody');

            tableBody.append(getOrderItemRow(selectedItemObject));

            $("#add-item-modal").modal('hide');

            initThemeElements();
        }

        $('#add-item-modal').on('hide.bs.modal', function () {
            let selectedItem = $(".selected_item");
            selectedItem.val("");
            selectedItem.trigger('change');
        });

        function recalculateOrder() {
            let itemsDiv = $('#items-div');

            $('#items_currency').val($('#currency').val());

            divSubmit(itemsDiv);
        }

        function setCalculations(response) {
            $('#items-subtotal').html(response.subtotal);
            $('#items-tax_total').html(response.tax_total);
            $('#items-total').html(response.total);
        }
    </script>
@endsection
