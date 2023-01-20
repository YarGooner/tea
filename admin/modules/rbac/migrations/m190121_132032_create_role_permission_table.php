<?php

use yii\db\Migration;

/**
 * Handles the creation of table `role_permission`.
 * Has foreign keys to the tables:
 *
 * - `role`
 * - `action`
 */
class m190121_132032_create_role_permission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('role_permission', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer()->notNull(),
            'action_id' => $this->integer(),
        ]);

        // creates index for column `role_id`
        $this->createIndex(
            'idx-role_permission-role_id',
            'role_permission',
            'role_id'
        );

        // add foreign key for table `role`
        $this->addForeignKey(
            'fk-role_permission-role_id',
            'role_permission',
            'role_id',
            'role',
            'id',
            'CASCADE'
        );

        // creates index for column `action_id`
        $this->createIndex(
            'idx-role_permission-action_id',
            'role_permission',
            'action_id'
        );

        // add foreign key for table `action`
        $this->addForeignKey(
            'fk-role_permission-action_id',
            'role_permission',
            'action_id',
            'action',
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
            'fk-role_permission-role_id',
            'role_permission'
        );

        // drops index for column `role_id`
        $this->dropIndex(
            'idx-role_permission-role_id',
            'role_permission'
        );

        // drops foreign key for table `action`
        $this->dropForeignKey(
            'fk-role_permission-action_id',
            'role_permission'
        );

        // drops index for column `action_id`
        $this->dropIndex(
            'idx-role_permission-action_id',
            'role_permission'
        );

        $this->dropTable('role_permission');
    }
}
