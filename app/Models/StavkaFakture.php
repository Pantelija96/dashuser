<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StavkaFakture
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StavkaFakture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StavkaFakture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StavkaFakture query()
 * @mixin \Eloquent
 */
class StavkaFakture extends Model
{
    use HasFactory;

    protected $table = "stavka_fakture";

    protected $guarded = ['id'];
}
