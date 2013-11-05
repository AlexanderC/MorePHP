<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 11:55 AM
 */

namespace PHP\Traits;


use PHP\TypeHinting;

/**
 * Class MultiConstruct
 * @package PHP\Traits
 *
 * @example constructor syntax:
 *          __construct([0-9]+)
 *          __construct([0-9]+)_(\w+)
 *
 *          The number means count of parameters to be provided to the method.
 *          The words are necessary to keep method names unique across the class.
 *
 * Note: trying to skip reflections overload- make methods names more informative
 */
trait MultiConstruct
{
    public function __construct()
    {
        $args = func_get_args();
        $argsCount = func_num_args();

        // choose the fastest way
        $methods = get_class_methods($this);

        $argsNumDump = [];

        // find all available methods that passes our regexp
        foreach($methods as $method) {
            if(preg_match("#^__construct([0-9]+)(?:_.+)?$#u", $method, $matches)) {
                $numArgs = (int) $matches[1];

                if(!isset($argsNumDump[$numArgs])) {
                    $argsNumDump[$numArgs] = [];
                }

                $argsNumDump[$numArgs][] = $method;
            }
        }

        if(!isset($argsNumDump[$argsCount])) {
            throw new \OutOfBoundsException("Unable to find multi constructor with {$argsCount} args specified");
        }

        foreach($argsNumDump[$argsCount] as $method) {
            $reflectionMethod = new \ReflectionMethod($this, $method);

            if(TypeHinting::hintMethodParameters($reflectionMethod, $args)) {
                call_user_func_array([$this, $method], $args);
                return;
            }
        }

        throw new \RuntimeException("Unable to find multiple constructor that passes type hinting test");
    }
}