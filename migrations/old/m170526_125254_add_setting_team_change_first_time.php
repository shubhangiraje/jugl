<?php

use yii\db\Migration;

class m170526_125254_add_setting_team_change_first_time extends Migration
{
    public function up()
    {
        $this->execute("INSERT INTO `setting` (`name`, `title`, `type`, `value`) 
            VALUES ('TEAM_CHANGE_FIRST_TIME', 'Zeit bis zum nächsten Teamwechsel (Min.)', 1, '15');");
    }

    public function down()
    {
        echo "m170526_125254_add_setting_team_change_first_time cannot be reverted.\n";

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
