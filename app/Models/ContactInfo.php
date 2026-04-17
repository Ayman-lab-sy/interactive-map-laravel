<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ContactInfo extends Model
{
    protected $table = "contact_info";

    protected $fillable = ['name', 'value', 'type', 'in_home', 'in_footer', 'icon'];

}
