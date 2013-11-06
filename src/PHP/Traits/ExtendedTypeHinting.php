<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/6/13
 * @time 10:48 AM
 */

namespace PHP\Traits;


use PHP\TypeHinting;

trait ExtendedTypeHinting
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, array $arguments)
    {
        if(!self::__isMethodTypeHintAble($method)) {
            return $this->__callHook($method, $arguments);
        } else if(!TypeHinting::hintMethodParameters(new \ReflectionMethod($this, $method), $arguments)) {
            throw new \InvalidArgumentException("Type Hinting failed when calling {$method}");
        }

        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function __callStatic($method, array $arguments)
    {
        if(!self::__isMethodTypeHintAble($method)) {
            return self::__callStaticHook($method, $arguments);
        } else if(!TypeHinting::hintMethodParameters(new \ReflectionMethod(get_class(), $method), $arguments)) {
            throw new \InvalidArgumentException("Type Hinting failed when calling {$method}");
        }

        return call_user_func_array([get_class(), $method], $arguments);
    }

    /**
     * @param string $method
     * @return bool
     */
    protected static function __isMethodTypeHintAble($method)
    {
        return in_array($method, self::__getTypeHintAbleMethods());
    }

    /**
     * Get all methods that would be threated as
     * type hint able
     *
     * @return array
     */
    protected static function __getTypeHintAbleMethods()
    {
        return [];
    }

    /**
     * This method should be used by extending classes
     * instead of magic __call method
     * (would be called after trying it as type hint able)
     *
     * @param string $method
     * @param array $arguments
     */
    protected function __callHook($method, array & $arguments)
    {   }

    /**
     * This method should be used by extending classes
     * instead of magic __callStatic method
     * (would be called after trying it as type hint able)
     *
     * @param string $method
     * @param array $arguments
     */
    protected static function __callStaticHook($method, array & $arguments)
    {   }
}