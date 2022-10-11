<?php

namespace Corals\Modules\Exotel\database\seeds;

use Illuminate\Database\Seeder;

class ExotelMenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exotel_menu_id = \DB::table('menus')->insertGetId([
            'parent_id' => 1,// admin
            'key' => 'exotel',
            'url' => null,
            'active_menu_url' => 'bars*',
            'name' => 'Exotel',
            'description' => 'Exotel Menu Item',
            'icon' => 'fa fa-globe',
            'target' => null, 'roles' => '["1","2"]',
            'order' => 0
        ]);

        // seed children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $exotel_menu_id,
                    'key' => null,
                    'url' => config('exotel.models.bar.resource_url'),
                    'active_menu_url' => config('exotel.models.bar.resource_url') . '*',
                    'name' => 'Bars',
                    'description' => 'Bars List Menu Item',
                    'icon' => 'fa fa-cube',
                    'target' => null, 'roles' => '["1"]',
                    'order' => 0
                ],
            ]
        );
    }
}
