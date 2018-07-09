<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;
}