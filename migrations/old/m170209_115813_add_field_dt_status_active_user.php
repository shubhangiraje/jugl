<?php

use yii\db\Migration;

class m170209_115813_add_field_dt_status_active_user extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user` ADD `dt_status_active` timestamp NULL;");
    }

    public function down()
    {
        echo "m170209_115813_add_field_dt_status_active_user cannot be reverted.\n";

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
