<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Corals\Modules\Exotel\Services\BarService;
use Corals\Modules\Exotel\Http\Controllers\API\BarsController;

class ExotelCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exotel:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Incoming recording Sync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exotelcontroller = new BarsController(new BarService);
        $exotelcontroller->upload_recording();
        return 0;
    }
}
