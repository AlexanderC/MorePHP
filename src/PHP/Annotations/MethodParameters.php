<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 2:10 PM
 */

namespace PHP\Annotations;


class MethodParameters
{
    const BLOCK_REGEXP = "~(?:\s*\*\s*(?P<line>@param\s+[\w\\\]+\s+[$\w]+)\s*\n)~umi";
    const PARAM_REGEXP = "~@param\s+(?P<type>[\w\\\]+)\s+(?P<name>[$\w]+)~ui";

    /**
     * @var \ReflectionMethod
     */
    protected $method;

    /**
     * @var array
     */
    protected $parsedParameters = [];

    /**
     * @param \ReflectionMethod $method
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;

        $this->parseDocBlock();
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getParameterType(\ReflectionParameter $parameter)
    {
        if(!isset($this->parsedParameters[$parameter->getName()])) {
            throw new \OutOfBoundsException("Unable to find type in DocBlock for parameter {$parameter->getName()}");
        }

        return $this->parsedParameters[$parameter->getName()];
    }

    /**
     * @return void
     */
    protected function parseDocBlock()
    {
        $docBlock = $this->method->getDocComment();

        if(preg_match_all(self::BLOCK_REGEXP, $docBlock, $matches)) {
            foreach($matches['line'] as $commentLine) {
                if(preg_match(self::PARAM_REGEXP, $commentLine, $matches)) {
                    $this->parsedParameters[trim($matches['name'], ' $')] = strtolower($matches['type']);
                }
            }
        }
    }
} 