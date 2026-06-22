<?php

namespace App\Models;

use Database\Factories\MigrationDbFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigrationDb extends Model
{
    /** @use HasFactory<MigrationDbFactory> */
    use HasFactory;

    public $guarded = ['id'];
}
