<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $created_at
 *
 * @property Stock[] $stock
 * @property StockProduct[] $stockProducts
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'integer'],
            [['name'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 1500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование товара',
            'description' => 'Описание товара',
            'created_at' => 'Дата изготовления',
        ];
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAllStocks()
    {
        return Stock::find()->indexBy('id')->all();
    }

    public function fields()
    {
        return [
            'name',
            'description',
            'prices' => function () {

                $prices = [];
                /** @var StockProduct $StockProd */
                foreach ($this->stockProducts as $StockProd) {
                    if ($StockProd->price > 0)
                    $prices['price'][] = [
                        'stock_code' => $StockProd->stock->code,
                        'stock_name' => $StockProd->stock->name,
                        'price' => $StockProd->price
                    ];
                }
                return $prices;
            },
        ];
    }

    public function getAllStocksAndProduct($sp_models = false)
    {
        $stocks = Stock::find()->indexBy('id')->all();

        $stockProdArray = [];
        $i = 1;
        if ($this->isNewRecord) {
            /** @var Stock $stock */
            foreach ($stocks as $stock) {
                $stockProdArray[] = [
                    'id' => $i,
                    'product_id' => null,
                    'stock_id' => $stock->id,
                    'stock_name' => $stock->name,
                    'stock_product_model' => new StockProduct()
                ];
                $i++;
            }
        } else {
            /** @var Stock $stock */
            foreach ($stocks as $stock) {
                $stock_product_model = StockProduct::find()
                    ->where(['product_id' => $this->id, 'stock_id' => $stock->id])->one();

                if ($stock_product_model) {
                    $stockProdArray[] = [
                        'id' => $i,
                        'product_id' => $this->id,
                        'stock_id' => $stock->id,
                        'stock_name' => $stock->name,
                        'stock_product_model' => $stock_product_model
                    ];
                } else {
                    $stockProdArray[] = [
                        'id' => $i,
                        'product_id' => $this->id,
                        'stock_id' => $stock->id,
                        'stock_name' => $stock->name,
                        'stock_product_model' => new StockProduct()
                    ];
                }
                $i++;
            }
        }

        if ($sp_models)
            return ArrayHelper::getColumn($stockProdArray, 'stock_product_model');

        $provider = new ArrayDataProvider([
            'allModels' => $stockProdArray,
        ]);

        return $provider;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (isset(Yii::$app->request->post()['StockProduct']) && count(Yii::$app->request->post()['StockProduct']) > 0) {
            foreach (Yii::$app->request->post()['StockProduct'] as $item) {

                if (!isset($item['stock_id']))
                    continue;

                $stock_product_model = StockProduct::find()
                    ->where(['product_id' => $this->getPrimaryKey(), 'stock_id' => $item['stock_id']])->one();

                if (!$stock_product_model)
                    $stock_product_model = new StockProduct();

                $stock_product_model->product_id = $this->getPrimaryKey();
                $stock_product_model->stock_id = $item['stock_id'];
                $stock_product_model->price = $item['price'];
                $stock_product_model->quantity = $item['quantity'];

                $stock_product_model->save();
            }
        }

        if ($insert) {
            Yii::$app->session->setFlash('success', 'Запись добавлена');
        } else {
            Yii::$app->session->setFlash('success', 'Запись обновлена');
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            StockProduct::deleteAll(['product_id' => $this->id]);
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasMany(Stock::class, ['id' => 'stock_id'])
            ->viaTable('stock_product', ['product_id' => 'id']);
    }

    /**
     * Gets query for [[StockProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockProducts()
    {
        return $this->hasMany(StockProduct::class, ['product_id' => 'id']);
    }
}
