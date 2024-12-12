<?php

namespace app\common\components;

use yii\web\View;

use yii;

class Utilities
{

    public static function registerJS($view, $p_options, $p_use_quotation_mark = true)
    {
        if (is_array($p_options)) {
            $script = '';
            foreach ($p_options as $key => $val) {
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                if ($p_use_quotation_mark) {
                    $script .= $key . '="' . $val . '";';
                } else {
                    $script .= $key . '=' . $val . ';';
                }
            }
            $view->registerJs($script, View::POS_HEAD);
        }
    }

    public static function registerExcecutableJS($view, $p_options)
    {
        if (is_array($p_options)) {
            $script = '';
            foreach ($p_options as $key => $val) {
                $script .= $val . ';';
            }
            $view->registerJs($script, View::POS_READY);
        }
    }

    public static function getVersionedFiles($p_files)
    {
        $out = [];
        foreach ($p_files as $file) {
            $out[] = $file . '?v=' . Yii::$app->params['web_version'];
        }
        return $out;
    }

    public static function outputResult($p_content, $p_is_json = true)
    {
        if ($p_is_json) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        \Yii::$app->response->data  =  $p_content;
    }

    public static function getIncomingParameter($p_param, $p_default_value = '')
    {
        if (array_key_exists($p_param, $_POST)) {
            $result = Utilities::cleanInput($_POST[$p_param]);
        } elseif (array_key_exists($p_param, $_GET)) {
            $result = Utilities::cleanInput($_GET[$p_param]);
        } else {
            $result = $p_default_value;
        }
        return $result;
    }

    public static function cleanInput($p_input, $strip_html_tags = false)
    {
        if (is_array($p_input)) {
            $result = [];
            foreach ($p_input as $key => $input) {
                $result[$key] = self::cleanInput($input, $strip_html_tags);
            }
        } else {
            $result = Yii::$app->db->quoteValue($p_input);
            $result = stripcslashes($result);
            $result = trim($result);
            if (is_string($p_input)) {
                $result = substr($result, 1, strlen($result) - 2);
            }
            if ($strip_html_tags) {
                $result = strip_tags($result);
                $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
            }
            $result = trim($result);
        }
        return $result;
    }
}
