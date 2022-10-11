<?php

namespace Corals\Modules\Demo\database\seeds;

use Carbon\Carbon;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Modules\Utility\Models\Rating\Rating;
use Corals\User\Models\Role;
use Corals\User\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $demo_superuser_id = \DB::table('users')->insertGetId([
            'name' => 'Laraship Admin',
            'email' => 'superuser@laraship.com',
            'password' => bcrypt('123456'),
            'job_title' => 'Administrator',
            'address' => 'Cecilia Chapman, Mankato Mississippi 96522',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $superuser_role = Role::whereName('superuser')->first();

        if ($superuser_role) {
            $superuser_role->users()->attach($demo_superuser_id);
        }


        if (\Modules::isModuleActive('corals-subscriptions')) {

            $users = factory(User::class, 100)->create()->each(function ($user) {
                $user->subscriptions()->save(factory(Subscription::class)->make());
            });

            $subscriptions = Subscription::all();

            foreach ($subscriptions as $subscription) {
                $invoice = Invoice::create([
                    'code' => Invoice::getCode('INV'),
                    'currency' => 'usd',
                    'status' => 'paid',
                    'invoicable_id' => $subscription->id,
                    'invoicable_type' => get_class($subscription),
                    'due_date' => Carbon::now()->addDays(random_int(-365, 0)),
                    'invoice_date' => now(),
                    'sub_total' => $subscription->plan->price,
                    'total' => $subscription->plan->price,
                    'user_id' => $subscription->user_id,
                ]);

                $invoice->items()->create([
                    'code' => \Str::random(6),
                    'amount' => $subscription->plan->price,
                    'itemable_id' => $subscription->plan->id,
                    'itemable_type' => get_class($subscription->plan),
                ]);
            }
        }

        if (\Modules::isModuleActive('corals-ecommerce')) {
            $users = factory(\Corals\User\Models\User::class, 260)->create();

            $skus = \Corals\Modules\Ecommerce\Models\SKU::get();

            $statuses = ['pending', 'processing', 'canceled', 'completed'];

            foreach ($skus as $sku) {
                $userId = $users->random()->id;
                $qt = random_int(1, 4);
                $items = [];
                $shippingItems = [
                    [
                        'amount' => 10.00,
                        'description' => 'Flat Rate -  Shipping',
                        'quantity' => 1,
                        'sku_code' => 'FlatRate|Flat Rate',
                        'type' => 'Shipping',
                        'item_options' => null
                    ],
                    [
                        'amount' => 7.75,
                        'description' => 'USPS - Parcel Select Shipping',
                        'quantity' => 1,
                        'sku_code' => 'Shippo|' . \Str::random(32),
                        'type' => 'Shipping',
                        'item_options' => null
                    ],
                    [
                        'amount' => 4.66,
                        'description' => 'USPS - First-Class Package/Mail Parcel Shipping',
                        'quantity' => 1,
                        'sku_code' => 'Shippo|' . \Str::random(32),
                        'type' => 'Shipping',
                        'item_options' => null
                    ]
                ];
                $discountItem = [
                    'amount' => -45,
                    'description' => 'Discount Coupon',
                    'quantity' => 1,
                    'sku_code' => 'CORALS-FIXED',
                    'type' => 'Discount',
                    'item_options' => null
                ];
                $skuItem = [
                    'amount' => $sku->price,
                    'description' => $sku->product->name,
                    'quantity' => $qt,
                    'sku_code' => $sku->code,
                    'type' => 'Product',
                    'item_options' => '{\"product_options\":[]}'
                ];

                $items[] = $skuItem;

                if ($sku->id % 2) {
                    $items[] = $discountItem;
                }

                $items[] = $shippingItems[random_int(0, 2)];

                $amount = 0;

                foreach ($items as $item) {
                    $amount += $item['amount'] * $item['quantity'];
                }
                $orderNum = \Ecommerce::createOrderNumber();

                $orderId = \DB::table('ecommerce_orders')->insertGetId([
                    'amount' => $amount,
                    'currency' => 'USD',
                    'order_number' => $orderNum,
                    'billing' => '{\"status\":\"pending\",\"label_url\":\"\",\"tracking_number\":\"\",\"shipping_address\":{\"address_1\":\"711-2880 Nulla St.\",\"address_2\":\"Cecilia Chapman\",\"type\":\"shipping\",\"city\":\"Mankato\",\"state\":\"MS\",\"zip\":\"96522\",\"country\":\"US\"},\"shipping_provider\":\"FlatRate\",\"selected_shipping\":{\"provider\":\"Flat Rate\",\"service\":\"\",\"currency\":\"USD\",\"amount\":\"10.00\",\"estimated_days\":\"\"}}\', \'{\"billing_address\":{\"address_1\":\"711-2880 Nulla St.\",\"address_2\":\"Cecilia Chapman\",\"type\":\"billing\",\"city\":\"Mankato\",\"state\":\"MS\",\"zip\":\"96522\",\"country\":\"US\"},\"payment_reference\":\"ch_1C8YJrG0x8xKQUt93uHWgRQr\",\"gateway\":\"Stripe\",\"payment_status\":\"paid\"}',
                    'status' => $statuses[random_int(0, 3)],
                    'user_id' => $userId,
                    'created_at' => Carbon::now()->subDays($userId)->toDateString(),
                    'updated_at' => Carbon::now()->subDays($userId / 2)->toDateString(),
                ]);

                foreach ($items as $index => $item) {
                    $items[$index]['order_id'] = $orderId;
                }

                \DB::table('ecommerce_order_items')->insert($items);
            }

            $orders = \Corals\Modules\Ecommerce\Models\Order::get();

            foreach ($orders as $order) {
                $invoice = Invoice::create([
                    'code' => Invoice::getCode('INV'),
                    'currency' => $order->currency,
                    'status' => 'paid',
                    'invoicable_id' => $order->id,
                    'invoicable_type' => get_class($order),
                    'due_date' => $order->created_at,
                    'invoice_date' => now(),
                    'sub_total' => $order->amount,
                    'total' => $order->amount,
                    'user_id' => $order->user->id,
                    'created_at' => $order->created_at
                ]);

                $invoice_items = [];
                foreach ($order->items as $order_item) {
                    $invoice_items[] = [
                        'code' => \Str::random(6),
                        'description' => $order_item->description,
                        'amount' => $order_item->amount,
                        'quantity' => $order_item->quantity,
                        'itemable_id' => $order_item->id,
                        'itemable_type' => get_class($order_item),
                    ];
                }

                $invoice->items()->createMany($invoice_items);
            }


            // add reviews
            $users = \Corals\User\Models\User::whereHas('roles', function ($role) {
                $role->where('name', 'member');
            })->get();
            $faker = Faker::create();
            foreach ($users as $user) {
                if ($user->id % 25 == 0) {
                    for ($g = 1; $g <= 28; $g++) {
                        Rating::create([
                            'rating' => random_int(3, 5),
                            'title' => $faker->realText(50),
                            'body' => $faker->realText(),
                            'reviewrateable_id' => $g,
                            'reviewrateable_type' => \Corals\Modules\Ecommerce\Models\Product::class,
                            'author_id' => $user->id,
                            'author_type' => \Corals\User\Models\User::class
                        ]);
                    }
                }
            }
        }
    }
}
