<?php

namespace Corals\Modules\Demo\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Demo\DataTables\DemosDataTable;
use Corals\Modules\Demo\Http\Requests\DemoRequest;
use Corals\Modules\Demo\Models\Demo;

class DemosController extends BaseController
{
    public function __construct()
    {
        $this->resource_url = config('demo.models.demo.resource_url');
        $this->title = 'Demos';
        $this->title_singular = 'Demo';

        parent::__construct();
    }

    /**
     * @param DemoRequest $request
     * @param DemosDataTable $dataTable
     * @return mixed
     */
    public function index(DemoRequest $request, DemosDataTable $dataTable)
    {
        return $dataTable->render('Demo::demos.index');
    }

    /**
     * @param DemoRequest $request
     * @return $this
     */
    public function create(DemoRequest $request)
    {
        $demo = new Demo();

        $this->setViewSharedData(['title_singular' => 'Create ' . $this->title_singular]);

        return view('Demo::demos.create_edit')->with(compact('demo'));
    }

    /**
     * @param DemoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(DemoRequest $request)
    {
        try {
            $data = $request->all();

            $demo = Demo::create($data);

            flash(trans('Corals::messages.success.created', ['item' => 'Demo']))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Demo::class, 'store');
        }

        return redirect($this->resource_url);
    }

    /**
     * @param DemoRequest $request
     * @param Demo $demo
     * @return $this
     */
    public function show(DemoRequest $request, Demo $demo)
    {
        $this->setViewSharedData(['title_singular' => "[{$demo->name}]"]);
        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $demo->hashed_id . '/edit']);
        return view('Demo::demos.show')->with(compact('demo'));
    }

    /**
     * @param DemoRequest $request
     * @param Demo $demo
     * @return $this
     */
    public function edit(DemoRequest $request, Demo $demo)
    {
        $this->setViewSharedData(['title_singular' => "Update [{$demo->name}]"]);

        return view('Demo::demos.create_edit')->with(compact('demo'));
    }

    /**
     * @param DemoRequest $request
     * @param Demo $demo
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(DemoRequest $request, Demo $demo)
    {
        try {
            $data = $request->all();

            $demo->update($data);

            flash(trans('Corals::messages.success.updated', ['item' => 'Demo']))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Demo::class, 'update');
        }

        return redirect($this->resource_url);
    }

    /**
     * @param DemoRequest $request
     * @param Demo $demo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DemoRequest $request, Demo $demo)
    {
        try {
            $demo->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => 'Demo'])];
        } catch (\Exception $exception) {
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
            log_exception($exception, Demo::class, 'destroy');
        }

        return response()->json($message);
    }
}
