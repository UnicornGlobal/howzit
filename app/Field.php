<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'deleted_at',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}