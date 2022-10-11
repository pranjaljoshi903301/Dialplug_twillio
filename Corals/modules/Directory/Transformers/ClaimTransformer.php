<?php

namespace Corals\Modules\Directory\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Directory\Models\Claim;

class ClaimTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('directory.models.claim.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Claim $claim
     * @return array
     * @throws \Throwable
     */
    public function transform(Claim $claim)
    {
        $listingUrl = url("listings/{$claim->listing->slug}");

        $levels = [
            'pending' => 'info',
            'approved' => 'success',
            'declined' => 'danger',
        ];

        $status = $claim->status;

        $transformedArray = [
            'id' => $claim->id,
            'requester' => $claim->user ? "<a href='" . url('users/' . $claim->user->hashed_id) . "'> {$claim->user->name}</a>" : "-",
            'listing' => $claim->listing ? '<a href="' . $listingUrl . '"target="_blank">' . $claim->listing->name . '</a>' : '-',
            'file' => $claim->claim_file ? "<a href='" . $claim->claim_file->getFullUrl() . "'> {$claim->claim_file->file_name}</a>" : "-",
            'status' => formatStatusAsLabels($status, ['level' => $levels[$status], 'text' => trans('Directory::attributes.claim.status_options.' . $status)]),
            'created_at' => format_date($claim->created_at),
            'action' => $this->actions($claim)
        ];

        return parent::transformResponse($transformedArray);
    }
}
