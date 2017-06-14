<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/14
 * Time: 下午9:12
 */

namespace Ludwig;


interface ColumnInterface
{

    public function setValue($value);

    public function getValue();

    public function eq($data);

    public function neq($data);

    public function egt($data);

    public function gt($data);

    public function elt($data);

    public function lt($data);

    public function between($from,$to);

    public function in($array);

    public function not_in($array);

    public function like($need,$match_case);
}