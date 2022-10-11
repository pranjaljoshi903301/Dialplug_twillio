<?php

namespace Corals\Modules\Ecommerce\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Transformers\OrderTransformer;
use Yajra\DataTables\EloquentDataTable;

class OrdersDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('ecommerce.models.order.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new OrderTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Order $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Order $model)
    {
        return $model->with('user')->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'order_number' => ['title' => trans('Ecommerce::attributes.order.order_number')],
            'amount' => ['title' => trans('Ecommerce::attributes.order.amount')],
            'status' => ['title' => trans('Corals::attributes.status')],
            'payment_status' => ['title' => trans('Ecommerce::attributes.order.payment_status'), 'orderable' => false, 'searchable' => false],
            'user_id' => ['title' => trans('Ecommerce::attributes.order.user_id')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')]
        ];
    }

    protected function getBulkActions()
    {
        return [
            'pending' => ['title' => trans('Ecommerce::status.order.pending'), 'permission' => 'Ecommerce::order.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'processing' => ['title' => trans('Ecommerce::status.order.processing'), 'permission' => 'Ecommerce::order.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'submitted' => ['title' => trans('Ecommerce::status.order.submitted'), 'permission' => 'Ecommerce::order.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'canceled' => ['title' => trans('Ecommerce::status.order.canceled'), 'permission' => 'Ecommerce::order.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'completed' => ['title' => trans('Ecommerce::status.order.completed'), 'permission' => 'Ecommerce::order.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
        ];
    }

    protected function getOptions()
    {
        $url = url(config('ecommerce.models.order.resource_url'));
        return ['has_action' => true, 'resource_url' => $url];
    }

    protected function getBuilderParameters()
    {
        return ['order' => [[ 7 , 'desc']]];
    }

    public function getFilters()
    {
        return [
            'user.name' => ['title' => trans('User::attributes.user.name'), 'class' => 'col-md-2', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'user.last_name' => ['title' => trans('User::attributes.user.last_name'), 'class' => 'col-md-2', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'created_at' => ['title' => trans('Corals::attributes.created_at'), 'class' => 'col-md-4', 'type' => 'date_range', 'active' => true],
            'status' => ['title' => trans('Ecommerce::attributes.order.status_order'), 'class' => 'col-md-2', 'options' => trans(config('ecommerce.models.order.statuses')), 'type' => 'select', 'active' => true],

        ];
    }


}
