<?php

use yii\db\Migration;

class m171205_122655_add_vip_plus_settings extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('VIP_PLUS_UPGRADE_COST_CURRENCY',	'VIP plus upgrade cost in €',	'float',	'49'),
            ('VIP_PLUS_COST_CURRENCY',	'VIP plus cost in €',	'float',	'29');
        ");
    }

    public function down()
    {
        echo "m171205_122655_add_vip_plus_settings cannot be reverted.\n";

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
