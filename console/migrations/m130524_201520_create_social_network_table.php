<?php

use yii\db\Migration;

/**
 * Handles the creation of table `social_network`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m130524_201520_create_social_network_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%social_network}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(11),
            'social_network_id' => $this->string(10)->notNull(),
            'user_auth_id' => $this->string(300)->notNull(),
            'access_token' => $this->string(300),
            'last_auth_date' => $this->integer(11),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-social_network-user_id',
            'social_network',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-social_network-user_id',
            'social_network',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-social_network-user_id',
            'social_network'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-social_network-user_id',
            'social_network'
        );

        $this->dropTable('{{%social_network}}');
    }
}
