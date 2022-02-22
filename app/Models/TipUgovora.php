<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TipUgovora
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TipUgovora newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipUgovora newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipUgovora query()
 * @mixin \Eloquent
 */
class TipUgovora extends Model
{
    use HasFactory;

    protected $table = "tip_ugovora";

    protected $guarded = ['id'];
}
