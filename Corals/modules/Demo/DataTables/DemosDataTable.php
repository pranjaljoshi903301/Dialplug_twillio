<?php

namespace Corals\Modules\Demo\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Demo\Models\Demo;
use Corals\Modules\Demo\Transformers\DemoTransformer;
use Yajra\DataTables\EloquentDataTable;

class DemosDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('demo.models.demo.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new DemoTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Demo $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Demo $model)
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
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }
}
