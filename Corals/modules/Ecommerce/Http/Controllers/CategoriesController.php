<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\Modules\Ecommerce\DataTables\CategoriesDataTable;
use Corals\Modules\Ecommerce\Http\Requests\CategoryRequest;
use Corals\Modules\Ecommerce\Models\Category;
use Corals\Modules\Ecommerce\Services\CategoryService;

class CategoriesController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->resource_url = config('ecommerce.models.category.resource_url');
        $this->title = 'Ecommerce::module.category.title';
        $this->title_singular = 'Ecommerce::module.category.title_singular';

        parent::__construct();
    }

    /**
     * @param CategoryRequest $request
     * @param CategoriesDataTable $dataTable
     * @return mixed
     */
    public function index(CategoryRequest $request, CategoriesDataTable $dataTable)
    {
        return $dataTable->render('Ecommerce::categories.index');
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(CategoryRequest $request)
    {
        $category = new Category();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Ecommerce::categories.create_edit')->with(compact('category'));
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CategoryRequest $request)
    {
        try {
            $this->categoryService->store($request, Category::class);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return Category
     */
    public function show(CategoryRequest $request, Category $category)
    {
        return $category;
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return $this
     */
    public function edit(CategoryRequest $request, Category $category)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $category->name])]);

        return view('Ecommerce::categories.create_edit')->with(compact('category'));
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->update($request, $category);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BulkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(BulkRequest $request)
    {
        try {
            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);
            switch ($action) {
                case 'delete':
                    foreach ($selection as $selection_id) {
                        $category = Category::findByHash($selection_id);
                        $category_request = new CategoryRequest;
                        $category_request->setMethod('DELETE');
                        $this->destroy($category_request, $category);
                    }
                    $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
                    break;
                case 'active' :
                    foreach ($selection as $selection_id) {
                        $category = Category::findByHash($selection_id);
                        if (user()->can('Ecommerce::category.update')) {
                            $category->update([
                                'status' => 'active'
                            ]);
                            $category->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;

                case 'inActive' :
                    foreach ($selection as $selection_id) {
                        $category = Category::findByHash($selection_id);
                        if (user()->can('Ecommerce::category.update')) {
                            $category->update([
                                'status' => 'inactive'
                            ]);
                            $category->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }
        return response()->json($message);
    }

    public function destroy(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->destroy($request, $category);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
