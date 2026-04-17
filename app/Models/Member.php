<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Member extends Model
{
    use Notifiable;
    protected $fillable = [
        'first_name', 'last_name', 'birth_date', 'gender', 'street', 'postcode', 'location', 'phone',
        'email', 'aggrement_1', 'aggreement_2', 'validation_code', 'is_verified'
    ];

}
