<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
    ];
}
