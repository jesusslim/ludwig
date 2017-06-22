<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 2017/6/22
 * Time: 下午5:17
 */

namespace Ludwig;

/**
 * 表达式解析
 * Class ExpressionEngine
 * @package Ludwig
 */
class ExpressionEngine
{

    const OP_BRACKET_LEFT = '(';
    const OP_BRACKET_RIGHT = ')';
    const OP_GT = '>';
    const OP_LT = '<';
    const OP_EGT = '>=';
    const OP_ELT = '<=';
    const OP_IN = 'in';
    const OP_NOT_IN = 'notin';
    const OP_BETWEEN = 'between';
    const OP_LIKE = 'like';
    const OP_EQ = '=';
    const OP_NEQ = '!=';
    const OP_AND = '&&';
    const OP_OR = '||';

    /**
     * 运算符优先级
     * @var array
     */
    protected $ops_levels = [
        self::OP_BRACKET_LEFT  => 1,
        self::OP_BRACKET_RIGHT => 1,
        self::OP_AND           => 2,
        self::OP_OR            => 3,
        self::OP_GT            => 6,
        self::OP_LT            => 6,
        self::OP_EGT           => 6,
        self::OP_ELT           => 6,
        self::OP_IN            => 6,
        self::OP_NOT_IN        => 6,
        self::OP_BETWEEN       => 6,
        self::OP_LIKE          => 6,
        self::OP_EQ            => 7,
        self::OP_NEQ           => 7
    ];

    /**
     * 运算符对应的Column Interface方法
     * @var array
     */
    protected $funcs = [
        self::OP_GT      => 'gt',
        self::OP_LT      => 'lt',
        self::OP_EGT     => 'egt',
        self::OP_ELT     => 'elt',
        self::OP_IN      => 'in',
        self::OP_NOT_IN  => 'not_in',
        self::OP_BETWEEN => 'between',
        self::OP_LIKE    => 'like',
        self::OP_EQ      => 'eq',
        self::OP_NEQ     => 'neq'
    ];

    /**
     * 允许的Column类map
     * @var array
     */
    protected $columns_map;

    /**
     * ExpressionEngine constructor.
     * @param array $columns_map
     */
    public function __construct($columns_map = [])
    {
        $this->columns_map = $columns_map;
    }

    /**
     * 运算符优先级比较
     * @param $opa
     * @param $opb
     * @return bool
     */
    protected function operatorsGt($opa, $opb)
    {
        return $this->ops_levels[$opa] - $this->ops_levels[$opb] > 0;
    }

    /**
     * 条件值转化(常量或注入column区分)
     * @param $value
     * @return array
     */
    protected function conditionValueConvert($value){
        $type = Ludwig::CONDITION_VALUE_TYPE_COMMON;
        if ($value[0] == '$'){
            $key = substr($value,1);
            if (isset($this->columns_map[$key])) {
                $type = Ludwig::CONDITION_VALUE_TYPE_MAGIC;
                $value = $this->columns_map[$key];
            }
        }
        return compact('type','value');
    }

    /**
     * convert expression
     * @param $str
     * @return array
     */
    public function convert($str){
        $str = str_replace(')',' ) ',str_replace('(',' ( ',$str));
        $arr = explode(' ',$str);
        $real = [];
        $stack = new \SplStack();
        foreach ($arr as $item){
            if (strlen(trim($item)) == 0){
                continue;
            }elseif ($item == '('){
                $stack->push($item);
            }elseif ($item == ')'){
                $last = $stack->isEmpty() ? null : $stack->pop() ;
                while (true){
                    if (is_null($last) || $last == '(') break;
                    $real[] = $last;
                    $last = $stack->isEmpty() ? null : $stack->pop() ;
                }
            }elseif (in_array($item,[self::OP_EQ,self::OP_NEQ,self::OP_GT,self::OP_LT,self::OP_EGT,self::OP_ELT,self::OP_LIKE,self::OP_IN,self::OP_NOT_IN,self::OP_BETWEEN,self::OP_AND,self::OP_OR])){
                if (!$stack->isEmpty()){
                    $top = $stack->top();
                    if (!$this->operatorsGt($item,$top)){
                        $real[] = $stack->pop();
                    }
                }
                $stack->push($item);
            }else{
                $real[] = $item;
            }
        }
        $last = $stack->isEmpty() ? null : $stack->pop() ;
        while (true){
            if (is_null($last)) break;
            $real[] = $last;
            $last = $stack->isEmpty() ? null : $stack->pop() ;
        }

        foreach ($real as $item){
            if (in_array($item,[self::OP_EQ,self::OP_NEQ,self::OP_GT,self::OP_LT,self::OP_EGT,self::OP_ELT])){
                $value = $stack->pop();
                $key = $stack->pop();
                $data = [
                    'column' => $this->columns_map[$key],
                    'func' => $this->funcs[$item],
                    'condition_values' => [
                        'data' => $this->conditionValueConvert($value)
                    ]
                ];
                $stack->push($data);
            }elseif(in_array($item,[self::OP_LIKE])){
                $value = $stack->pop();
                $key = $stack->pop();
                $data = [
                    'column' => $this->columns_map[$key],
                    'func' => $this->funcs[$item],
                    'condition_values' => [
                        'need' => $this->conditionValueConvert($value)
                    ]
                ];
                $stack->push($data);
            }elseif(in_array($item,[self::OP_BETWEEN])){
                $to = $stack->pop();
                $from = $stack->pop();
                $key = $stack->pop();
                $data = [
                    'column' => $this->columns_map[$key],
                    'func' => $this->funcs[$item],
                    'condition_values' => [
                        'from' => $this->conditionValueConvert($from),
                        'to' => $this->conditionValueConvert($to)
                    ]
                ];
                $stack->push($data);
            }elseif(in_array($item,['in','notin'])){
                $value = $stack->pop();
                $key = $stack->pop();
                $array = explode(',',$value);
                $data = [
                    'column' => $this->columns_map[$key],
                    'func' => $this->funcs[$item],
                    'condition_values' => [
                        'array' => [
                            'type' => Ludwig::CONDITION_VALUE_TYPE_COMMON,
                            'value' => $array
                        ],
                    ]
                ];
                $stack->push($data);
            }elseif(in_array($item,[self::OP_AND,self::OP_OR])){
                $condition1 = $stack->pop();
                $condition2 = $stack->pop();
                $data = [
                    'condition_type' => Ludwig::CONDITION_TYPE_LOGIC,
                    'logic' => ($item == self::OP_OR) ? Ludwig::CONDITION_LOGIC_OR :Ludwig::CONDITION_LOGIC_AND,
                    'sons' => [$condition1,$condition2]
                ];
                $stack->push($data);
            }else{
                $stack->push($item);
            }
        }
        return [$stack->pop()];
    }
}