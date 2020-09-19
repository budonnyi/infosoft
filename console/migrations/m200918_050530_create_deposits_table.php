<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%deposits}}`.
 */
class m200918_050530_create_deposits_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%deposits}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'wallet_id' => $this->integer()->notNull(),
            'invested' => $this->double(2)->defaultValue(0),
            'percent' => $this->double(2)->defaultValue(0),
            'active' => $this->smallInteger(1)->defaultValue(0),
            'duration' => $this->smallInteger(1)->defaultValue(0),
            'accrue_times' => $this->smallInteger(2)->defaultValue(0),
            'created_at' => $this->integer(10)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%deposits}}');
    }
}
