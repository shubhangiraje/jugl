<?php

use yii\db\Migration;

class m171124_132511_add_vip_plus_free_registrations_limit extends Migration
{
    public function up()
    {
        $this->execute("update setting set `name`='STANDART_FREE_REGISTRATIONS_LIMIT' where `name`='STANDARD_FREE_REGISTRATIONS_LIMIT'");
        $this->execute("INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
('VIP_PLUS_FREE_REGISTRATIONS_LIMIT',	'PREMIUMPLUS: Einladungskontingent',	'int',	'999999');");
    }

    public function down()
    {
        echo "m171124_132511_add_vip_plus_free_registrations_limit cannot be reverted.\n";

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
