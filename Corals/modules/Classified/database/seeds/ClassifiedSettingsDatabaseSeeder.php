<?php

namespace Corals\Modules\Classified\database\seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClassifiedSettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->insert([
            [
                'code' => 'classified_auth_theme',
                'type' => 'TEXT',
                'category' => 'Classified',
                'label' => 'Auth theme code',
                'value' => 'corals-classified-master',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_product_condition_options',
                'type' => 'SELECT',
                'category' => 'Classified',
                'label' => 'Product condition options',
                'value' => json_encode(['new' => 'New', 'used' => 'Used', 'refurbished' => 'Refurbished']),
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_wishlist_enable',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Enable Wishlist',
                'value' => 'true',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_rating_enable',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Enable Rating',
                'value' => 'true',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_appearance_page_limit',
                'type' => 'number',
                'category' => 'Classified',
                'label' => 'Product page limit',
                'value' => 10,
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_messaging_is_enable',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Enable Internal Messaging',
                'value' => 'true',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'classified_subscription_is_enable',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Enable Classified Subscription Integration',
                'value' => 'false',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'classified_year_model_visible',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Year Model field visible',
                'value' => 'false',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'classified_subscription_product_id',
                'type' => 'NUMBER',
                'category' => 'Classified',
                'label' => 'Subscription Product Id',
                'value' => '',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'classified_allowed_products_count_feature_id',
                'type' => 'NUMBER',
                'category' => 'Classified',
                'label' => 'Allowed Products Count Feature Id',
                'value' => '',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'classified_allowed_featured_products_count_feature_id',
                'type' => 'NUMBER',
                'category' => 'Classified',
                'label' => 'Allowed Featured Products Count Feature Id',
                'value' => '',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'classified_enable_google_location',
                'type' => 'BOOLEAN',
                'category' => 'Classified',
                'label' => 'Enable Google Location',
                'value' => 'false',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
