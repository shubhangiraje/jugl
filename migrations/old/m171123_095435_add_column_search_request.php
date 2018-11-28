<?php

use yii\db\Migration;

class m171123_095435_add_column_search_request extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `search_request`
			ADD `search_request_type` enum('EXTERNAL_AD', 'STANDART') NULL DEFAULT 'STANDART' AFTER `id` ,
            ADD `feedback_text_de` mediumtext NULL AFTER `validation_status`,
			ADD `feedback_text_en` mediumtext NULL AFTER `feedback_text_de`,
			ADD `feedback_text_ru` mediumtext NULL AFTER `feedback_text_en`;
        ");
    }

    public function down()
    {
        echo "m171123_095435_add_column_search_request cannot be reverted.\n";

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
