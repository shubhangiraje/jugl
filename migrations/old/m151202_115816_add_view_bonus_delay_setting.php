<?php

use yii\db\Schema;
use yii\db\Migration;

class m151202_115816_add_view_bonus_delay_setting extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('VIEW_BONUS_DELAY', 'Werbungbonus Anzeigepause, sekunden', 1, '30');
        ");
    }

    public function down()
    {
        echo "m151202_115816_add_view_bonus_delay_setting cannot be reverted.\n";

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
