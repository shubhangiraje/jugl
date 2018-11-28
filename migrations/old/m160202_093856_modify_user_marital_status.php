<?php

use yii\db\Schema;
use yii\db\Migration;

class m160202_093856_modify_user_marital_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
              CHANGE `marital_status` `marital_status` enum('single','married','vergeben') COLLATE 'utf8_general_ci' NULL AFTER `visibility_profession`;
        ");
    }

    public function down()
    {
        echo "m160202_093856_modify_user_marital_status cannot be reverted.\n";

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
