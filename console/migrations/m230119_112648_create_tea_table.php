<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tea}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%collection}}`
 */
class m230119_112648_create_tea_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tea}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'collection_id' => $this->integer()->notNull(),
            'subtitle' => $this->text(),
            'description' => $this->text(),
            'image_fon' => $this->string(),
            'image_pack' => $this->string(),
            'weight' => $this->text(),
            'temperature_brewing' => $this->text(),
            'time_brewing' => $this->text(),
            'buy_button_flag' => $this->boolean(),
            'url' => $this->text(),
            'output_priority' => $this->integer(),
        ]);

        // creates index for column `collection_id`
        $this->createIndex(
            '{{%idx-tea-collection_id}}',
            '{{%tea}}',
            'collection_id'
        );

        // add foreign key for table `{{%collection}}`
        $this->addForeignKey(
            '{{%fk-tea-collection_id}}',
            '{{%tea}}',
            'collection_id',
            '{{%collection}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%collection}}`
        $this->dropForeignKey(
            '{{%fk-tea-collection_id}}',
            '{{%tea}}'
        );

        // drops index for column `collection_id`
        $this->dropIndex(
            '{{%idx-tea-collection_id}}',
            '{{%tea}}'
        );

        $this->dropTable('{{%tea}}');
    }
}
