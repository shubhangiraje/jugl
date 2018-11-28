<?php

use yii\db\Migration;

class m170209_072602_add_field_reject_reason_offer extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer` ADD `reject_reason` mediumtext COLLATE 'utf8_general_ci' NULL;
        ");
    }

    public function down()
    {
        echo "m170209_072602_add_field_reject_reason_offer cannot be reverted.\n";

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
