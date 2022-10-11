<?php

namespace Corals\Modules\BM\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Transformers\BitrixMobileTransformer;
use Yajra\DataTables\EloquentDataTable;

class BitrixMobileDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('bm.models.bitrixmobile.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new BitrixMobileTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param BitrixMobile $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(BitrixMobile $model)
    {
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if(isSuperUser() || $isValid) {
            return $model->newQuery()->where('product_id','5');
        } else {
            return $model->newQuery()->where([['email', user()->email],['product_id','5']]);
        }
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

            // BM Fields
            'user' => ['title' => 'Subscriber'],            
            'webhook_url' => ['title' => 'WebHook URL'],
            'agent_count' => ['title' => 'Agent Count'],
            'created_at' => ['title' => 'Created at']
            
        ];
    }
}
