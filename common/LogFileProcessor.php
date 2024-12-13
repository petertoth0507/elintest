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
    const LINE_LIMIT = 10000000;
    const BASE_WEB_PATH = '/home/www/clients/client1286/web15061/web/';

    public $jstreeTypeArray = [];

    public $prevItem = [];
    public $currentItem = [];

    public $parents = [['id' => '0', 'levelPos' => 0, 'parent' => '#', 'text' => 'log file started']];

    public function readLogFile()
    {
        $logFilePath = (dirname(dirname(__FILE__)) . static::LOG_FILE_RELATIVE_PATH);
        Yii::error(__CLASS__ . ' started.');
        Yii::error($logFilePath);

        $logFile = new SplFileObject($logFilePath);
        $rowCounter = 1;
        $isFirstProcessedRow = true;
        while (!$logFile->eof()) {
            set_time_limit(60);
            $fileRow = $logFile->fgets();
            if (strpos($fileRow, ' -> ')) {
                $processedData = $this->processFileRow($fileRow);
                $processedData +=  ['id' => (string) $rowCounter];
                if (empty($this->prevItem)) {
                    $processedData += ['parent' => (string) end($this->parents)['parent']];
                    $processedData += ['relativeMem' => $processedData['usedMemory']];
                    $processedData += ['duringTime' => $processedData['startTime']];
                    $processedData += ['text' => number_format(($processedData['usedMemory'] / 1024), 2, '.', '') . 'Kb,  0s ---- '  . $processedData['processedRow'] . ':' . $processedData['logRow']];

                    $this->prevItem = $processedData;
                }
                $this->setParentId($processedData);
                if (end($this->parents)['levelPos'] === $processedData['levelPos']) {
                    $processedData += ['parent' => (string) end($this->parents)['parent']];
                } else {
                    $processedData += ['parent' => (string) end($this->parents)['id']];
                }

                $processedData += ['relativeMem' => $processedData['usedMemory'] - $this->prevItem['usedMemory']];
                $this->prevItem['duringTime'] = (float) $processedData['startTime'] - (float) $this->prevItem['startTime'];
                $this->prevItem['text'] = number_format(($this->prevItem['usedMemory'] / 1024), 2, '.', '') . 'Kb,  ' .  number_format($this->prevItem['duringTime'], 2, '.', '') . 's ---- ' . $this->prevItem['processedRow'] . ':' . $this->prevItem['logRow'];

                if (!$isFirstProcessedRow) {
                    $this->jstreeTypeArray[] = $this->prevItem;
                }
                $this->prevItem = $processedData;
                $isFirstProcessedRow = false;
            }
            $rowCounter++;
            if ($rowCounter > static::LINE_LIMIT) {
                break;
            }
        }
        return $this->jstreeTypeArray;
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
            'logRow' => $logRow,
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
