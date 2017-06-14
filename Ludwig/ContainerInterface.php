<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/14
 * Time: 下午9:30
 */

namespace Ludwig;


use Inject\InjectorInterface;

interface ContainerInterface extends InjectorInterface
{

    public function flush();

    public function mapData($k,$v);

}