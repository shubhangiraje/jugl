<?php

use yii\db\Migration;

class m171128_144137_insert_advertising_setting extends Migration
{
    public function up()
    {
		$this->execute("INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES ('ADVERTISING_OFFER_MOD_COUNT', 'Interval \"Neuste Werbung\" Anzeigen schalten', 1, '2')");
    }

    public function down()
    {
        echo "m171128_144137_insert_advertising_setting cannot be reverted.\n";

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
