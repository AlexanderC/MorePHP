<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 12:45 PM
 */

namespace PHP\Constants;


class Ini extends IConst
{
    /**
     * Also check scalar type
     */
    const ANNOTATIONS_SCALAR_TYPE_HINTING = 'more_php.annotation.check_scalar_types';

    /**
     * @var array
     */
    protected static $constants = [
        'ANNOTATIONS_SCALAR_TYPE_HINTING' => self::ANNOTATIONS_SCALAR_TYPE_HINTING
    ];
}