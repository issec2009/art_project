<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'filter' => DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'createTimeRange',
                    'convertFormat'=>true,
                    'startAttribute'=>'createTimeStart',
                    'endAttribute'=>'createTimeEnd',
                    'pluginOptions'=>[
                        'timePicker'=>true,
                        'timePickerIncrement'=>30,
                        'locale'=>[
                            'format'=>'Y-m-d h:i A'
                        ]
                    ]
                ])
            ],
            'name',
            'description',
            [
                'label' => 'Склад и стоимость',
                'format' => 'raw',
                'value' => function ($model) {
                    $line = '';
                    foreach ($model->stockProducts as $stock_product) {
                        if (isset($stock_product->price) && $stock_product->price > 0)
                            $line .= $stock_product->stock->name . ' - ' . $stock_product->price . 'руб. <br>';
                    }
                    return $line;
                }
            ],
//            [
//                'class' => ActionColumn::class,
//                'template' => '{update} {delete}',
//                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                }
//            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {

                    return Html::a('Редактировать', ['update', 'id' => $model->id]) . '<br>' .
                        Html::a('Удалить', ['delete', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
