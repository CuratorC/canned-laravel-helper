<?php

namespace CuratorC\CannedLaravelHelper\Models\Traits;

use DB;
use Log;

trait MiddleTableOperate
{
    protected string $modelFieldName = "model";
    protected string $idFieldName = "id";

    /**
     * 将本对象与目标对象绑定。
     * @param object|array $object 可接受参数：array:  ["model" => "AimModel", "id" => 12];
     *                                               ["model" => "AimModel", "id" => [10, 11, 12]];
     *                                       object: Model;
     *                                               Collection;
     * @param string|null $firstModelName 手动指定本体对象的模型名，如： ExhibitionHallImage
     * @param string|null $secondModelName 手动指定绑定对象的模型名，如： ResourceStorage
     * @return void
     */
    public function middleSync(object|array $object, string $firstModelName = null, string $secondModelName = null): void
    {
        [$table_name, $first_key, $second_key] = $this->getMiddleTableName($object, $firstModelName, $secondModelName);

        if ($table_name) {
            $class = "App\Models\Pivots\\" . create_big_camelize($table_name);
            // $query = DB::table($table_name);
            $query = new $class;

            // 创建模型
            $this->createForeachSecondModel($this, $object, $query, $first_key, $second_key);
        }
    }

    /**
     * 将本对象与目标对象绑定，并清除与目标对象同类型的其他对象
     * @param array|object $object 可接受参数：array:  ["model" => "AimModel", "id" => 12];
     *                                               ["model" => "AimModel", "id" => [10, 11, 12]];
     *                                       object: Model;
     *                                               Collection;
     * @param string|null $firstModelName
     * @param string|null $secondModelName
     * @return void
     */
    public function middleSyncAndDeleteAnother($object, string $firstModelName = null, string $secondModelName = null): void
    {
        [$table_name, $first_key, $second_key] = $this->getMiddleTableName($object, $firstModelName, $secondModelName);

        if ($table_name) {
            $class = "App\Models\Pivots\\" . create_big_camelize($table_name);
            // $query = DB::table($table_name);
            $query = new $class;

            // 删除掉不在 $object 中的其他关联
            $newIds = $this->getSecondValuesFromObject($object);
            $deleteList = $query->where($first_key, $this->id)->whereNotIn($second_key, $newIds)->get();
            $noOperationList = $query->where($first_key, $this->id)->whereIn($second_key, $newIds)->pluck("id");
            foreach ($deleteList as $item) {
                $item->delete();
            }

            // 创建模型
            $this->createForeachSecondModel($this, $object, $query, $first_key, $second_key, $noOperationList);
        }

    }

    /**
     * 将本对象与目标模型的关联全部清除。
     * @param array|object $object 可接受参数：array: ["model" => "AimModel"];
     *                                      object: new AimModel();
     * @param string|null $firstModelName
     * @param string|null $secondModelName
     * @return void
     */
    public function cleanSync($object, string $firstModelName = null, string $secondModelName = null): void
    {
        [$table_name, $first_key, $second_key] = $this->getMiddleTableName($object, $firstModelName, $secondModelName);
        if ($table_name) {
            $class = "App\Models\Pivots\\" . create_big_camelize($table_name);
            $query = new $class;

            $list = $query->where($first_key, $this->id)->get();
            foreach ($list as $item) {
                $item->delete();
            }
        }
    }

    /**
     * 将本对象与目标模型的关联删除。
     * @param array|object $object 可接受参数：array:  ["model" => "AimModel", "id" => 12];
     *                                               ["model" => "AimModel", "id" => [10, 11, 12]];
     *                                       object: Model;
     *                                               Collection;
     * @param string|null $firstModelName
     * @param string|null $secondModelName
     * @return void
     */
    public function deleteSync($object, string $firstModelName = null, string $secondModelName = null): void
    {
        [$table_name, $first_key, $second_key] = $this->getMiddleTableName($object, $firstModelName, $secondModelName);
        if ($table_name) {
            $class = "App\Models\Pivots\\" . create_big_camelize($table_name);

            $ids = $this->getSecondValuesFromObject($object);
            foreach ($ids as $id) {
                $query = new $class;
                $list = $query->where($first_key, $this->id)->where($second_key, $id)->get();
                foreach ($list as $item) {
                    $item->delete();
                }
            }
        }
    }

    /**
     * @description 为第二模型插入数据
     * @param $firstModel
     * @param $secondModel
     * @param $query
     * @param $first_key
     * @param $second_key
     * @param array $exceptSecondIds
     * @author CuratorC
     * @date 2021/3/4
     */
    private function createForeachSecondModel($firstModel, $secondModel, $query, $first_key, $second_key, array $exceptSecondIds = []): void
    {
        if (is_array($secondModel)) { // 数组键值对
            // 判断 id 有几个
            if (is_array($secondModel[$this->idFieldName])) {
                // 数组
                foreach ($secondModel[$this->idFieldName] as $id) {
                    if (!in_array($id, $exceptSecondIds)) $this->createMiddleTableData($query, $first_key, $firstModel->id, $second_key, $id);
                }
            } else {
                // 单个
                if (!in_array($secondModel[$this->idFieldName], $exceptSecondIds)) $this->createMiddleTableData($query, $first_key, $firstModel->id, $second_key, $secondModel[$this->idFieldName]);
            }
        } elseif (object_is_collection($secondModel)) {
            foreach ($secondModel as $item) { // 集合
                if (!in_array($item->id, $exceptSecondIds)) $this->createMiddleTableData($query, $first_key, $firstModel->id, $second_key, $item->id);
            }
        } else { // 模型
            if (!in_array($secondModel->id, $exceptSecondIds)) $this->createMiddleTableData($query, $first_key, $firstModel->id, $second_key, $secondModel->id);
        }
    }

    /**
     * @description 输入中间表数据
     * @param $query
     * @param $first_key
     * @param $first_value
     * @param $second_key
     * @param $second_value
     * @author CuratorC
     * @date 2021/3/4
     */
    private function createMiddleTableData($query, $first_key, $first_value, $second_key, $second_value): void
    {
        $query->firstOrCreate([$first_key => $first_value, $second_key => $second_value]);
    }

    /**
     * @description 获取中间表名称
     * @param $object
     * @param $firstModelName
     * @param $secondModelName
     * @return array
     * @author CuratorC
     * @date 2021/3/4
     */
    private function getMiddleTableName($object, $firstModelName, $secondModelName): array
    {
        $firstModelName = create_under_score($firstModelName ?? $this->getModelName($this));
        $secondModelName = create_under_score($secondModelName ?? $this->getModelName($object));
        if ($firstModelName && $secondModelName) {
            $modelArray = [$firstModelName, $secondModelName];
            sort($modelArray);
            // 判断前半部分完全匹配的话，调转排序 - laravel 的排序特性
            $lengthOne = strlen($modelArray[0]);
            $lengthTwo = strlen($modelArray[1]);
            if ($lengthTwo > $lengthOne) {
                $substr = substr($modelArray[1], 0, $lengthOne);
                if ($substr === $modelArray[0]) {
                    $temp = $modelArray[0];
                    $modelArray[0] = $modelArray[1];
                    $modelArray[1] = $temp;
                }
            }

            return [$modelArray[0] . "_" . $modelArray[1], $firstModelName . "_id", $secondModelName . "_id"];
        } else {
            return [false, false, false];
        }
    }

    /**
     * @description 获取对象的模块名称
     * @param $model
     * @return string
     * @author CuratorC
     * @date 2021/3/4
     */
    private function getModelName($model): string
    {
        if (is_array($model)) return str_replace("\\", "", str_replace("App\Models\\", "", $model[$this->modelFieldName]));
        else if (object_is_collection($model)) return $this->getModelName($model[0]);
        else return str_replace("\\", "", str_replace("App\Models\\", "", get_class($model)));
    }

    /**
     * 从对象中获取第二模型的 id 列表
     * @param $object
     * @return array
     */
    private function getSecondValuesFromObject($object): array
    {
        if (is_array($object)) { // 数组键值对
            if (is_array($object[$this->idFieldName])) return $object[$this->idFieldName];
            else return [$object[$this->idFieldName]];
        } elseif (object_is_collection($object)) {
            $ids = array();
            foreach ($object as $item) { // 集合
                $ids[] = $item->id;
            }
            return $ids;
        } else { // 模型
            return [$object->id];
        }
    }
}
