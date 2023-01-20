<?php

use yii\db\Migration;

/**
 * Handles the creation of table `action`.
 * Has foreign keys to the tables:
 *
 * - `controller`
 */
class m190121_131611_create_action_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('action', [
            'id' => $this->primaryKey(),
            'controller_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'description' => $this->string(),
        ]);

        // creates index for column `controller_id`
        $this->createIndex(
            'idx-action-controller_id',
            'action',
            'controller_id'
        );

        // add foreign key for table `controller`
        $this->addForeignKey(
            'fk-action-controller_id',
            'action',
            'controller_id',
            'controller',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `controller`
        $this->dropForeignKey(
            'fk-action-controller_id',
            'action'
        );

        // drops index for column `controller_id`
        $this->dropIndex(
            'idx-action-controller_id',
            'action'
        );

        $this->dropTable('action');
    }
}
