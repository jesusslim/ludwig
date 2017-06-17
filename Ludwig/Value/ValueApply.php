<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/17
 * Time: 下午2:27
 */

namespace Ludwig\Value;


use Ludwig\Container;
use Ludwig\ContainerInterface;
use Ludwig\Ludwig;

class ValueApply
{

    protected $injector;

    public function __construct(Container $container = null)
    {
        $this->injector = $container ? $container : new Container();
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

    public function apply($values){
        $result = [];
        foreach ($values as $key => $info){
            if ($info['type'] == Ludwig::APPLY_VALUE_TYPE_MAGIC){
                /* @var $value_column ValueInterface */
                $value_column = $this->getInjector()->produce($info['value']);
                $result[$key] = $value_column->getValue();
            }else{
                $result[$key] = $info['value'];
            }
        }
        return $result;
    }
}