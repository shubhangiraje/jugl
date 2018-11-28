<?php

use yii\db\Migration;

class m171010_063943_add_columns_to_news_de_en_ru extends Migration
{
    public function up()
    {
	$this->execute("
            ALTER TABLE `news`
			CHANGE `title` `title_de` varchar(256) COLLATE 'utf8_general_ci' NOT NULL ,
			CHANGE `text` `text_de` mediumtext COLLATE 'utf8_general_ci' NOT NULL ,
            ADD `title_en` varchar(256) NULL DEFAULT NULL AFTER `title_de`,
            ADD `title_ru` varchar(256) NULL DEFAULT NULL AFTER `title_en`,
			ADD `text_en` mediumtext NULL DEFAULT NULL AFTER `text_de`,
            ADD `text_ru` mediumtext NULL DEFAULT NULL AFTER `text_en`
			;
        ");
    }

    public function down()
    {
        echo "m171010_063943_add_columns_to_news_de_en_ru cannot be reverted.\n";

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
