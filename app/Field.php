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
        'created_by',
        'updated_by',
        'deleted_by',
        'form_id',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}