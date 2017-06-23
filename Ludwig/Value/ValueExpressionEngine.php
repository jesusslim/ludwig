<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/23
 * Time: 上午10:48
 */

namespace Ludwig\Value;


use Ludwig\Ludwig;

class ValueExpressionEngine
{

    /**
     * 允许的Value类map
     * @var array
     */
    protected $values_map;

    /**
     * ValueExpressionEngine constructor.
     * @param array $values_map
     */
    public function __construct($values_map = [])
    {
        $this->values_map = $values_map;
    }

    /**
     * @param $value
     * @return array
     */
    protected function returnValueConvert($value){
        $type = Ludwig::APPLY_VALUE_TYPE_COMMON;
        if ($value[0] == '$'){
            $key = substr($value,1);
            if (isset($this->values_map[$key])) {
                $type = Ludwig::APPLY_VALUE_TYPE_MAGIC;
                $value = $this->values_map[$key];
            }
        }
        return compact('type','value');
    }

    /**
     * @param $str
     * @param string $split_character
     * @return array
     */
    public function convert($str,$split_character = ','){
        $result = [];
        $kvs = explode($split_character,$str);
        foreach ($kvs as $kv){
            $kv_arr = explode('=',$kv);
            if (isset($kv_arr[1])){
                $result[$kv_arr[0]] = $this->returnValueConvert($kv_arr[1]);
            }
        }
        return $result;
    }
}