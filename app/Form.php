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
        'deleted_at',
        'credentials_id',
    ];

    public function fields()
    {
        return $this->hasMany(Fields::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }
}