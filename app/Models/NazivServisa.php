<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NazivServisa
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NazivServisa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NazivServisa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NazivServisa query()
 * @mixin \Eloquent
 */
class NazivServisa extends Model
{
    use HasFactory;

    protected $table = "naziv_servisa";

    protected $guarded = ['id'];
}
