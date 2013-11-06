<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/6/13
 * @time 2:30 PM
 */

require __DIR__ . '/bootstrap.php';

class MemoryStorageTest
{
    /**
     * @var PHP\MemoryStorage
     */
    protected $storage;

    /**
     * @var string
     */
    protected $value;

    public function __construct()
    {
        $this->storage = new \PHP\MemoryStorage();
        $this->value = uniqid();
    }

    /**
     * @return \PHP\MemoryStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    public function insertScalars()
    {
        $this->storage->set('test', $this->value);
        $this->storage->set(321, $this->value);
        $this->storage->set(321.57849, $this->value);
    }

    public function insertArrays()
    {
        $this->storage->set(['test', 12313, curl_init()], $this->value);
    }

    public function insertResources()
    {
        $this->storage->set(curl_init(), $this->value);
        $this->storage->set(tmpfile(), $this->value);
    }

    public function insertObjects()
    {
        $this->storage->set(\PHP\Config::get(), $this->value);
        $this->storage->set($this, $this->value);
    }
}


$test = new MemoryStorageTest();
$test->insertScalars();
$test->insertArrays();
$test->insertResources();
$test->insertObjects();

$test->getStorage()->dump(false);