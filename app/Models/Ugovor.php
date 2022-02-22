<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ugovor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Ugovor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ugovor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ugovor query()
 * @mixin \Eloquent
 */
class Ugovor extends Model
{
    use HasFactory;

    protected $table = "ugovor";

    protected $guarded = ['id'];
}
