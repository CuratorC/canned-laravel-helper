<?php

namespace CuratorC\CannedLaravelHelper\Models;

use JetBrains\PhpStorm\ArrayShape;
use Storage;

/**
 * App\Models\ResourceStorage
 *
 * @method static \Illuminate\Database\Eloquent\Builder where(mixed $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static ResourceStorage create(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder coderIdInPath(string $id, string $field = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder coderNameInPath(string $name, string $modelName, string $nameField = 'name', string $pathField = 'path')
 * @method static \Illuminate\Database\Eloquent\Builder coderOrder(...$orderRules)
 * @method static \Illuminate\Database\Eloquent\Builder coderPaginate()
 * @method static \Illuminate\Database\Eloquent\Builder coderWhen($field, $func)
 * @method static \Illuminate\Database\Eloquent\Builder coderWhenKeyword()
 * @method static \Illuminate\Database\Eloquent\Builder coderWhereDate($field)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceStorage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceStorage newQuery()
 * @method static \Illuminate\Database\Query\Builder|ResourceStorage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceStorage query()
 * @method static \Illuminate\Database\Query\Builder|ResourceStorage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ResourceStorage withoutTrashed()
 * @mixin \Eloquent
 */
class ResourceStorage extends Model
{
    protected $fillable = ['user_id', 'name', 'used', 'used_model_type', 'used_model_id', 'used_model_field'];

    const USED_TRUE_CODE = 1;
    const USED_FALSE_CODE = 9;
    #[ArrayShape([self::USED_TRUE_CODE => "string", self::USED_FALSE_CODE => "string"])] public static function getUsedName(): array
    {
        return [
            self::USED_TRUE_CODE    => '使用中',
            self::USED_FALSE_CODE   => '未使用',
        ];
    }

    /**
     * @description 获取名称
     * @param $path
     * @return mixed
     * @author CuratorC
     * @date 2021/3/10
     */
    public function new($path)
    {
        do {
            $name = $path . '/' . get_str_random(15);
            $occupy = self::where('name', $name)->first();
        } while ($occupy);

        // 创建信息
        return self::create([
            'name'  => $name,
            'user_id'   => Auth::id() ?? 0
        ]);
    }

    /**
     * @description 删除未引用的资源
     * @author CuratorC
     * @date 2021/3/10
     */
    public function forceDeleteStorage(): bool
    {
        if ($this->used == self::USED_TRUE_CODE) return false;
        $disk = Storage::disk('oss');
        // 删除 oss 上面的文件
        if ($disk->has($this->name)) $disk->delete($this->name);
        $this->forceDelete();
        return true;
    }
}
