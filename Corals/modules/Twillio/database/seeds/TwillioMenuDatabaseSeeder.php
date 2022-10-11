<?php

namespace Corals\Modules\Twillio\database\seeds;

use Illuminate\Database\Seeder;

class TwillioMenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $twillio_menu_id = \DB::table('menus')->insertGetId([
            'parent_id' => 1,// admin
            'key' => 'twillio',
            'url' => null,
            'active_menu_url' => 'bars*',
            'name' => 'Twillio',
            'description' => 'Twillio Menu Item',
            'icon' => 'fa fa-globe',
            'target' => null, 'roles' => '["1","2"]',
            'order' => 0
        ]);

        // seed children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $twillio_menu_id,
                    'key' => null,
                    'url' => config('twillio.models.bar.resource_url'),
                    'active_menu_url' => config('twillio.models.bar.resource_url') . '*',
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
