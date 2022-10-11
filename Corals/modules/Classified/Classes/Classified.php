<?php

namespace Corals\Modules\Classified\Classes;

use Corals\Modules\Classified\Models\Product;
use Corals\Modules\Subscriptions\Classes\Products;
use Corals\Modules\Utility\Models\Address\Location;
use Corals\Modules\Utility\Models\Category\Attribute;
use Corals\Modules\Utility\Models\Category\Category;
use Corals\Settings\Facades\Settings;
use Illuminate\Http\Request;
use Corals\Foundation\Search\Search;
use Corals\User\Models\User;
use Corals\Modules\Utility\Classes\Wishlist\WishlistManager;


class Classified
{
    public $page_limit;

    public function __construct()
    {
        $this->page_limit = Settings::get('classified_appearance_page_limit', 15);
    }

    public function getAttributesForFilters($categories)
    {

        if (!$categories) {
            $categories = [];
        }

        if (!is_array($categories)) {
            $categories = [$categories];
        }

        $attributes = Attribute::query()->whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('slug', $categories);
        })->where('use_as_filter', true)->get();

        $filters = '';

        foreach ($attributes as $attribute) {
            $filters .= \Category::renderAttribute($attribute, null, ['as_filter' => true]);
        }

        return $filters;
    }

    protected function optionsQueryBuilderFilter($products, $attributes)
    {

        $attributesColumnMapping = \Category::attributesColumnMapping();

        $attributes = array_filter($attributes, function ($value) {
            return !empty($value);
        });

        if (empty($attributes)) {
            return $products;
        }

        foreach ($attributes as $key => $value) {
            $products = $products->join("utility_model_attribute_options as attribute_$key", "attribute_$key.model_id", '=', 'classified_products.id')
                ->where("attribute_$key.model_type", Product::class);

            $value = isset($attributesColumnMapping[$key]['operation']) && $attributesColumnMapping[$key]['operation'] == 'like' ? '%' . $value . '%' : $value;

            if (is_array($value)) {
                $products = $products->where("attribute_$key." . $attributesColumnMapping[$key]['column'] ?? 'string_value', $value);
            } else {
                $products = $products->where("attribute_$key." . $attributesColumnMapping[$key]['column'] ?? 'string_value', $attributesColumnMapping[$key]['operation'] ?? '=', $value);
            }
        }

        return $products;
    }

    protected function locationQueryBuilderFilter($products, $location_slug)
    {
        $queryMethod = 'where';

        if (is_array($location_slug)) {
            $queryMethod = 'whereIn';
        }

        $products = $products->join('utility_locations', 'classified_products.location_id', 'utility_locations.id')
            ->{$queryMethod}('utility_locations.slug', $location_slug);

        return $products;
    }


    public function getProducts(Request $request)
    {
        $products = $this->productsPublicBaseQuery();
        foreach ($request->all() as $filter => $value) {
            $filterMethod = $filter . 'QueryBuilderFilter';
            if (method_exists($this, $filterMethod) && !empty($value)) {
                $products = $this->{$filterMethod}($products, $value);

            }
        }
        $products = $products->addSelect('classified_products.*')->paginate($this->page_limit);
        return $products;

    }

    public function getMinPrice()
    {
        $min = Product::query()->active()->min('price');

        return \Payments::currency_convert($min);
    }

    public function getMaxPrice()
    {
        $max = Product::query()->active()->max('price');

        if ($max < 10) {
            $max = 99999;
        }
        return \Payments::currency_convert($max);

    }

    public function getMinYearModel()
    {
        $min = Product::query()->active()->min('year_model');
        return $min;
    }

    public function getMaxYearModel()
    {
        $max = Product::query()->active()->max('year_model');

        if ($max < 10) {
            $max = 99999;
        }

        return $max;

    }

    protected function categoryQueryBuilderFilter($products, $category, $status = 'active')
    {
        $queryMethod = 'where';

        if (is_array($category)) {
            $queryMethod = 'whereIn';
        }

        $orQueryMethod = 'or' . ucfirst($queryMethod); // << i.e orWhere || orWhereIn

        $categories = Category::{
        $queryMethod}('utility_categories.id', $category)
            ->orWhere(function ($parent) use ($queryMethod, $category) {
                $parent->{$queryMethod}('utility_categories.parent_id', $category)
                    ->where('utility_categories.parent_id', '<>', 0);
            })->{$orQueryMethod}('utility_categories.slug', $category)->pluck('id')->toArray();

        if ($categories) {

            $products = $products->join('utility_model_has_category', 'utility_model_has_category.model_id', 'classified_products.id')
                ->join('utility_categories', 'utility_model_has_category.category_id', 'utility_categories.id')
                ->where('utility_model_has_category.model_type', Product::class)
                ->where(function ($query) use ($categories) {
                    $query->whereIn('utility_categories.id', $categories)
                        ->orWhereIn('utility_categories.parent_id', $categories);
                });
        }


        if (!empty($status)) {
            $products->where('classified_products.status', $status);
        }

        return $products;

    }

    protected function priceQueryBuilderFilter($products, $price)
    {

        if (!is_array($price)) {
            return $products;
        }
        if (!isset($price['min']) && !isset($price['max'])) {
            return $products;
        }

        $minPrice = \Arr::get($price, 'min', 0);
        $minPrice = \Payments::currency_convert($minPrice, \Payments::session_currency(), \Payments::admin_currency_code());

        $maxPrice = \Arr::get($price, 'max', 999999);
        $maxPrice = \Payments::currency_convert($maxPrice, \Payments::session_currency(), \Payments::admin_currency_code());

        if ($this->getMinPrice() != $minPrice || $this->getMaxPrice() != $maxPrice) {
            $products = $products->whereBetween('classified_products.price', [$minPrice, $maxPrice]);
        }

        return $products;

    }


    protected function zipcodeQueryBuilderFilter($products, $zipcode)
    {

        if ($zipcode) {
            $products = $products->where('classified_products.zip_code', $zipcode);

        }


        return $products;

    }

    protected function year_modelQueryBuilderFilter($products, $yearModel)
    {
        if (!is_array($yearModel)) {
            return $products;
        }

        if (!isset($yearModel['min']) && !isset($yearModel['max'])) {
            return $products;
        }

        $minYear = \Arr::get($yearModel, 'min', 0);

        $maxYear = \Arr::get($yearModel, 'max', 999999);

        if ($this->getMinYearModel() != $minYear || $this->getMaxYearModel() != $maxYear) {
            $products = $products->whereBetween('classified_products.year_model', [$minYear, $maxYear]);
        }

        return $products;

    }

    public function getProductsList($objects = false, $takeOnlyLast = 0, $onlyFeatured = false, $paginationLimit = 0, $search = null, $categorySlug = 'all', $filters = [])
    {
        $products = Product::query();

        foreach ($filters as $filter => $value) {
            $filterMethod = $filter . 'QueryBuilderFilter';
            if (method_exists($this, $filterMethod) && !empty($value)) {
                $products = $this->{$filterMethod}($products, $value);
            }
        }

        if ($takeOnlyLast) {
            $products = $products->take($takeOnlyLast)->orderby('updated_at');
        }

        if ($search) {
            $products = $products->where('name', 'like', '%' . $search . '%');
        }

        if ($onlyFeatured) {
            $products = $products->featured();
        }

        if ($categorySlug != 'all') {
            $products = $products->whereHas('categories', function ($query) use ($categorySlug) {
                $query->where('utility_categories.slug', $categorySlug);
            });
        }

        if ($paginationLimit) {
            return $products->paginate($paginationLimit);
        }
        if ($objects) {
            return $products->get();
        }
        return $products->pluck('name', 'id');
    }

    public function getCategoryAvailableProducts($category_id, $count = false)
    {
        $products = Product::query()->active();

        $products = $this->categoryQueryBuilderFilter($products, $category_id);

        if ($count) {
            $products = $products->count();
        } else {
            $products = $products->select('classified_products.*')->paginate($this->page_limit);
        }
        return $products;
    }

    public function productsPublicBaseQuery()
    {
        return Product::active()->groupBy('classified_products.id');
    }


    public function getListingsVisitorsCount($status, $user = null)
    {
        $visitorsCount = Product::query();

        if ($status) {
            $visitorsCount = $visitorsCount->where('status', $status);
        }
        if ($user) {
            $visitorsCount = $visitorsCount->where('user_id', $user);
        }
        return $visitorsCount->sum('visitors_count');
    }


    /**
     * @param int $limit
     * @return mixed
     */
    public function getFeaturedProducts($limit = 3)
    {
        $products = $this->productsPublicBaseQuery();

        $products = $products->featured()->limit($limit)->get();

        return $products;
    }

    protected function sortQueryBuilderFilter($products, $sortOption)
    {
        switch ($sortOption) {
            case 'popular':
                break;
            case 'low_high_price':
                $products = $products->orderBy('price', 'asc');
                break;
            case 'high_low_price':
                $products = $products->orderBy('price', 'desc');
                break;
            case 'average_rating':

                $products = $products->leftJoin('ratings', 'reviewrateable_id', '=', 'classified_products.id')
                    ->where('ratings.reviewrateable_type', Product::class)
                    ->orWhereNull('ratings.id')
                    ->addSelect(\DB::raw('ROUND(AVG(rating), 2) as averageReviewRateable'))->orderBy('averageReviewRateable', 'desc');
                break;
            case 'a_z_order':
                $products = $products->orderBy('classified_products.name', 'asc');
                break;
            case 'z_a_order':
                $products = $products->orderBy('classified_products.name', 'desc');
                break;
        }
        return $products;
    }

    protected function searchQueryBuilderFilter($products, $search_term)
    {
        $search = new Search();

        $config = [
            'title_weight' => \Settings::get('classified_search_title_weight'),
            'content_weight' => \Settings::get('classified_search_content_weight'),
            'enable_wildcards' => \Settings::get('classified_search_enable_wildcards')
        ];

        $products = $search->AddSearchPart($products, $search_term, Product::class, $config);

        return $products;
    }

    protected function userQueryBuilderFilter($products, $user_hashed_id)
    {
        $user = User::findByHash($user_hashed_id);

        if (!$user) {
            abort(404);
        }

        $products = $products->where('classified_products.created_by', $user->id);

        return $products;

    }

    public function getActiveProductsCount($featured = false, $currentUser = false)
    {
        $products = Product::active();

        if ($featured) {
            $products = $products->featured();
        }

        if ($currentUser) {
            $products = $products->authUser();
        }

        return $products->count();
    }


    public function getActiveLocationsCount()
    {
        return Location::query()->active()->count();
    }

    public function getSoldProductsCount($currentUser = false)
    {
        $products = Product::ByStatus('sold');

        if ($currentUser) {
            $products = Product::ByStatus('sold')->authUser();
        }

        return $products->count();


    }

    public function getProductsCount($currentUser = false)
    {
        $products = Product::query();

        if ($currentUser) {
            $products = $products->authUser();
        }

        return $products->count();
    }

    public function getArchivedProductsCount($currentUser = false)
    {
        $products = Product::ByStatus('archived');

        if ($currentUser) {
            $products = $products->authUser();;
        }

        return $products->count();
    }

    public function getFeaturedProductsCount($currentUser)
    {

        $products = Product::featured();

        if ($currentUser) {
            $products = $products->authUser();
        }

        return $products->count();
    }


    public function getMyWishlistsCount()
    {
        $wishlist = new WishlistManager(new Product());

        $wishlists = $wishlist->getUserWishlist(true);

        return $wishlists;
    }

    protected function conditionQueryBuilderFilter($products, $condition)
    {
        $products = $products->ByCondition($condition);

        return $products;
    }

    public function getRandomProductForCategory($category, $count = 3)
    {
        $rand = array_rand($category);

        $random_category = $category[$rand];

        $category_id = Category::query()->where('slug', $random_category)->pluck('id');

        if (isset($category_id) && count($category_id) > 0) {
            $products = Product::query()->join('utility_model_has_category', 'classified_products.id', 'utility_model_has_category.model_id')
                ->where('utility_model_has_category.model_type', Product::class)
                ->where('utility_model_has_category.category_id', $category_id)->limit($count)->get();
            return $products;
        } else {
            return [];
        }
    }

    public function subscriptionActive()
    {
        return \Modules::isModuleActive('corals-subscriptions')
            && \Settings::get('classified_subscription_is_enable', false)
            && \Settings::get('classified_subscription_product_id')
            && \Settings::get('classified_allowed_products_count_feature_id')
            && \Settings::get('classified_allowed_featured_products_count_feature_id');
    }

    public function getUserSubscriptionStatus($user = null)
    {
        if (!$this->subscriptionActive()) {
            return null;
        }

        if (!$user) {
            $user = user();
        }

        $classifiedSubscriptionProductId = \Settings::get('classified_subscription_product_id');

        if ($user && $activeSubscription = $user->currentSubscription($classifiedSubscriptionProductId)) {
            $status = [];

            $productsCountFeatureId = \Settings::get('classified_allowed_products_count_feature_id');

            $productsCountFeature = $activeSubscription->plan->features()->where('feature_id', $productsCountFeatureId)->first();

            if ($productsCountFeature) {
                $limit = $productsCountFeature->pivot->value;
                $usage = Product::query()->where('user_id', $user->id)->count();
            } else {
                $limit = 0;
                $usage = 0;
            }

            $status['products_count']['limit'] = intval($limit);
            $status['products_count']['usage'] = $usage;
            $status['products_count']['limit_reached'] = $usage >= $limit;
            $status['products_count']['message'] = $status['products_count']['limit_reached'] ? trans('Classified::messages.products_count_limit_reached', ['count' => $limit, 'plan_name' => $activeSubscription->plan->name]) : '';

            $featuredProductsCountFeatureId = \Settings::get('classified_allowed_featured_products_count_feature_id');

            $featuredProductsCountFeature = $activeSubscription->plan->features()->where('feature_id', $featuredProductsCountFeatureId)->first();

            if ($featuredProductsCountFeature) {
                $limit = $featuredProductsCountFeature->pivot->value;
                $usage = Product::query()->featured()->where('user_id', $user->id)->count();
            } else {
                $limit = 0;
                $usage = 0;
            }

            $status['feature_products_count']['limit'] = intval($limit);
            $status['feature_products_count']['usage'] = $usage;
            $status['feature_products_count']['limit_reached'] = $usage >= $limit;
            $status['feature_products_count']['message'] = $status['feature_products_count']['limit_reached'] ? trans('Classified::messages.feature_products_count_limit_reached', ['count' => $limit, 'plan_name' => $activeSubscription->plan->name]) : '';

            return $status;
        }

        return null;
    }
}
