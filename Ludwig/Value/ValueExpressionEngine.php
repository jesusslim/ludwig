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
     * 分隔符
     * @var string
     */
    protected $separator;

    /**
     * 字符替换map
     * @var array
     */
    protected $replace_map;

    /**
     * ValueExpressionEngine constructor.
     * @param array $values_map
     * @param string $separator
     * @param array $replace_map
     */
    public function __construct($values_map = [],$separator = ' ',$replace_map = [])
    {
        $this->values_map = $values_map;
        $this->separator = $separator;
        $this->replace_map = $replace_map;
    }

    /**
     * @return array
     */
    public function getValuesMap()
    {
        return $this->values_map;
    }

    /**
     * @param array $values_map
     */
    public function setValuesMap($values_map)
    {
        $this->values_map = $values_map;
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @return array
     */
    public function getReplaceMap()
    {
        return $this->replace_map;
    }

    /**
     * @param array $replace_map
     */
    public function setReplaceMap($replace_map)
    {
        $this->replace_map = $replace_map;
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
     * @return array
     */
    public function convert($str){
        $result = [];
        $kvs = explode($this->separator,$str);
        foreach ($kvs as $kv){
            $kv_arr = explode('=',$kv);
            if (isset($kv_arr[1])){
                $value = $kv_arr[1];
                foreach ($this->replace_map as $k => $rep){
                    $value = str_replace($k,$rep,$value);
                }
                $result[$kv_arr[0]] = $this->returnValueConvert($value);
            }
        }
        return $result;
    }
}