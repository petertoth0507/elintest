<?php

namespace app\common;

use SplFileObject;
use Yii;


/**
 * This class processes a log file which has been created by xdebug.
 */
class LogFileProcessor
{

    const LOG_FILE_RELATIVE_PATH = DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'teszt.log';
    const LINE_LIMIT = 100000000;
    const BASE_WEB_PATH = '/home/www/clients/client1286/web15061/web/';

    public $jstreeTypeArray = [];

    public $prevItem = [];
    public $currentItem = [];

    public function readLogFile()
    {
        $logFilePath = (dirname(dirname(__FILE__)) . static::LOG_FILE_RELATIVE_PATH);
        Yii::error(__CLASS__ . ' started.');
        Yii::error($logFilePath);

        $logFile = new SplFileObject($logFilePath);
        $rowCounterString = '';
        $rowCounter = 1;
        while (!$logFile->eof()) {
            set_time_limit(60);
            $fileRow = $logFile->fgets();
            if ($rowCounter > 1) {
                $processedData = $this->processFileRow($fileRow);
                if ($processedData['levelPos'] !== false) {
                    $processedData += ['id' => $rowCounter];
                    Yii::error(json_encode($processedData));
                }
            }
            $rowCounter++;
            if ($rowCounter > static::LINE_LIMIT) {
                break;
            }
        }
        return $rowCounterString;
    }


    public function processFileRow(string $fileRow)
    {
        $levelPos = strpos($fileRow, ' -> ');
        $trimmedRow = trim($fileRow);

        $startTime = substr($trimmedRow, 0, strpos(trim($trimmedRow), ' '));
        $processedRow = trim(substr($trimmedRow, strlen($startTime)));
        $usedMemory = substr($processedRow, 0, strpos($processedRow, ' '));
        $processedRow =  trim(substr($processedRow, strlen($usedMemory)));
        $logRow = substr($processedRow, strrpos(trim($processedRow), ':') + 1);
        $processedRow = substr(substr(trim(substr($processedRow, strrpos(trim($processedRow), ' '))), 0, - (strlen($logRow) + 1)), strlen(static::BASE_WEB_PATH));

        return [
            'levelPos' => $levelPos,
            'startTime' => $startTime,
            'usedMemory' => $usedMemory,
            'processedRow' => $processedRow,
            'logRow' => $logRow
        ];
    }
}
