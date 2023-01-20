<?php

use yii\db\Migration;

/**
 * Handles the creation of table `role_model_permission`.
 * Has foreign keys to the tables:
 *
 * - `role`
 * - `field`
 */
class m190123_083349_create_role_model_permission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('role_model_permission', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer()->notNull(),
            'field_id' => $this->integer()->notNull(),
            'type' => $this->boolean()->notNull(),
        ]);

        // creates index for column `role_id`
        $this->createIndex(
            'idx-role_model_permission-role_id',
            'role_model_permission',
            'role_id'
        );

        // add foreign key for table `role`
        $this->addForeignKey(
            'fk-role_model_permission-role_id',
            'role_model_permission',
            'role_id',
            'role',
            'id',
            'CASCADE'
        );

        // creates index for column `field_id`
        $this->createIndex(
            'idx-role_model_permission-field_id',
            'role_model_permission',
            'field_id'
        );

        // add foreign key for table `field`
        $this->addForeignKey(
            'fk-role_model_permission-field_id',
            'role_model_permission',
            'field_id',
            'field',
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
            'fk-role_model_permission-role_id',
            'role_model_permission'
        );

        // drops index for column `role_id`
        $this->dropIndex(
            'idx-role_model_permission-role_id',
            'role_model_permission'
        );

        // drops foreign key for table `field`
        $this->dropForeignKey(
            'fk-role_model_permission-field_id',
            'role_model_permission'
        );

        // drops index for column `field_id`
        $this->dropIndex(
            'idx-role_model_permission-field_id',
            'role_model_permission'
        );

        $this->dropTable('role_model_permission');
    }
}
