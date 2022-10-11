<?php

namespace Corals\Modules\BT\database\seeds;

use Illuminate\Database\Seeder;

class BTMenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bt_menu_id = \DB::table('menus')->insertGetId([
            'parent_id' => 1,// admin
            'key' => 'bt',
            'url' => null,
            'active_menu_url' => 'bitrix_telephony*',
            'name' => 'Bitrix Telephony',
            'description' => 'Bitrix Telephony Menu Item',
            'icon' => 'fa fa-phone',
            'target' => null, 'roles' => '["1","2"]',
            'order' => 0
        ]);

        // seed children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $bt_menu_id,
                    'key' => null,
                    'url' => config('bt.models.bitrixtelephony.resource_url'),
                    'active_menu_url' => config('bt.models.bitrixtelephony.resource_url') . '*',
                    'name' => 'Configuration',
                    'description' => 'Bitrix Telephony Configurations',
                    'icon' => 'fa fa-gears',
                    'target' => null, 'roles' => '["1", "2"]',
                    'order' => 0
                ],
                [
                    'parent_id' => $bt_menu_id,
                    'key' => null,
                    'url' => config('bt.models.user.resource_url'),
                    'active_menu_url' => config('bt.models.user.resource_url') . '*',
                    'name' => 'Users',
                    'description' => 'Bitrix Telephony Users',
                    'icon' => 'fa fa-users',
                    'target' => null, 'roles' => '["1", "2"]',
                    'order' => 0
                ]
            ]
        );
    }
}
