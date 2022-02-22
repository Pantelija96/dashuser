<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TehnologijeUgovor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TehnologijeUgovor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TehnologijeUgovor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TehnologijeUgovor query()
 * @mixin \Eloquent
 */
class TehnologijeUgovor extends Model
{
    use HasFactory;

    protected $table = "tehnologije_ugovor";

    protected $guarded = ['id', 'id_tehnologije', 'id_ugovor'];
}
