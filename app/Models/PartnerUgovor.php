<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PartnerUgovor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerUgovor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerUgovor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerUgovor query()
 * @mixin \Eloquent
 */
class PartnerUgovor extends Model
{
    use HasFactory;

    protected $table = "partner_ugovor";

    protected $guarded = ['id', 'id_partner', 'id_ugovor'];
}
