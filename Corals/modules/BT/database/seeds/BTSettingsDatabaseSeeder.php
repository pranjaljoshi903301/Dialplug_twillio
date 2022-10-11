<?php

namespace Corals\Modules\BT\database\seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BTSettingsDatabaseSeeder extends Seeder
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
                'code' => 'bt_setting',
                'type' => 'TEXT',
                'category' => 'BT',
                'label' => 'BT setting',
                'value' => 'bt',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
