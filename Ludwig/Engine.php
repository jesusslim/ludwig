<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/14
 * Time: 下午9:24
 */

namespace Ludwig;

class Engine
{

    const CONDITION_TYPE_COMMON = 1;
    const CONDITION_TYPE_LOGIC = 2;

    const LOGIC_AND = 1;
    const LOGIC_OR = 2;

    const CONDITION_VALUE_TYPE_COMMON = 1;
    const CONDITION_VALUE_TYPE_MAGIC = 2;

    protected $injector;

    public function __construct()
    {
        $this->injector = new Container();
    }

    /**
     * @return ContainerInterface
     */
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * @param ContainerInterface $injector
     */
    public function setInjector($injector)
    {
        $this->injector = $injector;
    }

    /**
     * @param $column
     * @param $func
     * @param array $condition_values
     * @return mixed
     * @throws LudwigException
     */
    public function is($column,$func,$condition_values = []){
//        if (!method_exists($column,$func)) throw new LudwigException("$func undefined");
        return $this->getInjector()->callInClass($column,$func,$this->walk($condition_values));
    }

    /**
     * @param $condition_values
     * @return array
     */
    public function walk($condition_values){
        $r = [];
        foreach ($condition_values as $key => $info){
            if ($info['type'] == self::CONDITION_VALUE_TYPE_MAGIC){
                /* @var $value_column ColumnInterface */
                $value_column = $this->getInjector()->produce($info['value']);
                $r[$key] = $value_column->getValue();
            }else{
                $r[$key] = $info['value'];
            }
        }
        return $r;
    }

    /**
     * run
     * @param $conditions
     * @param int $logic
     * @return bool
     */
    public function run($conditions,$logic = self::LOGIC_AND){
        $r = false;
        foreach ($conditions as $condition){
            if ($condition['condition_type'] == self::CONDITION_TYPE_LOGIC){
                $r = $this->run($condition['sons'],$condition['logic']);
            }else{
                $r = $this->is($condition['column'],$condition['func'],$condition['condition_values']);
            }
            if ($logic == self::LOGIC_AND && $r === false) return false;
            if ($logic == self::LOGIC_OR && $r === true) return true;
        }
        return $r;
    }
}