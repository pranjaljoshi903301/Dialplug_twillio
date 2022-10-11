<?php

namespace Corals\Modules\Ecommerce\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Brand extends BaseModel implements HasMedia
{
    use PresentableTrait, LogsActivity, HasMediaTrait;

    protected $table = 'ecommerce_brands';
    public $mediaCollectionName = 'ecommerce-brand-thumbnail';

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'ecommerce.models.brand';

    protected static $logAttributes = ['name'];

    protected $guarded = ['id'];

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('ecommerce_brands.status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('ecommerce_brands.is_featured', true);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = \Str::slug($value);
    }

    public function categories()
    {
        return $this->hasManyDeep(Category::class, [Product::class, 'ecommerce_category_product']);
    }
}
