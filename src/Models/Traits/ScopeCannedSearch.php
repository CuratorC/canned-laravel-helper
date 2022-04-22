<?php

namespace CuratorC\CannedLaravelHelper\Models\Traits;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 * @method static Builder CannedOrder()
 */
trait ScopeCannedSearch
{

    protected FormRequest $searchRequest;

    // 查询字段记录
    private array $searchArray = array();

    /**
     * @description 单条查询
     * $query->cannedWhen('field', function(Builder $query, string $value){
     *     $query->...
     * });
     * @param Builder $query
     * @param string $field
     * @param $func
     * @return Builder
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedWhen(Builder $query, string $field, $func): Builder
    {
        $query->when($this->searchRequest->$field, function ($query) use ($func, $field) {
            $func($query, $this->searchRequest->$field);
        });
        $this->searchArray[] = ['field' => $field, 'func' => $func];

        return $query;
    }

    /**
     * @description 为 keyword 匹配所有查询条件查询
     * $query->cannedWhenKeyword();
     * @param Builder $query
     * @return Builder
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedWhenKeyword(Builder $query): Builder
    {
        return $query->when('keyword', function ($query) {
            foreach ($this->searchArray as $search) {
                $func = $search['func'];
                $field = $search['field'];
                $func($query, $this->searchRequest->$field);
            }
        });
    }

    /**
     * @description order 排序
     * $query->cannedOrder(['created_at', 'DESC'], ['id', 'ASC']);
     * @param Builder $query
     * @param mixed ...$orderRules
     * @return Builder
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedOrder(Builder $query, ...$orderRules): Builder
    {
        // 检查用户自带参数
        $query->when($this->searchRequest->field, function ($query) {
            // 补全 order 参数、
            if (empty($this->searchRequest->order)) $this->searchRequest->order = 'ASC';
            $query->orderBy($this->searchRequest->field, $this->searchRequest->order);
        });

        // 循环程序自定义参数
        foreach ($orderRules as $orderRule) {
            if (is_string($orderRule)) $query->orderBy($orderRule);
            if (is_array($orderRule)) $query->orderBy($orderRule[0], $orderRule[1]);
        }

        // 追加固定排序
        return $query->orderBy('id', 'DESC');
    }


    /**
     *ㅤ分页，从 request 中自动取 page, size 两个参数进行判断。
     *
     * page: 页码
     * 当 ableAll 为 true 时:
     *       若 request 中存在 page 则分页，不存在则返回全部。
     * 当 ableAll 为 false 时:
     *       若 request 中存在 page 则分页，不存在则返回第一页。
     *
     * size: 每页长度，默认 10
     *
     * $query->cannedPaginate(false);
     * @param $query
     * @param bool $ableAll
     * @param int $defaultSize
     * @return Builder
     * @date 2020/10/13
     * @author Curator
     */
    public function scopeCannedPaginate($query, bool $ableAll = true, int $defaultSize = 10): Builder
    {
        if (empty($this->searchRequest->page) && $ableAll) return $query->get();
        if (empty($this->searchRequest->size)) $this->searchRequest->size = $defaultSize;
        return $query->paginate($this->searchRequest->size);
    }


    /**
     * @description 日期筛选
     * $query->cannedWhereData('field')
     * field 自动匹配 request 中的值，该字段与要查询的模型的 field 一致。
     * 日期格式是一个范围，需符合： yyyy-MM-dd - yyyy-MM-dd
     * @param $query
     * @param $field
     * @return mixed
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedWhereDate($query, $field): Builder
    {
        if (preg_match("/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) - [1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->searchRequest->$field)) {
            $timeArray = explode(' - ', $this->searchRequest->$field);
            // 结束日期加一天
            $endDay = Carbon::create($timeArray[1])->addDay();
            $query->where($field, '>=', $timeArray[0])->where($field, '<=', $endDay);
        }
        return $query;
    }


    /**
     * @description 解析 path 按 id 查找父级
     * $query->cannedIdInPath($id, 'path');
     * path 是 模型中 路径字段的字段名，字段值是一个 id 的面包屑导航，需符合格式：1-2-3-4...
     * @param Builder $query
     * @param int $id
     * @param string $field
     * @return Builder
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedIdInPath(Builder $query, int $id, string $field = 'path'): Builder
    {
        return $query->where(function ($query) use ($id, $field) {
            $query->where('id', $id)
                ->orWhere($field, 'like', '%-' . $id . '-%')
                ->orWhere($field, 'like', $id . '-%')
                ->orWhere($field, 'like', '%-' . $id)
                ->orWhere($field, $id);
        });
    }

    /**
     * @description 解析 path 按名称查找父级
     * $query->cannedNameInPath($name, User::class, 'name', 'path');
     * $nameField 是 模型中要查找值对应的字段名。
     * @param Builder $query
     * @param string $name
     * @param string $className
     * @param string $nameField
     * @param string $pathField
     * @return Builder
     * @author CuratorC
     * @date 2021/3/1
     */
    public function scopeCannedNameInPath(Builder $query, string $name, string $className, string $nameField = 'name', string $pathField = 'path'): Builder
    {
        // 先将 name 转换为 model, 然后根据 model->id 使用 scopeCannedIdInPath
        $models = $className::where($nameField, 'like', '%' . $name . '%')->get();
        return $query->where(function ($query) use ($models, $pathField) {
            foreach ($models as $model) {
                $query->orWhere(function ($query) use ($model, $pathField) {
                    $query->CannedIdInPath($model->id, $pathField);
                });
            }
        });
    }
}
