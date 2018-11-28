<?php

use yii\db\Migration;

class m180604_065409_add_field_is_update_county_after_login_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user`ADD `is_update_country_after_login` tinyint(1) NOT NULL DEFAULT \'0\';');
    }

    public function down()
    {
        echo "m180604_065409_add_field_is_update_county_after_login_for_user cannot be reverted.\n";

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
