<?php

use yii\db\Schema;
use yii\db\Migration;

class m151202_063942_settings_change extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `setting` SET
            `name` = 'VIP_COST_JUGL',
            `title` = 'VIP Registration reward in Jugl',
            `type` = 2,
            `value` = '100'
            WHERE `name` = 'VIP_COST_JUGL' AND `name` = 'VIP_COST_JUGL' COLLATE utf8_bin;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('STANDARD_COST_JUGL', 'STANDARD Registration reward in Jugl', 2, '100');
        ");
    }

    public function down()
    {
        echo "m151202_063942_settings_change cannot be reverted.\n";

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
