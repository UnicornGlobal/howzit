<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'form_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function responseElements()
    {
        return $this->hasMany(ResponseElement::class);
    }
}
