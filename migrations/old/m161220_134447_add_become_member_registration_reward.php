<?php

use yii\db\Migration;

class m161220_134447_add_become_member_registration_reward extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('STANDARD_COST_JUGL_BECOME_MEMBER',	'STANDARD Registration reward in Jugl (Midglied werden)',	'float',	'100'),
            ('VIP_COST_JUGL_BECOME_MEMBER',	'VIP Registration reward in Jugl (Midglied werden)',	'float',	'100');
        ");
    }

    public function down()
    {
        echo "m161220_134447_add_become_member_registration_reward cannot be reverted.\n";

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
