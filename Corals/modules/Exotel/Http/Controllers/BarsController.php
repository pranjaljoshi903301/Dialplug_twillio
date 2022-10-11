<?php

namespace Corals\Modules\Exotel\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Exotel\DataTables\BarsDataTable;
use Corals\Modules\Exotel\Http\Requests\BarRequest;
use Corals\Modules\Exotel\Models\Bar;
use Corals\Modules\Exotel\Services\BarService;

class BarsController extends BaseController
{
    protected $barService;

    public function __construct(BarService $barService)
    {
        $this->barService = $barService;

        $this->resource_url = config('exotel.models.bar.resource_url');

        $this->title = "Exotel";
        $this->title_singular = "Exotel";

        parent::__construct();
    }

    /**
     * @param BarRequest $request
     * @param BarsDataTable $dataTable
     * @return mixed
     */
    public function index(BarRequest $request, BarsDataTable $dataTable)
    {
        return $dataTable->render('Exotel::bars.index');
    }

    /**
     * @param BarRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(BarRequest $request)
    {
        $bar = new Bar();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Exotel::bars.create_edit')->with(compact('bar'));
    }

    /**
     * @param BarRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(BarRequest $request)
    {
        try {
            $bar = $this->barService->store($request, Bar::class);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Bar::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(BarRequest $request, Bar $bar)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $bar->getIdentifier()])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $bar->hashed_id . '/edit']);

        return view('Exotel::bars.show')->with(compact('bar'));
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(BarRequest $request, Bar $bar)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $bar->getIdentifier()])]);

        return view('Exotel::bars.create_edit')->with(compact('bar'));
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(BarRequest $request, Bar $bar)
    {
        try {
            $this->barService->update($request, $bar);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Bar::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BarRequest $request, Bar $bar)
    {
        try {
            $this->barService->destroy($request, $bar);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Bar::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
