<?php

namespace Yaxin;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Yaxin\TimefileHandler\TimefileHandler;


class TimefileHandlerTest extends TestCase
{
    private $logger;
    private $logBasePath;

    protected function setUp()
    {
        $this->logger = new Logger('test');
        $this->logBasePath = __DIR__ . '/log';

        parent::setUp();
    }

    public function testLogFile()
    {
        $logFile = $this->logBasePath . '/testing.log';
        $handler = new TimefileHandler($logFile);
        $this->logger->setHandlers([$handler]);
        $this->logger->error("test log file exist");
        self::assertFileExists($logFile);
    }

    public function testDateTimePattern()
    {
        $logFilePattern = $this->logBasePath . '/test%(_Ymd_His).log';

        $handler = new TimefileHandler($logFilePattern);
        $this->logger->pushHandler($handler);

        $logFile = $this->logBasePath . '/test' . date('_Ymd_His') . '.log';
        $this->logger->warning('AAAAAAAAAAAA');
        self::assertTrue(is_file($logFile));

        sleep(2);

        $logFile2 = $this->logBasePath . '/test' . date('_Ymd_His') . '.log';
        $this->logger->warning('BBBBBBBBBBBB');
        self::assertTrue(is_file($logFile2));

        self::assertNotSame($logFile, $logFile2);
    }

    protected function tearDown()
    {
        // Delete all test log file
        $logFiles = glob($this->logBasePath . '/*');
        if ($logFiles) {
            foreach ($logFiles as $file) {
                @unlink($file);
            }
        }
        parent::tearDown();
    }
}