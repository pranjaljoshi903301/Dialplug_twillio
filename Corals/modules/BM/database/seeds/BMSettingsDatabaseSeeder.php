<?php

namespace Corals\Modules\BM\database\seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BMSettingsDatabaseSeeder extends Seeder
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
                'code' => 'bm_setting',
                'type' => 'TEXT',
                'category' => 'BM',
                'label' => 'BM setting',
                'value' => 'bm',
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
