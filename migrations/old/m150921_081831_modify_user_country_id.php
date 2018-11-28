<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_081831_modify_user_country_id extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
              CHANGE `country_id` `country_id` int(11) NULL AFTER `registration_dt`;
        ");
    }

    public function down()
    {
        echo "m150921_081831_modify_user_country_id cannot be reverted.\n";

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
