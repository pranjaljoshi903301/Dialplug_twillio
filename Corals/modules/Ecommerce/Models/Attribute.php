<?php

namespace Corals\Modules\Ecommerce\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\Foundation\Traits\Node\SimpleNode;
use Spatie\Activitylog\Traits\LogsActivity;

class Attribute extends BaseModel
{
    use PresentableTrait, LogsActivity, SimpleNode;

    protected $table = 'ecommerce_attributes';

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'ecommerce.models.attribute';


    protected $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'ecommerce_category_attributes');
    }
}
