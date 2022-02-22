<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tehnologije
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tehnologije newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tehnologije newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tehnologije query()
 * @mixin \Eloquent
 */
class Tehnologije extends Model
{
    use HasFactory;

    protected $table = "tehnologije";

    protected $guarded = ['id'];
}
