<?php

use yii\db\Migration;

class m170918_075936_time_delay_per_invited_member extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `user`
            ADD `delay_invited_member` int(11) NULL DEFAULT '0' AFTER `ad_status_auto`;
        ");
		
		$this->execute("
			INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES ('TIME_DELAY_INVITED_MEMBER', 'Zeit Pause pro eingeladenes Mitglied in Sekunden', 'int', '2')
		");
    }

    public function down()
    {
        echo "m170918_075936_time_delay_per_invited_member cannot be reverted.\n";

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
