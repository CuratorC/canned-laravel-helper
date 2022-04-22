<?php

namespace CuratorC\CannedLaravelHelper\Models;

use CuratorC\CannedLaravelHelper\Models\Traits\MiddleTableOperate;
use CuratorC\CannedLaravelHelper\Models\Traits\ScopeCannedSearch;
use Eloquent;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderIdInPath(string $id, string $field = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderNameInPath(string $name, string $modelName, string $nameField = 'name', string $pathField = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderOrder(...$orderRules)
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderPaginate()
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderWhen($field, $func)
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderWhenKeyword()
 * @method static \Illuminate\Database\Eloquent\Builder|Model coderWhereDate($field)
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
    use ScopeCannedSearch; // scope 查询
    use MiddleTableOperate; // 中间表操作
}

