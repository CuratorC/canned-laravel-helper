<?php

use Illuminate\Http\JsonResponse;


/**
 * 返回错误信息
 * @param $error
 * @param int $status
 * @return JsonResponse
 */
function canned_response_error($error, int $status = 422): JsonResponse
{
    if (is_string($error) && is_array(json_decode($error, true))) $error = json_decode($error, true);

    if (is_array($error)) {
        if (is_double_array($error)) $errors = $error;
        else $errors = ['error' => $error];
    } else {
        $errors = ['error' => [$error]];
    }

    // 创建 message
    $message = create_message_by_errors($errors);

    return response()->json(compact('message', 'errors'), $status);
}

/**
 * 生成随机字符串
 * @param int $length 长度
 * @return string 字符串
 */
function get_str_random(int $length = 6): string
{
    return get_random($length, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * @description 产生随机字符串
 * @param int $length
 * @param string $chars
 * @return string
 * @author CuratorC
 * @date 2021/2/26
 */
function get_random(int $length, string $chars = '0123456789'): string
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * @description 格式化金额
 * @param $obj
 * @return string
 * @author CuratorC
 * @date 2021/2/26
 */
function format_money($obj): string
{
    return number_format($obj, 2);
}

/**
 * @description 格式化数字
 * @param $decimal
 * @return float
 * @author CuratorC
 * @date 2021/2/26
 */
function format_decimal($decimal): float
{
    return round($decimal, 2);
}

/**
 * @description 格式化显示手机
 * @param $phone
 * @return string
 * @author CuratorC
 * @date 2021/2/26
 */
function format_show_phone($phone): string
{
    if (strlen($phone) == 11) {
        return substr($phone, 0, 3) . ' ' . substr($phone, 3, 4) . ' ' . substr($phone, 7);
    } else {
        return $phone;
    }
}

/**
 * @description 格式化隐藏手机
 * @param $phone
 * @return string
 * @author CuratorC
 * @date 2021/2/26
 */
function format_hidden_phone($phone): string
{
    if (strlen($phone) == 11) {
        return substr($phone, 0, 3) . ' **** ' . substr($phone, 7);
    } else {
        return substr($phone, 0, 3) . '???';
    }
}

/**
 * @description 金额去逗号
 * @param string $moneyString
 * @return float
 * @author CuratorC
 * @date 2021/2/26
 */
function replace_money(string $moneyString): float
{
    return (float)str_replace(',', '', $moneyString);
}

/**
 * @description YYYY-MM-dd HH:ii:ss 转 YYYY/MM/dd
 * @param string $datetimeString
 * @return string
 * @author CuratorC
 * @date 2021/2/26
 */
function datetime_to_date(string $datetimeString): string
{
    $result = explode(' ', $datetimeString);
    if (is_array($result)) return $result[0];
    else return $result;
}

/**
 * @description 将多维数组转换为一维数组
 * @param $arr
 * @param array $return
 * @return array
 * @author CuratorC
 * @date 2021/2/5
 */
function array_to_dimension($arr, array $return = []): array
{
    if (is_dimensions($arr)) {
        // 是多维数组，对其中每一位都调用变换
        foreach ($arr as $item) {
            $return = array_to_dimension($item, $return);
        }
    } else {
        // 不是多维，将 arr 并入 return ,返回 return
        $return = array_merge($return, $arr);
    }
    return $return;
}

/**
 * @description 是否为多维数组
 * @param $arr
 * @return bool
 * @author CuratorC
 * @date 2021/2/5
 */
function is_dimensions($arr): bool
{
    return !(count($arr) == count($arr, 1));
}

/**
 * @description 驼峰转下划线
 * @param $camelCaps
 * @param string $separator
 * @return string
 * @author CuratorC
 * @date 2021/3/4
 */
function create_under_score($camelCaps, string $separator = '_'): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}


/**
 * 下划线转驼峰
 * @param $words
 * @param string $separator
 * @return string
 */
function create_camelize($words, string $separator = '_'): string
{
    $words = $separator . str_replace($separator, " ", strtolower($words));
    return ltrim(str_replace(" ", "", ucwords($words)), $separator);
}

function create_big_camelize($words, $separator = '_'): string
{
    return ucwords(create_camelize($words, $separator));
}


/**
 * @description 对象是否为集合
 * @param $object
 * @return bool
 * @author CuratorC
 * @date 2021/3/4
 */
function object_is_collection($object): bool
{
    return in_array(
        get_class($object),
        [
            Illuminate\Support\Collection::class,
            Illuminate\Database\Eloquent\Collection::class,
        ]
    );
}

/**
 *ㅤ是二位数组
 * @param $array
 * @return bool
 * @date 2020/10/20
 * @author CuratorC
 */
function is_double_array($array): bool
{
    if (is_array($array)) {
        foreach ($array as $item) {
            if (is_array($item)) return true;
        }
    }
    return false;
}

/**
 *ㅤ根据 exception 中的 errors 创建 message
 * @param $errors
 * @return string
 * @date 2020/10/20
 * @author CuratorC
 */
function create_message_by_errors($errors): string
{
    $errorMessage = [];
    foreach ($errors as $error) {
        foreach ($error as $item) {
            $errorMessage[] = $item;
        }
    }
    return implode(", ", $errorMessage);
}

/**
 * 序列化组合
 * @param $collections
 * @param array $replace
 * @return string
 */
function serialize_collections($collections, array $replace = array()) : string
{
    $serializeData = serialize($collections);
    foreach ($replace as $key => $value) {
        $serializeData = str_replace($key, $value, $serializeData);
    }
    return $serializeData;
}
