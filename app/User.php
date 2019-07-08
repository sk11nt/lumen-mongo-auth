<?php
declare (strict_types=1);

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'username', 'email', 'activation_code',

    ];

    protected $hidden = [
        'password',
    ];

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }


}
