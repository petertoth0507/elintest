<?php

namespace app\controllers;

use app\common\components\Utilities;
use app\common\LogFileProcessor;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index',);
    }

    public function actionLoadJstree()
    {
        $logFileProcessor = new LogFileProcessor;

        $result = json_encode($logFileProcessor->readLogFile());

        /*   $result =
            '[
            "Simple root node",
            {
              "text" : "Root node 2",
              "state" : {
                "opened" : true,
                "selected" : true
              },
              "children" : [
                { "text" : "Child 1" },
                "Child 2"
              ]
           }
         ]';*/


        Utilities::outputResult($result);
    }
}
