<?php

namespace Corals\Modules\Ecommerce\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\Foundation\Traits\Node\SimpleNode;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Category extends BaseModel implements HasMedia
{
    use PresentableTrait, LogsActivity, HasMediaTrait, SimpleNode;

    protected $table = 'ecommerce_categories';

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    public $mediaCollectionName = 'ecommerce-category-thumbnail';
    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'ecommerce.models.category';

    protected static $logAttributes = ['name', 'slug'];

    protected $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'ecommerce_category_product');
    }

    public function scopeActive($query)
    {
        return $query->where('ecommerce_categories.status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('ecommerce_categories.is_featured', true);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = \Str::slug($value);
    }

    public function categoryAttributes()
    {
        return $this->belongsToMany(Attribute::class, 'ecommerce_category_attributes', 'category_id');
    }
}
