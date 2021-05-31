<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fb_user extends Model
{
    public $timestamps = false;
    public $table="fb_user";
    protected $fillable=[
        'name','email','fbID'
    ];
}
