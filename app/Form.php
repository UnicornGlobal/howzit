<?php

namespace App;


use Fields;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'user',
    ];

    public function fields()
    {
        return $this->hasMany(Field::class)->orderBy('order_index');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
