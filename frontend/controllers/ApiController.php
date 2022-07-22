<?php

namespace frontend\controllers;;

use common\models\Product;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\rest\Serializer;

class ApiController extends ActiveController
{
    public $modelClass = Product::class;
}