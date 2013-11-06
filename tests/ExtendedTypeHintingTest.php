<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 11:57 AM
 */

require __DIR__ . '/bootstrap.php';

class ExtendedTypeHintingTest
{
    use \PHP\Traits\ExtendedTypeHinting;

    /**
     * @param string $second
     * @param array $arg
     */
    protected function test(array $arg, $second)
    {
        echo "array and ", gettype($second), " provided to test method", "\n";
    }

    /**
     * @param string $second
     * @param array $arg
     */
    protected static function testStatic(array $arg, $second)
    {
        echo "array and ", gettype($second), " provided to testStatic method", "\n";
    }

    /**
     * @return array
     */
    protected static function __getTypeHintAbleMethods()
    {
        return ['test', 'testStatic'];
    }

}

function testExtendedTypeHinting()
{
    $obj = new ExtendedTypeHintingTest();

    $obj->test(['smth'], 'test');
    $obj->test(['smth'], 321);

    ExtendedTypeHintingTest::testStatic(['smth'], 'test');
    ExtendedTypeHintingTest::testStatic(['smth'], 321);
}

try {
    \PHP\Config::get()[\PHP\Constants\Ini::ANNOTATIONS_SCALAR_TYPE_HINTING] = false;
    testExtendedTypeHinting();
} catch(\RuntimeException $e) {
    echo "Runtime Exception: ", $e->getMessage(), "\n";
}

try {
    \PHP\Config::get()[\PHP\Constants\Ini::ANNOTATIONS_SCALAR_TYPE_HINTING] = true;
    testExtendedTypeHinting();
} catch(\RuntimeException $e) {
    echo "Runtime Exception: ", $e->getMessage(), "\n";
}
