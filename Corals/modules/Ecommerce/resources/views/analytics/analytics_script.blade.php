<script>
    window.dataLayer = window.dataLayer || [];
</script>
@isset($page_type)

    @switch($page_type)
        @case('product_archive')


        <script>
            dataLayer.push({
                'ecommerce': {
                    'currencyCode': 'USD',
                    'impressions': {!!   \Shop::formatAnalyticsProducts($products) !!},
                },
                'event': 'productImpression'

            });
        </script>
        @break

        @case('product_single')

        <script>
            dataLayer.push({
                'ecommerce': {
                    'detail': {
                        'products': corals.product,
                    }
                },
                'event': 'productDetail'
            });
        </script>



        @break

        @case('checkout_page')

        <script>
            var cart_products = {!!   \Shop::formatAnalyticsCartItems() !!};
        </script>




        @break


        @case('checkout_success')
        <script>
            // Send transaction data with a pageview if available
            // when the page loads. Otherwise, use an event when the transaction
            // data becomes available.
            dataLayer.push({
                'ecommerce': {
                    'currencyCode': '{{$order->currency}}',
                    'purchase': {
                        'actionField': {
                            'id': '{{$order->order_number}}',                         // Transaction ID. Required for purchases and refunds.
                            'affiliation': 'Online Store',
                            'revenue': '{{$order->amount}}',                     // Total transaction value (incl. tax and shipping)
                            'tax': '{{  $order->getTaxAmount()   }}',
                            'shipping': '{{  $order->getShippingAmount()   }}',
                            'coupon': '{{  $order->getCouponCode()   }}'
                        },
                        'products': corals.order_products,
                    }
                },
                'event': 'transactionSuccess'
            });
        </script>
        @break

        @default
    @endswitch

@endisset