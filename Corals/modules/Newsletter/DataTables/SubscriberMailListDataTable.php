<?php

namespace Corals\Modules\Newsletter\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Newsletter\Models\MailList;
use Corals\Modules\Newsletter\Models\Subscriber;
use Corals\Modules\Newsletter\Transformers\MailListTransformer;
use Yajra\DataTables\EloquentDataTable;

class SubscriberMailListDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('newsletter.models.mail-list.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new MailListTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Subscriber $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Subscriber $model)
    {
        $subscriber = $this->request->route('subscriber');
        if (!$subscriber) {
            abort('404');
        }
        return $subscriber->mailLists()->where('newsletter_mail_list_subscriber.subscriber_id', $subscriber->id)->withCount('subscribers');
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
            'name' => ['title' => trans('Newsletter::attributes.mail_list.name')],
            'status' => ['title' => trans('Corals::attributes.status')],
            'subscribers_count' => ['title' => trans('Newsletter::attributes.mail_list.subscribers_count'), 'searchable' => false],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }

    public function getFilters()
    {
        return [
            'name' => ['title' => trans('Newsletter::attributes.mail_list.name'), 'class' => 'col-md-3', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'created_at' => ['title' => trans('Corals::attributes.created_at'), 'class' => 'col-md-2', 'type' => 'date', 'active' => true],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at'), 'class' => 'col-md-2', 'type' => 'date', 'active' => true],
        ];
    }

}
