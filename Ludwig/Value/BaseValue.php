<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/17
 * Time: 下午2:22
 */

namespace Ludwig\Value;


class BaseValue implements ValueInterface
{

    protected $value;

    public function getValue()
    {
        return $this->value;
    }

}