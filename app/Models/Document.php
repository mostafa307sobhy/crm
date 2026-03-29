<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Document extends Model
{
        use  LogsActivity;

    protected $fillable = [
        'name',
        'drive_url',
        'client_id' 
    ];
}
