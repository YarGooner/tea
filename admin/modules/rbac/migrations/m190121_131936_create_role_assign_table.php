<?php

use yii\db\Migration;

/**
 * Handles the creation of table `role_assign`.
 * Has foreign keys to the tables:
 *
 * - `role`
 * - `user`
 */
class m190121_131936_create_role_assign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('role_assign', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer(),
            'user_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `role_id`
        $this->createIndex(
            'idx-role_assign-role_id',
            'role_assign',
            'role_id'
        );

        // add foreign key for table `role`
        $this->addForeignKey(
            'fk-role_assign-role_id',
            'role_assign',
            'role_id',
            'role',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-role_assign-user_id',
            'role_assign',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-role_assign-user_id',
            'role_assign',
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
        // drops foreign key for table `role`
        $this->dropForeignKey(
            'fk-role_assign-role_id',
            'role_assign'
        );

        // drops index for column `role_id`
        $this->dropIndex(
            'idx-role_assign-role_id',
            'role_assign'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-role_assign-user_id',
            'role_assign'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-role_assign-user_id',
            'role_assign'
        );

        $this->dropTable('role_assign');
    }
}
