<?php

namespace Corals\Modules\Exotel\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;
use \Spatie\MediaLibrary\Models\Media;

class TwillioDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TwillioPermissionsDatabaseSeeder::class);
        $this->call(TwillioMenuDatabaseSeeder::class);
        $this->call(TwillioSettingsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Twillio::%')->delete();

        Menu::where('key', 'twillio')
            ->orWhere('active_menu_url', 'like', 'twillios%')
            ->orWhere('url', 'like', 'twillios%')
            ->delete();

        Setting::where('category', 'Twillio')->delete();

        Media::whereIn('collection_name', ['twillio-media-collection'])->delete();
    }
}
