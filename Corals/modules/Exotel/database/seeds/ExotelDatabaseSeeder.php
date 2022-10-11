<?php

namespace Corals\Modules\Exotel\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;
use \Spatie\MediaLibrary\Models\Media;

class ExotelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ExotelPermissionsDatabaseSeeder::class);
        $this->call(ExotelMenuDatabaseSeeder::class);
        $this->call(ExotelSettingsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Exotel::%')->delete();

        Menu::where('key', 'exotel')
            ->orWhere('active_menu_url', 'like', 'exotels%')
            ->orWhere('url', 'like', 'exotels%')
            ->delete();

        Setting::where('category', 'Exotel')->delete();

        Media::whereIn('collection_name', ['exotel-media-collection'])->delete();
    }
}
