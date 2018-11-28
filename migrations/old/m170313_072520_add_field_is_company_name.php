<?php

use yii\db\Migration;

class m170313_072520_add_field_is_company_name extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user` ADD `is_company_name` tinyint(1) NOT NULL DEFAULT '0' AFTER `nick_name`;");
    }

    public function down()
    {
        echo "m170313_072520_add_field_is_company_name cannot be reverted.\n";

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
