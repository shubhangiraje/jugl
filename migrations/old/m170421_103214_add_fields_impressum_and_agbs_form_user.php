<?php

use yii\db\Migration;

class m170421_103214_add_fields_impressum_and_agbs_form_user extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user`
            ADD `company_manager` varchar(64) NULL,
            ADD `impressum` mediumtext NULL AFTER `company_manager`,
            ADD `agb` mediumtext NULL AFTER `impressum`;
        ");
    }

    public function down()
    {
        echo "m170421_103214_add_fields_impressum_and_agbs_form_user cannot be reverted.\n";

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
