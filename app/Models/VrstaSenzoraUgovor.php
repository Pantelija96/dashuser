<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VrstaSenzoraUgovor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzoraUgovor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzoraUgovor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VrstaSenzoraUgovor query()
 * @mixin \Eloquent
 */
class VrstaSenzoraUgovor extends Model
{
    use HasFactory;

    protected $table = "vrsta_senzora_ugovor";

    protected $guarded = ['id', 'id_vrsta_senzora', 'id_ugovor'];
}
