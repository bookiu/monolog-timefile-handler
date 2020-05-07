<?php

namespace Yaxin\TimefileHandler\TestCases;

use Yaxin\TimefileHandler\TimefileHandler;


class TimefileHandlerTest extends TestCase
{
    private $lastError;

    protected function setUp()
    {
        $dir = __DIR__ . '/Fixtures';
        chmod($dir, 0777);
        if (!is_writable($dir)) {
            $this->markTestSkipped($dir . ' must be writable to test the RotatingFileHandler.');
        }
        $this->lastError = null;
        set_error_handler(function ($code, $message) {
            $this->lastError = array(
                'code' => $code,
                'message' => $message,
            );

            return true;
        });
    }

    public function testRotationCreatesNewFile()
    {
        $handler = new TimefileHandler(__DIR__ . '/Fixtures/foo_%(Ymd_H).rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());

        $log = __DIR__ . '/Fixtures/foo_' . date('Ymd_H') . '.rot';
        $this->assertTrue(file_exists($log));
        $this->assertEquals('test', file_get_contents($log));
    }

    public function testRotation()
    {
        $handler = new TimefileHandler(__DIR__ . '/Fixtures/foo_%(Ymd_His).rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());
        $log1 = __DIR__ . '/Fixtures/foo_' . date('Ymd_His') . '.rot';
        sleep(1);
        $handler->handle($this->getRecord());
        $log2 = __DIR__ . '/Fixtures/foo_' . date('Ymd_His') . '.rot';

        $this->assertNotEquals($log1, $log2);
        $this->assertTrue(file_exists($log1));
        $this->assertTrue(file_exists($log2));
    }

    public function testReuseCurrentFile()
    {
        $log = __DIR__ . '/Fixtures/foo_' . date('Ymd') . '.rot';
        file_put_contents($log, "foo");
        $handler = new TimefileHandler(__DIR__ . '/Fixtures/foo_%(Ymd).rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());
        $this->assertEquals('footest', file_get_contents($log));
    }

    protected function tearDown()
    {
        foreach (glob(__DIR__ . '/Fixtures/*.rot') as $file) {
            unlink($file);
        }
        restore_error_handler();
    }
}