<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'user_id',
        'user_agent',
        'used_at',
    ];

    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}