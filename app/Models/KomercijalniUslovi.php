<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KomercijalniUslovi
 *
 * @method static \Illuminate\Database\Eloquent\Builder|KomercijalniUslovi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KomercijalniUslovi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KomercijalniUslovi query()
 * @mixin \Eloquent
 */
class KomercijalniUslovi extends Model
{
    use HasFactory;

    protected $table = "komercijalni_uslovi";

    protected $guarded = ['id'];


}
