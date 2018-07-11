<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;

class ResponseElement extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'field_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function response()
    {
        return $this->belongsTo(Response::class);
    }
}