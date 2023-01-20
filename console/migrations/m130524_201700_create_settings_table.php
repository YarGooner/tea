<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m130524_201700_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'parameter' => $this->string(100)->notNull()->comment('Название параметра'),
            'value' => $this->string(255)->notNull()->comment('Значение'),
            'description' => $this->string(255)->notNull()->comment('Описание параметра'),
        ]);
		
		// Default Data
        $this->batchInsert('{{%settings}}',['parameter','value','description'],[
            ['email_server', 'smtp.test.ru', 'Сервер отправки почты'],
            ['email_port', '25', 'Порт для отправки'],
            ['email_username','admin', 'Имя пользователя почтового сервера'],
            ['email_password','admin123', 'Пароль от почтового сервера'],
            ['email_from','admin@admin.ru', 'Ящик, с которого происходит отправка'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
