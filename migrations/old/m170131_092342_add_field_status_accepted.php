<?php

use yii\db\Migration;

class m170131_092342_add_field_status_accepted extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `pay_out_request` ADD `dt_accepted` timestamp NULL;");
    }

    public function down()
    {
        echo "m170131_092342_add_field_status_accepted cannot be reverted.\n";

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
