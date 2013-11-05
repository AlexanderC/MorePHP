<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 11:57 AM
 */

require __DIR__ . '/bootstrap.php';

class MultiConstructTest
{
    use PHP\Traits\MultiConstruct;

    /**
     * @param array $arg
     */
    protected function __construct1_first(array $arg)
    {
        echo "array provided", "\n";
        var_dump($arg);
    }

    /**
     * @param \PHP\Config $arg
     */
    protected function __construct1_second($arg)
    {
        echo "\\PHP\\Config provided", "\n";
        var_dump($arg);
    }

    /**
     * @param string $arg
     * @return void
     */
    protected function __construct1_iDoNotCareWhatIsWrittenHere($arg)
    {
        echo "String provided: ", $arg, "\n";
    }
}

function testMultiConstructor()
{
    new MultiConstructTest('some text');
    new MultiConstructTest(['some text']);
    new MultiConstructTest(\PHP\Config::get());
    new MultiConstructTest(321);
}

try {
    \PHP\Config::get()[\PHP\Constants\Ini::ANNOTATIONS_SCALAR_TYPE_HINTING] = false;
    testMultiConstructor();
} catch(\RuntimeException $e) {
    echo "Runtime Exception: ", $e->getMessage(), "\n";
}

try {
    \PHP\Config::get()[\PHP\Constants\Ini::ANNOTATIONS_SCALAR_TYPE_HINTING] = true;
    testMultiConstructor();
} catch(\RuntimeException $e) {
    echo "Runtime Exception: ", $e->getMessage(), "\n";
}
