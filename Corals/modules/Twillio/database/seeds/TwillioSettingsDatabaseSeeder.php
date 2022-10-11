<?php

namespace Corals\Modules\Twillio\database\seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TwillioSettingsDatabaseSeeder extends Seeder
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
                'code' => 'twillio_setting',
                'type' => 'TEXT',
                'category' => 'Twillio',
                'label' => 'Twillio setting',
                'value' => 'Twillio',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
