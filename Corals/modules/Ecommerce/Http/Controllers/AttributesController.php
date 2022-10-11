<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Ecommerce\DataTables\AttributesDataTable;
use Corals\Modules\Ecommerce\Http\Requests\AttributeRequest;
use Corals\Modules\Ecommerce\Models\Attribute;
use Corals\Modules\Ecommerce\Services\AttributeService;

class AttributesController extends BaseController
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;

        $this->resource_url = config('ecommerce.models.attribute.resource_url');
        $this->title = 'Ecommerce::module.attribute.title';
        $this->title_singular = 'Ecommerce::module.attribute.title_singular';

        parent::__construct();
    }

    /**
     * @param AttributeRequest $request
     * @param AttributesDataTable $dataTable
     * @return mixed
     */
    public function index(AttributeRequest $request, AttributesDataTable $dataTable)
    {
        return $dataTable->render('Ecommerce::attributes.index');
    }

    /**
     * @param AttributeRequest $request
     * @return $this
     */
    public function create(AttributeRequest $request)
    {
        $attribute = new Attribute();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Ecommerce::attributes.create_edit')->with(compact('attribute'));
    }

    /**
     * @param AttributeRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(AttributeRequest $request)
    {
        try {
            $this->attributeService->store($request, Attribute::class);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Attribute::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @return Attribute
     */
    public function show(AttributeRequest $request, Attribute $attribute)
    {
        return $attribute;
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @return $this
     */
    public function edit(AttributeRequest $request, Attribute $attribute)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $attribute->label])]);

        return view('Ecommerce::attributes.create_edit')->with(compact('attribute'));
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function update(AttributeRequest $request, Attribute $attribute)
    {
        try {
            $this->attributeService->update($request, $attribute);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Attribute::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AttributeRequest $request, Attribute $attribute)
    {
        try {
            $this->attributeService->destroy($request, $attribute);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Attribute::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
