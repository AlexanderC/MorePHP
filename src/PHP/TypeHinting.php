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
            case "array":
                return is_array($value);
            case "callable":
                return is_callable($value);
            case "":
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
            case "string":
            case "str":
                return is_string($value);
            case "integer":
            case "int":
            case "long":
                return is_integer($value);
            case "float":
            case "double":
                return is_float($value);
            case "resource":
            case "res":
                return is_resource($value);
            case "object":
            case "obj":
                return is_object($value);
            default: return $value instanceof $type;
        }
    }

    /**
     * @param \ReflectionParameter $refParam
     * @return string
     */
    public static function getNativeParameterTypeHint(\ReflectionParameter $refParam)
    {
        $export = \ReflectionParameter::export(
            array(
                $refParam->getDeclaringClass()->name,
                $refParam->getDeclaringFunction()->name
            ),
            $refParam->name,
            true
        );

        return preg_replace('/.*?([\w\\\]*)\s+\$' . preg_quote($refParam->name, '/') . '.*/u', '\\1', $export);
    }
} 