<?php

use yii\db\Schema;
use yii\db\Migration;

class m150312_121913_add_balance_log_item_comment extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `balance_log`
            ADD `comment` varchar(128) NULL;
        ");

        $this->execute("
            update balance_log set comment='Kauf Einladungsgutschein' where type='OUT';
        ");

        $this->execute("
            update balance_log set comment='Auszahlung Jugls' where type='PAYOUT';
        ");

        $this->execute("
            update balance_log set comment='Registrierung' where type in ('IN_REG_REF','IN_REG_REF_REF');
        ");

    }

    public function down()
    {
        echo "m150312_121913_add_balance_log_item_comment cannot be reverted.\n";

        return false;
    }
}
