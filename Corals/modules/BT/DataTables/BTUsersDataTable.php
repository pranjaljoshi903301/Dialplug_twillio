<?php

namespace Corals\Modules\BT\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Transformers\BTUsersTransformer;
use Yajra\DataTables\EloquentDataTable;

class BTUsersDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('bt.models.user.resource_url'));
        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new BTUsersTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param BTUsers $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(BTUsers $model)
    {
        if(isSuperUser()) {
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
            'user' => ['title' => 'User'],

            // Dialplug Fields
            'bitrix_user_name' => ['title' => 'Bitrix User'],
            'is_default' => ['title' => 'Default'],	    
	    'company_name' => ['title' => 'Company Name'],
            'inbound_route' => ['title' => 'Username'],
	    'sync_status' => ['title' => 'Sync Status']
        ];
	} else {
	return [
            'id' => ['visible' => false],

            // Dialplug Fields
            'bitrix_user_name' => ['title' => 'Bitrix User'],
            'is_default' => ['title' => 'Default'],	    
            'inbound_route' => ['title' => 'Username'],
	    'sync_status' => ['title' => 'Sync Status']
        ];
	}
    }
}
