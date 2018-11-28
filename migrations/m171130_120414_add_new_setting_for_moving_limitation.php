<?php

use yii\db\Migration;

class m171130_120414_add_new_setting_for_moving_limitation extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('PARENT_MOVING_USER_LIMIT', 'Max. Anzahl der User, die in das Netzwerk anderer User verschoben werden dürfen', 1, '100');
        ");
    }

    public function down()
    {
        echo "m171130_120414_add_new_setting_for_moving_limitation cannot be reverted.\n";

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
