<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%news}}`.
 */
class m230119_113433_create_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'output_priority' => $this->integer(),
            'date' => $this->integer()->notNull(),
            'description' => $this->text(),
            'text' => $this->text(),
            'image' => $this->string(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%news}}');
    }
}
