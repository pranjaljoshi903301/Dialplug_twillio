<?php

namespace Corals\Modules\Ecommerce\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Ecommerce\DataTables\CouponsDataTable;
use Corals\Modules\Ecommerce\Http\Requests\CouponRequest;
use Corals\Modules\Ecommerce\Models\Coupon;
use Corals\Modules\Ecommerce\Services\CouponService;
use Corals\Modules\Ecommerce\Transformers\API\CouponPresenter;

class CouponsController extends APIBaseController
{
    protected $couponService;

    /**
     * CouponsController constructor.
     * @param CouponService $couponService
     * @throws \Exception
     */
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
        $this->couponService->setPresenter(new CouponPresenter());

        parent::__construct();
    }

    /**
     * @param CouponRequest $request
     * @param CouponsDataTable $dataTable
     * @return mixed
     * @throws \Exception
     */
    public function index(CouponRequest $request, CouponsDataTable $dataTable)
    {
        $coupons = $dataTable->query(new Coupon());

        return $this->couponService->index($coupons, $dataTable);
    }

    /**
     * @param CouponRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CouponRequest $request)
    {
        try {
            $coupon = $this->couponService->store($request, Coupon::class);
            return apiResponse($this->couponService->getModelDetails(), trans('Corals::messages.success.created', ['item' => $coupon->code]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param CouponRequest $request
     * @param Coupon $coupon
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        try {
            $this->couponService->update($request, $coupon);

            return apiResponse($this->couponService->getModelDetails(), trans('Corals::messages.success.updated', ['item' => $coupon->code]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param CouponRequest $request
     * @param Coupon $coupon
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CouponRequest $request, Coupon $coupon)
    {
        try {
            $this->couponService->destroy($request, $coupon);

            return apiResponse([], trans('Corals::messages.success.deleted', ['item' => $coupon->code]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
}
