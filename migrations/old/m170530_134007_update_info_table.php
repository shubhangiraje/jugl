<?php

use yii\db\Migration;

class m170530_134007_update_info_table extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `info`
            CHANGE `description` `description_de` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `title`,
            ADD `description_en` mediumtext COLLATE 'utf8_general_ci' NULL,
            ADD `description_ru` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `description_en`;");

        $this->execute("ALTER TABLE `info`
            CHANGE `title` `title_de` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `view`,
            ADD `title_en` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `title_de`,
            ADD `title_ru` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `title_en`;");

    }

    public function down()
    {
        echo "m170530_134007_update_info_table cannot be reverted.\n";

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
