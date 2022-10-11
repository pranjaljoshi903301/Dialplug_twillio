<?php

namespace Corals\Modules\Directory\Notifications;

use Corals\User\Communication\Classes\CoralsBaseNotification;

class ClaimDeclineStatus extends CoralsBaseNotification
{
    /**
     * @return mixed
     */
    public function getNotifiables()
    {
        return $this->data['user'];
    }

    public function getNotificationMessageParameters($notifiable, $channel)
    {
        $claim = $this->data['claim'];
        $reasons = $this->data['reasons'] ?? '-';

        return [
            'listing_name' => $claim->listing->name,
            'claim_status' => $claim->status,
            'proof_of_business_registration' => $claim->proof_of_business_registration,
            'brief_description' => $claim->brief_description,
            'reasons' => $reasons,
            'user_name' => $claim->user->name,
            'listing_link' => url('listings/' . $claim->listing->slug)
        ];
    }

    public static function getNotificationMessageParametersDescriptions()
    {
        return [
            'listing_name' => 'listing name',
            'claim_status' => 'claim status',
            'proof_of_business_registration' => 'Proof Of Business Registration',
            'brief_description' => 'Brief Description',
            'reasons' => 'Decline Reasons',
            'user_name' => 'End User name',
            'listing_link' => 'Listing Link'
        ];
    }
}
