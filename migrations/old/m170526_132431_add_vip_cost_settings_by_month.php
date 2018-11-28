<?php

use yii\db\Migration;

class m170526_132431_add_vip_cost_settings_by_month extends Migration
{
    public function up()
    {
        $this->execute("
INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
('VIP_COST_12_MONTHS',	'VIP cost 1 year in €',	'float',	'29'),
('VIP_COST_1_MONTHS',	'VIP cost 1 month in €',	'float',	'3'),
('VIP_COST_36_MONTHS',	'VIP cost 3 years in €',	'float',	'75'),
('VIP_COST_3_MONTHS',	'VIP cost 3 months in €',	'float',	'8.5'),
('VIP_COST_6_MONTHS',	'VIP cost 6 months in €',	'float',	'16'),
('VIP_UPGRADE_COST_12_MONTHS',	'VIP upgrade cost 1 year in €',	'float',	'29'),
('VIP_UPGRADE_COST_1_MONTHS',	'VIP upgrade cost 1 month in €',	'float',	'4.99'),
('VIP_UPGRADE_COST_36_MONTHS',	'VIP upgrade cost 3 years in €',	'float',	'75'),
('VIP_UPGRADE_COST_3_MONTHS',	'VIP upgrade cost 3 months in €',	'float',	'8.5'),
('VIP_UPGRADE_COST_6_MONTHS',	'VIP upgrade cost 6 months in €',	'float',	'16');
        ");
    }

    public function down()
    {
        echo "m170526_132431_add_vip_cost_settings_by_month cannot be reverted.\n";

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
