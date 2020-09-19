<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wallets}}`.
 */
class m200918_050510_create_wallets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wallets}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'ballance' => $this->double(2)->defaultValue(0),
            'created_at' => $this->integer(12)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wallets}}');
    }
}
