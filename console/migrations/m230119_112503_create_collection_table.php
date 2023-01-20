<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%collection}}`.
 */
class m230119_112503_create_collection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%collection}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'subtitle' => $this->string(),
            'hovercolor' => $this->string(),
            'image' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%collection}}');
    }
}
