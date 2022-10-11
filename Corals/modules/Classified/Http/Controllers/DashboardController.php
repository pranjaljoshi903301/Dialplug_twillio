<?php

namespace Corals\Modules\Classified\Http\Controllers;

use Corals\Foundation\Http\Controllers\PublicBaseController;
use Illuminate\Http\Request;

class DashboardController extends PublicBaseController
{
    public function __construct()
    {
        $this->title = 'Classified::module.dashboard.title';

        $this->title_singular = 'Classified::module.dashboard.title_singular';

        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('views.dashboard');
    }
}
