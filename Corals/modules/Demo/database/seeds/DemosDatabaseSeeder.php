<?php

namespace Corals\Modules\Demo\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\User\Models\Permission;
use Corals\User\Models\User;
use Illuminate\Database\Seeder;

class DemosDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DemoPermissionsDatabaseSeeder::class);
        $this->call(DemoDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Demo%')->delete();

        $menus = Menu::where('key', 'demo')->get();

        User::where('email', 'superuser@laraship.com')->delete();

        foreach ($menus as $menu) {
            Menu::where('parent_id', $menu->id)->delete();
            $menu->delete();
        }
    }
}
