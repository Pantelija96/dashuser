<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Uloga
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Uloga newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Uloga newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Uloga query()
 * @mixin \Eloquent
 */
class Uloga extends Model
{
    use HasFactory;

    protected $table = "uloga";

    protected $guarded = ['id'];
}
