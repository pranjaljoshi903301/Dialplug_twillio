<?php

namespace Corals\Modules\Twillio\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Twillio\Models\Bar;
use Corals\Modules\Twillio\Transformers\BarTransformer;
use Yajra\DataTables\EloquentDataTable;

class BarsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('twillio.models.bar.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new BarTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Bar $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Bar $model)
    {
        return $model->newQuery();
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
            'subscriber_email' => ['title' => 'Subscriber Email'],
            'bitrix_domain' => ['title' => 'Bitrix Domain'],            
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }
}
