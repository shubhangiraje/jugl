<?php

use yii\db\Migration;

class m171009_161442_add_faq_column_de_en_ru extends Migration
{
    public function up()
    {
	$this->execute("
            ALTER TABLE `faq`
			CHANGE `question` `question_de` mediumtext COLLATE 'utf8_general_ci' NOT NULL ,
			CHANGE `response` `response_de` mediumtext COLLATE 'utf8_general_ci' NOT NULL ,
            ADD `question_en` mediumtext NULL DEFAULT NULL AFTER `question_de`,
            ADD `question_ru` mediumtext NULL DEFAULT NULL AFTER `question_en`,
			ADD `response_en` mediumtext NULL DEFAULT NULL AFTER `response_de`,
            ADD `response_ru` mediumtext NULL DEFAULT NULL AFTER `response_en`
			;
        ");
    }

    public function down()
    {
        echo "m171009_161442_add_faq_column_de_en_ru cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
