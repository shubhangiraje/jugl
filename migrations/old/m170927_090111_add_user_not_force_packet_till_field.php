<?php

use yii\db\Migration;

class m170927_090111_add_user_not_force_packet_till_field extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `not_force_packet_selection_till` timestamp NULL AFTER `packet`;
        ");
    }

    public function down()
    {
        echo "m170927_090111_add_user_not_force_packet_till_field cannot be reverted.\n";

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
