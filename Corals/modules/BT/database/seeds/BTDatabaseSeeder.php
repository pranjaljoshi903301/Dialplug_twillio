<?php

namespace Corals\Modules\BT\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\Models\Media;

class BTDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BTPermissionsDatabaseSeeder::class);
        $this->call(BTMenuDatabaseSeeder::class);
        $this->call(BTSettingsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'BT::%')->delete();

        Menu::where('key', 'bt')
            ->orWhere('active_menu_url', 'like', 'bts%')
            ->orWhere('url', 'like', 'bts%')
            ->delete();

        Setting::where('category', 'BT')->delete();

        Media::whereIn('collection_name', ['bt-media-collection'])->delete();
    }
}
