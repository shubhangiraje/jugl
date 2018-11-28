<?php

use yii\db\Migration;

class m180819_150939_user extends Migration
{
    public function up()
    {
        $this->execute("alter table admin
	    add `access_translator` tinyint(1) DEFAULT '0';");
    }

    public function down()
    {
        echo "m180810_174347_add_access_translator_to_admin_table cannot be reverted.\n";

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
