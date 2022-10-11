<?php

namespace Corals\Modules\Ecommerce\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Ecommerce\Models\Brand;
use Corals\Modules\Ecommerce\Transformers\BrandTransformer;
use Yajra\DataTables\EloquentDataTable;

class BrandsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('ecommerce.models.brand.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new BrandTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Brand $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Brand $model)
    {
        return $model->withCount('products');
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
            'thumbnail' => ['title' => trans('Ecommerce::attributes.brand.logo')],
            'name' => ['title' => trans('Ecommerce::attributes.brand.name')],
            'slug' => ['title' =>trans('Ecommerce::attributes.brand.slug')],
            'products_count' => ['title' => trans('Ecommerce::attributes.brand.products_count'), 'searchable' => false],
            'status' => ['title' =>  trans('Corals::attributes.status')],
            'is_featured' => ['title' => trans('Ecommerce::attributes.brand.is_featured')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }

    protected function getBulkActions()
    {
        return [
            'delete' => ['title' => trans('Corals::labels.delete'), 'permission' => 'Ecommerce::brand.delete', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'active' => ['title' => trans('Corals::attributes.status_options.active'), 'permission' => 'Ecommerce::brand.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'inActive' => ['title' => trans('Corals::attributes.status_options.inactive'), 'permission' => 'Ecommerce::brand.update', 'confirmation' => trans('Corals::labels.confirmation.title')]
        ];
    }

    protected function getOptions()
    {
        $url = url(config('ecommerce.models.brand.resource_url'));
        return ['resource_url' => $url];
    }
}
