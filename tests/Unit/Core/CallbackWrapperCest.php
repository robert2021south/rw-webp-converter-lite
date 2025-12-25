<?php
namespace Tests\Unit\Core;

use ReflectionException;

use RobertWP\WebPConverterLite\Core\CallbackWrapper;
use Tests\Support\UnitTester;

class CallbackWrapperCest
{

    /**
     * @throws ReflectionException
     */
    public function testGenerateCallbackKey(UnitTester $I): void
    {
        $callback = fn() => 'test';
        $key1 = $this->callPrivate('generate_callback_key', $callback);
        $key2 = $this->callPrivate('generate_callback_key', $callback);

        $I->assertEquals($key1, $key2, '相同回调应生成相同的 key');
    }

    /**
     * @throws ReflectionException
     */
    private function callPrivate(string $method, $callback)
    {
        $ref = new \ReflectionClass(CallbackWrapper::class);
        $m = $ref->getMethod($method);
        return $m->invoke(null, $callback);
    }
}