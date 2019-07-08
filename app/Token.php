<?php
declare (strict_types=1);

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Token extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'token', 'expires_at',
    ];

    protected $hidden = [
        'user_id', '_id', 'created_at', 'updated_at',
    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
