<?php

use yii\db\Migration;

class m170522_083656_modify_dt_full_for_user_activity_log extends Migration
{
    public function up()
    {
        $this->execute("UPDATE user_activity_log SET dt_full = dt WHERE CAST(dt_full as CHAR)='0000-00-00 00:00:00';");
    }

    public function down()
    {
        echo "m170522_083656_modify_dt_full_for_user_activity_log cannot be reverted.\n";

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
