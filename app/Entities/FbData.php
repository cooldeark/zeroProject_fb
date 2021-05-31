<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
// use Illuminate\Database\Eloquent\SoftDeletes;//add by me
/**
 * Class FbData.
 *
 * @package namespace App\Entities;
 */
class FbData extends Model implements Transformable
{
    use TransformableTrait;
    // use SoftDeletes;//add by me

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = ['name','email','fbID'];

}
