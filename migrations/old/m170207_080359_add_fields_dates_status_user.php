<?php

use yii\db\Migration;

class m170207_080359_add_fields_dates_status_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `dt_user_blocked` timestamp NULL,
                ADD `dt_admin_delete` timestamp NULL AFTER `dt_user_blocked`,
                ADD `dt_user_delete` timestamp NULL AFTER `dt_admin_delete`,
                ADD `dt_active_validation` timestamp NULL AFTER `dt_user_delete`;
        ");
    }

    public function down()
    {
        echo "m170207_080359_add_fields_dates_status_user cannot be reverted.\n";

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
