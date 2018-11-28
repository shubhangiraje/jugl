<?php

use yii\db\Migration;

class m180402_132457_add_field_allow_moderator_country_change_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `allow_moderator_country_change` tinyint(1) NOT NULL DEFAULT \'0\';');
    }

    public function down()
    {
        echo "m180402_132457_add_field_allow_moderator_country_change_for_user cannot be reverted.\n";

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
