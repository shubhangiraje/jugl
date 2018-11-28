<?php

use yii\db\Migration;

class m170706_130511_add_lang_info_comment extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `info_comment` ADD `lang` varchar(2) NOT NULL DEFAULT 'de';");
    }

    public function down()
    {
        echo "m170706_130511_add_lang_info_comment cannot be reverted.\n";

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
