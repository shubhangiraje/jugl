<?php

use yii\db\Migration;

class m180427_073118_add_field_for_user_table extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `allow_country_change` tinyint(1) NOT NULL DEFAULT \'0\';');
    }

    public function down()
    {
        echo "m180427_073118_add_field_for_user_table cannot be reverted.\n";

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
