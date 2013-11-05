<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 12:48 PM
 */

namespace PHP\Constants;


abstract class IConst
{
    /**
     * @var array
     */
    protected static $constants = [];

    /**
     * Get all available constant values
     *
     * @return iterator
     */
    public static function all()
    {
        foreach(self::$constants as $constant) {
            yield $constant;
        }
    }
}