<?php

namespace Corals\Modules\Classified\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Classified\Models\Product;
use Corals\Modules\Classified\Transformers\ProductTransformer;
use Yajra\DataTables\EloquentDataTable;

class ProductsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('classified.models.product.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new ProductTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Product $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Product $model)
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
            'image' => ['width' => '50px', 'title' => trans('Classified::attributes.product.image'), 'orderable' => false, 'searchable' => false],
            'name' => ['title' => trans('Classified::attributes.product.name')],
            'location' => ['title' => trans('Classified::attributes.product.location'), 'orderable' => false, 'searchable' => false],
            'price' => ['title' => trans('Classified::attributes.product.price'), 'orderable' => false, 'searchable' => false],
            'categories' => ['title' => trans('Classified::attributes.product.categories'), 'orderable' => false, 'searchable' => false],
            'tags' => ['title' => trans('Classified::attributes.product.tags'), 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            'visitors_count' => ['title' => trans('Classified::attributes.product.views'), 'orderable' => true, 'searchable' => false, 'width' => '5%'],
            'status' => ['title' => trans('Corals::attributes.status')],
            'created_by' => ['title' => trans('Corals::attributes.created_by')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }

    public function getFilters()
    {
        return [
            'name' => ['title' => trans('Classified::attributes.product.title_name'), 'class' => 'col-md-2', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'location.id' => ['title' => trans('Classified::attributes.product.location'), 'class' => 'col-md-2', 'type' => 'select2', 'options' => \Address::getLocationsList('Classified'), 'active' => true],
            'description' => ['title' => trans('Classified::attributes.product.description'), 'class' => 'col-md-3', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'status' => ['title' => trans('Classified::labels.product.active_products'), 'class' => 'col-md-2', 'checked_value' => 'active', 'type' => 'boolean', 'active' => true],
        ];
    }

    protected function getBulkActions()
    {
        return [
            'delete' => ['title' => trans('Corals::labels.delete'), 'permission' => 'Classified::product.delete', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'active' => ['title' => '<i class="fa fa-check-circle"></i> ' . trans('Classified::attributes.product.status_options.active'), 'permission' => 'Classified::product.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'inActive' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Classified::attributes.product.status_options.inactive'), 'permission' => 'Classified::product.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'archived' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Classified::attributes.product.status_options.archived'), 'permission' => 'Classified::product.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'sold' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Classified::attributes.product.status_options.sold'), 'permission' => 'Classified::product.update', 'confirmation' => trans('Corals::labels.confirmation.title')]
        ];
    }

    protected function getOptions()
    {
        $url = url(config('classified.models.product.resource_url'));
        return ['resource_url' => $url];
    }
}
