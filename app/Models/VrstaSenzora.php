<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VrstaSenzora
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzora newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzora newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzora query()
 * @mixin \Eloquent
 */
class VrstaSenzora extends Model
{
    use HasFactory;

    protected $table = "vrsta_senzora";

    protected $guarded = ['id'];
}
