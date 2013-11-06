<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/6/13
 * @time 1:56 PM
 */

namespace PHP;


class MemoryStorage
{
    const SCALAR_KEY_TPL = "__SCR_|%s|%s";
    const ARRAY_KEY_TPL = "__ARR_|%s";
    const OBJECT_KEY_TPL = "__OBJ_|%s";
    const RESOURCE_KEY_TPL = "__RES_|%s";

    /**
     * @var array
     */
    protected $dump = [];

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->dump[$this->getKeyFromMixed($key)] = $value;
    }

    /**
     * @param mixed $key
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function get($key)
    {
        $mainKey = $this->getKeyFromMixed($key);

        // do not use internal method due to overhead of key transformations
        if(!array_key_exists($mainKey, $this->dump)) {
            throw new \OutOfBoundsException("{$key} does not exists in memory");
        }

        return $this->dump[$mainKey];
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($this->getKeyFromMixed($key), $this->dump);
    }

    /**
     * @param bool $return
     * @return array
     */
    public function dump($return = true)
    {
        if($return) {
            return $this->dump;
        }

        var_dump($this->dump);
    }

    /**
     * @param mixed $key
     * @return string
     */
    protected function getKeyFromMixed($key)
    {
        if(TypeHinting::hintValueAny(TypeHinting::T_SCALAR, $key)) {
            return sprintf(self::SCALAR_KEY_TPL, gettype($key), $key);
        } elseif(TypeHinting::hintValueAny(TypeHinting::T_OBJECT, $key)) {
            return sprintf(self::OBJECT_KEY_TPL, spl_object_hash($key));
        } elseif(TypeHinting::hintValueAny(TypeHinting::T_ARRAY, $key)) {
            return sprintf(self::ARRAY_KEY_TPL, serialize($key));
        } else {
            return sprintf(self::RESOURCE_KEY_TPL, (int) $key);
        }
    }
}