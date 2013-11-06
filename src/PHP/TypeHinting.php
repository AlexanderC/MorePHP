<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 3:34 PM
 */

namespace PHP;


use PHP\Annotations\MethodParameters;
use PHP\Constants\Ini;

class TypeHinting
{
    /**
     * Main native and extended types
     */
    const T_SCALAR = 'scalar';
    const T_OBJECT = 'object';
    const T_RESOURCE = 'resource';
    const T_FLOAT = 'float';
    const T_INTEGER = 'integer';
    const T_STRING = 'string';
    const T_ARRAY = 'array';
    const T_CALLABLE = 'callable';
    const T_ANY = '';

    /**
     * Advanced native and extended types notation
     */
    const T_STRING_SHORT = 'str';
    const T_INTEGER_SHORT = 'int';
    const T_INTEGER_CTYPE_LONG = 'long';
    const T_FLOAT_CTYPE_DOUBLE = 'double';
    const T_RESOURCE_SHORT = 'res';
    const T_OBJECT_SHORT = 'obj';

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @param array $args
     * @return bool
     */
    public static function hintMethodParameters(\ReflectionMethod $reflectionMethod, array & $args)
    {
        // if this flag is set as true than we need to check also type of parameter
        // in doc block if available. In any case Native type hinting has priority
        $useAnnotations = true === (bool) Config::get()->tryGet(Ini::ANNOTATIONS_SCALAR_TYPE_HINTING, false);

        $parameters = $reflectionMethod->getParameters();

        if($useAnnotations) {
            $annotationMethod = new MethodParameters($reflectionMethod);
        }

        /** @var $parameter \ReflectionParameter */
        foreach($parameters as $i => $parameter) {
            $parameterType = self::getNativeParameterTypeHint($parameter);

            if($useAnnotations && empty($parameterType)) {
                try {
                    $parameterType = $annotationMethod->getParameterType($parameter);

                    if(!self::hintValueAny($parameterType, $args[$i])) {
                        return false;
                    }
                } catch(\OutOfBoundsException $e) {     }
            } else {
                if(!self::hintValueNative($parameterType, $args[$i])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return bool
     */
    public static function hintValueAny($type, $value)
    {
        return self::hintValueNative($type, $value) || self::hintValueExtended($type, $value);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return bool
     */
    public static function hintValueNative($type, $value)
    {
        switch($type) {
            case self::T_ARRAY:
                return is_array($value);
            case self::T_CALLABLE:
                return is_callable($value);
            case self::T_ANY:
                return true;
                break;
            default: return $value instanceof $type;
        }
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return bool
     */
    public static function hintValueExtended($type, $value)
    {
        switch($type) {
            case self::T_STRING:
            case self::T_STRING_SHORT:
                return is_string($value);
            case self::T_INTEGER:
            case self::T_INTEGER_SHORT:
            case self::T_INTEGER_CTYPE_LONG:
                return is_integer($value);
            case self::T_FLOAT:
            case self::T_FLOAT_CTYPE_DOUBLE:
                return is_float($value);
            case self::T_RESOURCE:
            case self::T_RESOURCE_SHORT:
                return is_resource($value);
            case self::T_OBJECT:
            case self::T_OBJECT_SHORT:
                return is_object($value);
            case self::T_SCALAR:
                return is_scalar($value);
            default: return $value instanceof $type;
        }
    }

    /**
     * @param \ReflectionParameter $refParam
     * @return string
     */
    public static function getNativeParameterTypeHint(\ReflectionParameter $refParam)
    {
        /** @var MemoryStorage $memory */
        $memory = Config::get()[Config::MEMORY];
        $cacheKey = sprintf(
            "__MorePHP__|TypeHinting/ReflectionParameter/%s->%s({%d} %s)",
            $refParam->getDeclaringClass()->getName(),
            $refParam->getDeclaringFunction()->getName(),
            $refParam->getPosition(),
            $refParam->getName()
        );

        if($memory->has($cacheKey)) {
            return $memory->get($cacheKey);
        } else {
            $export = \ReflectionParameter::export(
                array(
                    $refParam->getDeclaringClass()->getName(),
                    $refParam->getDeclaringFunction()->getName()
                ),
                $refParam->getName(),
                true
            );

            $result = preg_replace('/.*?([\w\\\]*)\s+\$' . preg_quote($refParam->name, '/') . '.*/u', '\\1', $export);

            $memory->set($cacheKey, $result);
        }

        return $result;
    }
} 