<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LokacijaApp
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LokacijaApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LokacijaApp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LokacijaApp query()
 * @mixin \Eloquent
 */
class LokacijaApp extends Model
{
    use HasFactory;

    protected $table = "lokacija_app";

    protected $guarded = ['id'];
}
