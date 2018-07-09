<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;
}