<?php

use yii\db\Migration;

class m180628_103434_change_default_value_for_is_update_country_after_login extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` CHANGE `is_update_country_after_login` `is_update_country_after_login` tinyint(1) NOT NULL DEFAULT \'1\' AFTER `allow_country_change`;');
    }

    public function down()
    {
        echo "m180628_103434_change_default_value_for_is_update_country_after_login cannot be reverted.\n";

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
