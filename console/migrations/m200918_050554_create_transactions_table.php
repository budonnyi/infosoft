<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m200918_050554_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(30)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'wallet_id' => $this->integer()->notNull(),
            'deposit_id' => $this->integer(),
            'amount' => $this->double(2)->defaultValue(0),
            'created_at' => $this->integer(10)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transactions}}');
    }
}
