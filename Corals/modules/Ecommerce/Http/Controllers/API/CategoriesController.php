<?php

namespace Corals\Modules\Ecommerce\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Ecommerce\DataTables\CategoriesDataTable;
use Corals\Modules\Ecommerce\Http\Requests\CategoryRequest;
use Corals\Modules\Ecommerce\Models\Category;
use Corals\Modules\Ecommerce\Services\CategoryService;
use Corals\Modules\Ecommerce\Transformers\API\CategoryPresenter;

class CategoriesController extends APIBaseController
{
    protected $categoryService;

    /**
     * CategoriesController constructor.
     * @param CategoryService $categoryService
     * @throws \Exception
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->categoryService->setPresenter(new CategoryPresenter());

        parent::__construct();
    }

    /**
     * @param CategoryRequest $request
     * @param CategoriesDataTable $dataTable
     * @return mixed
     * @throws \Exception
     */
    public function index(CategoryRequest $request, CategoriesDataTable $dataTable)
    {
        $categories = $dataTable->query(new Category());

        return $this->categoryService->index($categories, $dataTable);
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->store($request, Category::class);
            return apiResponse($this->categoryService->getModelDetails(), trans('Corals::messages.success.created', ['item' => $category->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CategoryRequest $request, Category $category)
    {
        try {
            return apiResponse($this->categoryService->getModelDetails($category));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->update($request, $category);

            return apiResponse($this->categoryService->getModelDetails(), trans('Corals::messages.success.updated', ['item' => $category->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->destroy($request, $category);

            return apiResponse([], trans('Corals::messages.success.deleted', ['item' => $category->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
}
