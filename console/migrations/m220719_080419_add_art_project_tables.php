<?php

use yii\db\Migration;

/**
 * Class m220719_080419_add_art_project_tables
 */
class m220719_080419_add_art_project_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%stock}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'code' => $this->integer()->unique()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'description' => $this->string(1500)->null(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%stock_product}}', [
            'id' => $this->primaryKey(),
            'stock_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'price' => $this->float()->defaultValue(0)->null(),
            'quantity' => $this->integer()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk-stock_product-stock_id-stock-id','{{%stock_product}}','stock_id','{{%stock}}','id','CASCADE','CASCADE');
        $this->addForeignKey('fk-stock_product-product_id-product-id','{{%stock_product}}','product_id','{{%product}}','id','CASCADE','CASCADE');
        $this->createIndex('stock_product-stock_idx','{{%stock_product}}','stock_id');
        $this->createIndex('stock_product-product_idx','{{%stock_product}}','product_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-stock_product-product_id-product-id','{{%stock_product}}');
        $this->dropForeignKey('fk-stock_product-stock_id-stock-id','{{%stock_product}}');
        $this->dropTable('{{%stock_product}}');

        $this->dropTable('{{%product}}');

        $this->dropTable('{{%stock}}');
    }
}
