<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TipServisa
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TipServisa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipServisa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipServisa query()
 * @mixin \Eloquent
 */
class TipServisa extends Model
{
    use HasFactory;

    protected $table = "tip_servisa";

    protected $guarded = ['id'];
}
