<?php

namespace App\Models;

use CuratorC\CannedLaravelHelper\Models\Traits\MiddleTableOperate;
use CuratorC\CannedLaravelHelper\Models\Traits\ScopeCannedSearch;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedIdInPath(int $id, string $field = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedNameInPath(string $name, string $className, string $nameField = 'name', string $pathField = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedOrder(...$orderRules)
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedPaginate(bool $ableAll = true, int $defaultSize = 10)
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedWhen(string $column, $func)
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedWhenKeyword()
 * @method static \Illuminate\Database\Eloquent\Builder|Model cannedWhereDate($field)
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Query\Builder|Model onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @method static \Illuminate\Database\Query\Builder|Model withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Model withoutTrashed()
 * @mixin Eloquent
 */
class Model extends EloquentModel
{
    use HasFactory; // 模型工厂
    use SoftDeletes; // 软删除
    use ScopeCoderSearch; // scope 查询
    use MiddleTableOperate; // 中间表操作
}

