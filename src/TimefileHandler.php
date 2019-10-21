<?php
declare(strict_types=1);
/**
 *
 */

namespace Yaxin\TimefileHandler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class TimefileHandler extends StreamHandler
{
    const DATETIME_PATTERN = '/(\%\((.*)\))/';

    /**
     * @var string
     */
    private $rawFilename;

    /**
     * @var string
     */
    private $datePattern = '';

    /**
     * @var string
     */
    private $filePatternDate = '';


    public function __construct(string $filename, int $level = Logger::DEBUG, bool $bubble = true,
                                ?int $filePermission = null, bool $useLocking = false)
    {
        $this->rawFilename = $filename;

        parent::__construct($this->getRealFilename(), $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * Close file if datetime is expired. And open a new file base on new datetime.
     *
     * @param array $record
     */
    protected function write(array $record): void
    {
        $this->rotate($record);

        parent::write($record);
    }

    /**
     * @param array $record
     */
    protected function rotate(array &$record)
    {
        if ($this->filePatternDate == $record['datetime']->format($this->datePattern)) {
            return;
        }
        $this->close();
        $this->url = $this->getRealFilename();
    }

    /**
     * Replace datetime flag in raw filename, and return the real filename.
     *
     * @return string
     */
    private function getRealFilename()
    {
        $filename = preg_replace_callback(self::DATETIME_PATTERN, function($matched) {
            $this->datePattern = $matched[2];
            $this->filePatternDate = date($this->datePattern);

            return $this->filePatternDate;
        }, $this->rawFilename);
        return $filename ?: $this->rawFilename;
    }
}