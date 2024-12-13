<?php

use app\assets\JstTreeAsset;
use app\common\components\Utilities;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
JstTreeAsset::register($this);
Utilities::registerJS($this, [
    'load_jstree_url' => Url::toRoute('site/load-jstree'),
]);
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Ez egy Teszt oldal!</h1>

        <p class="lead">Ez egy log fájl feldolgozásának tesztelését szolgáló oldal.</p>

        <div id="btn-jstree" class="btn btn-lg btn-success">Nyomd meg ezt a gombot!</div>
    </div>

    <div class="body-content">
        <div class="row">
            <div id="jstree-div"></div>
        </div>

        <div class="row">

        </div>

    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalLoader" tabindex="-1" role="dialog" aria-labelledby="modalLoaderTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header mx-auto">
                    <h5 class="modal-title" id="modalLoaderTitle"><?php echo Yii::t('app', 'LoadingProcess'); ?></h5>
                </div>
                <hr>
                <div class="modal-body mx-auto">
                    <div class="loader"></div>
                    <div class="pt-5 text-center"><?php echo Yii::t('app', 'PleaseWait'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>