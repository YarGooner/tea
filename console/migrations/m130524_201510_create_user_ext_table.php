<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_ext`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m130524_201510_create_user_ext_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_ext}}', [
            'id' => $this->primaryKey(),

            //
            'user_id' => $this->integer(11)->notNull(),

            // name
            'first_name' => $this->string(30),
            'middle_name' => $this->string(30),
            'last_name' => $this->string(30),

            // phone
            'phone' => $this->string(25),

			// email
			'unconfirmed_email' => $this->string(255),
			'email' => $this->string(255),
            'email_confirm_token' => $this->string(255),
            'email_is_verified' => $this->boolean()->notNull()->defaultValue(0),
            'email_verified_at' => $this->integer(11),

            //
            'rules_accepted' => $this->boolean()->notNull()->defaultValue(0),
        ]);
		
		// creates index for column `email`
        $this->createIndex(
            'idx-user_ext-email',
            'user_ext',
            'email'
        );

        // creates index for column `unconfirmed_email`
        $this->createIndex(
            'idx-user_ext-unconfirmed_email',
            'user_ext',
            'unconfirmed_email'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-user_ext-user_id',
            'user_ext',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-user_ext-user_id',
            'user_ext',
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
            'fk-user_ext-user_id',
            'user_ext'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-user_ext-user_id',
            'user_ext'
        );

        $this->dropIndex(
            'idx-user_ext-unconfirmed_email',
            'user_ext'
        );

		$this->dropIndex(
            'idx-user_ext-email',
            'user_ext'
        );
		
        $this->dropTable('{{%user_ext}}');
    }
}
