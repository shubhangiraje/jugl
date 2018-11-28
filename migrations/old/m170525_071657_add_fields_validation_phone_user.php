<?php

use yii\db\Migration;

class m170525_071657_add_fields_validation_phone_user extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user`
            ADD `validation_phone_status` enum('NOT_VALIDATED','SEND_CODE','VALIDATED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'NOT_VALIDATED' AFTER `agb`,
            ADD `validation_phone` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `validation_phone_status`,
            ADD `validation_code` varchar(8) COLLATE 'utf8_general_ci' NULL AFTER `validation_phone`;");
    }

    public function down()
    {
        echo "m170525_071657_add_fields_validation_phone_user cannot be reverted.\n";

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
