<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $stocks common\models\Stock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $model->allStocksAndProduct,
        'columns' => [
//            'id',
            [
                'attribute' => 'stock_name',
                'format' => 'raw',
                'label' => 'Название склада'
            ],
            [
                'attribute' => 'Стоимость, руб.',
                'format' => 'raw',
                'value' => function ($model) use ($form) {
                    $line =$form->field($model['stock_product_model'], "[{$model['id']}]product_id")->hiddenInput(['value'=> $model['product_id']])->label(false);
                    $line .=$form->field($model['stock_product_model'], "[{$model['id']}]stock_id")->hiddenInput(['value'=> $model['stock_id']])->label(false);
                     $line .=$form->field($model['stock_product_model'], "[{$model['id']}]price")->label(false);
                    return $line;
                }
            ],
            [
                'attribute' => 'Кол-во штук в наличии',
                'format' => 'raw',
                'value' => function ($model) use ($form) {
                    return $form->field($model['stock_product_model'], "[{$model['id']}]quantity")->label(false);
                }
            ],
        ],
    ])
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
