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

    /**
     * @param $values
     * @return array|mixed
     */
    public function apply($values){
        $result = [];
        if (is_array($values)){
            foreach ($values as $key => $info){
                if ($info['type'] == Ludwig::APPLY_VALUE_TYPE_MAGIC){
                    /* @var $value ValueInterface */
                    $value = $this->getInjector()->produce($info['value']);
                    $result[$key] = $value->getValue();
                }else{
                    $result[$key] = $info['value'];
                }
            }
        }else{
            /* @var $value ValueInterface */
            $value = $this->getInjector()->produce($values);
            $result = $value->getValue();
        }
        return $result;
    }
}