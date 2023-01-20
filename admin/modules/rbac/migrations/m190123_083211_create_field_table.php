<?php

use yii\db\Migration;

/**
 * Handles the creation of table `field`.
 * Has foreign keys to the tables:
 *
 * - `model`
 */
class m190123_083211_create_field_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('field', [
            'id' => $this->primaryKey(),
            'model_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'description' => $this->string(),
        ]);

        // creates index for column `model_id`
        $this->createIndex(
            'idx-field-model_id',
            'field',
            'model_id'
        );

        // add foreign key for table `model`
        $this->addForeignKey(
            'fk-field-model_id',
            'field',
            'model_id',
            'model',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `model`
        $this->dropForeignKey(
            'fk-field-model_id',
            'field'
        );

        // drops index for column `model_id`
        $this->dropIndex(
            'idx-field-model_id',
            'field'
        );

        $this->dropTable('field');
    }
}
