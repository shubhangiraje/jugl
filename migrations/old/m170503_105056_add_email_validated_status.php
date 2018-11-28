<?php

use yii\db\Migration;

class m170503_105056_add_email_validated_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `status` `status` enum('EMAIL_VALIDATION','REGISTERED','LOGINED','ACTIVE','BLOCKED','DELETED') COLLATE 'utf8_general_ci' NULL AFTER `company_name`;
        ");
    }

    public function down()
    {
        echo "m170503_105056_add_email_validated_status cannot be reverted.\n";

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
