<?php

namespace DigitalCloud\PageBuilderField;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nova_page_builder_assets';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
