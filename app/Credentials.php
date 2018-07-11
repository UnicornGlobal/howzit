<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Credentials extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $hidden = [
        'id',
        'user_id',
        'provider_id',
        'secret',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
