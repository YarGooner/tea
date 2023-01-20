<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m130524_201500_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'password_hash' => $this->string(60),
            'auth_source' => $this->string(),
            'auth_key' => $this->string(),
            'password_reset_token' => $this->string(),
            'last_login_at' => $this->integer(11),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'status' => $this->integer()->notNull()->defaultValue('10'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
