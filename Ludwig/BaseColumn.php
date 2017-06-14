<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/14
 * Time: 下午9:16
 */

namespace Ludwig;


class BaseColumn implements ColumnInterface
{

    protected $value;

    public function getValue(){
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function eq($data){
        return $this->value == $data;
    }

    public function neq($data){
        return $this->value != $data;
    }

    public function egt($data){
        return $this->value >= $data;
    }

    public function gt($data){
        return $this->value > $data;
    }

    public function elt($data){
        return $this->value <= $data;
    }

    public function lt($data){
        return $this->value < $data;
    }

    public function between($from,$to){
        return $this->value >= $from && $this->value <= $to;
    }

    public function in($array){
        return in_array($this->value,$array);
    }

    public function not_in($array){
        return !$this->in($array);
    }

    public function like($need,$match_case = true){
        return $match_case ? strpos($this->value,$need) !== false : strpos(strtolower($this->value),strtolower($need)) !== false;
    }

}