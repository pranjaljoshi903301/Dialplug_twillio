<?php

namespace Corals\Modules\BM\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\Models\Media;

class BMDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BMPermissionsDatabaseSeeder::class);
        $this->call(BMMenuDatabaseSeeder::class);
        $this->call(BMSettingsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'BM::%')->delete();

        Menu::where('key', 'bm')
            ->orWhere('active_menu_url', 'like', 'bms%')
            ->orWhere('url', 'like', 'bms%')
            ->delete();

        Setting::where('category', 'BM')->delete();

        Media::whereIn('collection_name', ['bm-media-collection'])->delete();
    }
}
