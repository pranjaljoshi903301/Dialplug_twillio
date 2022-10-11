<?php

namespace Corals\Modules\BM\database\seeds;

use Illuminate\Database\Seeder;

class BMMenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bm_menu_id = \DB::table('menus')->insertGetId([
            'parent_id' => 1,// admin
            'key' => 'bm',
            'url' => null,
            'active_menu_url' => 'bm_config',
            'name' => 'Bitrix Mobile',
            'description' => 'BM Menu Item',
            'icon' => 'fa fa-phone',
            'target' => null, 'roles' => '["1","2"]',
            'order' => 0
        ]);

        // seed children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $bm_menu_id,
                    'key' => null,
                    'url' => config('bm.models.bitrixmobile.resource_url'),
                    'active_menu_url' => config('bm.models.bitrixmobile.resource_url') . '*',
                    'name' => 'Users',
                    'description' => 'BM Users',
                    'icon' => 'fa fa-users',
                    'target' => null, 'roles' => '["1", "2"]',
                    'order' => 0
                ],
            ]
        );
    }
}
