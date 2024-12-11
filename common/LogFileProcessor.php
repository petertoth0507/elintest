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

    public $parents = [['id' => '#', 'levelPos' => 0, 'parent' => '#']];

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
            if (strpos($fileRow, ' -> ')) {
                $processedData = $this->processFileRow($fileRow);
                $processedData += ['id' => $rowCounter];
                if (empty($this->prevItem)) {
                    $this->prevItem = $processedData;
                }
                $this->setParentId($processedData);
                if (end($this->parents)['levelPos'] === $processedData['levelPos']) {
                    $processedData += ['parent' => end($this->parents)['parent']];
                } else {
                    $processedData += ['parent' => end($this->parents)['id']];
                }

                Yii::error(json_encode(end($this->parents)));
                Yii::error(json_encode($processedData));
                $this->prevItem = $processedData;
            }
            $rowCounter++;
            if ($rowCounter > static::LINE_LIMIT) {
                break;
            }
        }
        return $rowCounterString;
    }


    protected function processFileRow(string $fileRow)
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
            'startTime' => (float) $startTime,
            'usedMemory' => (int) $usedMemory,
            'processedRow' => $processedRow,
            'logRow' => $logRow
        ];
    }

    protected function setParentId(array $currentData)
    {
        if ($currentData['levelPos'] > $this->prevItem['levelPos']) {
            array_push($this->parents, ['id' => $this->prevItem['id'], 'levelPos' =>  $this->prevItem['levelPos'], 'parent' => $this->prevItem['parent']]);
        } elseif ($currentData['levelPos'] < $this->prevItem['levelPos']) {
            $whileNumber = (($this->prevItem['levelPos'] - $currentData['levelPos']) / 2) - 1;
            while ($whileNumber > 0) {
                array_pop($this->parents);
                $whileNumber--;
            }
            while (end($this->parents)['levelPos'] !== $currentData['levelPos']) {
                array_pop($this->parents);
            }
        }
    }
}
