<?php
namespace App\Models\Pivots;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\Pivot AS BasicPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Pivots\Pivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot newQuery()
 * @method static \Illuminate\Database\Query\Builder|Pivot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot query()
 * @method static \Illuminate\Database\Query\Builder|Pivot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Pivot withoutTrashed()
 * @mixin Eloquent
 */
class Pivot extends BasicPivot
{
    use SoftDeletes;
}
