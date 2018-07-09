<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class Credentials extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;
}