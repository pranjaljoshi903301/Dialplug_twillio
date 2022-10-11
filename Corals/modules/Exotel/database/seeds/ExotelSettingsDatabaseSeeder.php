<?php

namespace Corals\Modules\Exotel\database\seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExotelSettingsDatabaseSeeder extends Seeder
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
                'code' => 'exotel_setting',
                'type' => 'TEXT',
                'category' => 'Exotel',
                'label' => 'Exotel setting',
                'value' => 'exotel',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
