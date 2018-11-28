<?php

use yii\db\Migration;

class m170113_110752_add_view_bonus_interest extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `interest`
                ADD `view_bonus` int(11) NULL;
        ");
    }

    public function down()
    {
        echo "m170113_110752_add_view_bonus_interest cannot be reverted.\n";
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
