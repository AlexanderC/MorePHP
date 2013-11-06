<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 12:17 PM
 */

namespace PHP;

use PHP\Constants\Ini;

class Config implements \ArrayAccess
{
    const DEFAULT_VALUE = null;
    const MEMORY = '_memory';

    /**
     * @var array
     */
    protected $map = [];

    public function __construct()
    {
        $this->initFromIni();
        $this->initMemoryStorage();
    }

    /**
     * @return Config
     */
    public static function get()
    {
        static $self;

        if($self instanceof self) {
            return $self;
        }

        $self = new self();
        return $self;
    }

    /**
     * @param string $offset
     * @param mixed $default
     * @return mixed
     */
    public function tryGet($offset, $default = self::DEFAULT_VALUE)
    {
        return $this->offsetExists($offset) ? $this->map[$offset] : $default;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->map);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @throws \OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new \OutOfBoundsException("Unable to find property {$offset}");
        }

        return $this->map[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->map[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @throws \OutOfBoundsException
     */
    public function offsetUnset($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new \OutOfBoundsException("Unable to find property {$offset}");
        }

        unset($this->map[$offset]);
    }

    /**
     * @return void
     */
    protected function initFromIni()
    {
        foreach(Ini::all() as $constant => $value) {
            $iniValue = ini_get($value);

            if(false !== $iniValue) {
                $this->offsetSet($value, $iniValue);
            }
        }
    }

    /**
     * @return void
     */
    protected function initMemoryStorage()
    {
        $this->offsetSet(self::MEMORY, new MemoryStorage());
    }
} 