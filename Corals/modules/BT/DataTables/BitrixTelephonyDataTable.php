<?php

namespace Corals\Modules\BT\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Transformers\BitrixTelephonyTransformer;
use Yajra\DataTables\EloquentDataTable;

class BitrixTelephonyDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('bt.models.bitrixtelephony.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new BitrixTelephonyTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param BitrixTelephony $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(BitrixTelephony $model)
    {        
        if (isSuperUser()) {
            return $model->newQuery();
        } else {
            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
            if($isValid) return $model->newQuery();
            return $model->newQuery()->where('user_id', user()->id);
        }
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
	if(isSuperUser()) {
        return [
	    'id' => ['visible' => false],

            // Dialplug Fields
            'user' => ['title' => 'User'],
            'freepbx_subdomain' => ['title' => 'FreePBX Domain'],
	    'phone_number' => ['title' => 'Phone Number'],
            'webhook_url' => ['title' => 'Webhook URL'],
	    'setup_status' => ['title' => 'Setup Status']
        ];
        } else {
        return [
	    'id' => ['visible' => false],

            // Dialplug Fields
            'user' => ['title' => 'User'],
            'webhook_url' => ['title' => 'Webhook URL'],
	    'phone_number' => ['title' => 'Phone Number'],
	    'setup_status' => ['title' => 'Setup Status']
        ];
        }
    }
}
