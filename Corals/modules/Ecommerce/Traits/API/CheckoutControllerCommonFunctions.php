<?php


namespace Corals\Modules\Ecommerce\Traits\API;


use Illuminate\Http\Request;

trait CheckoutControllerCommonFunctions
{
    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCouponByCode(Request $request, $code)
    {
        try {
            $coupon = $this->checkoutService->getCouponByCode($request, $code);

            return apiResponse($coupon);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    public function getAvailableShippingRoles(Request $request, $countryCode)
    {
        try {
            $coupon = $this->checkoutService->getAvailableShippingRoles($request, strtoupper($countryCode));

            return apiResponse($coupon);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
}
