<?php

use yii\db\Migration;

class m170208_093741_add_field_dt_status_change extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user` ADD `dt_status_change` timestamp NULL;
        ");

        $this->execute("
            ALTER TABLE `user`
                DROP `dt_user_blocked`,
                DROP `dt_admin_delete`,
                DROP `dt_user_delete`,
                DROP `dt_active_validation`;
        ");

    }

    public function down()
    {
        echo "m170208_093741_add_field_dt_status_change cannot be reverted.\n";

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
