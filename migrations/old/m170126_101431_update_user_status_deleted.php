<?php

use yii\db\Migration;

class m170126_101431_update_user_status_deleted extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE user SET avatar_file_id=5, first_name='', last_name='' WHERE status='DELETED'
        ");
    }

    public function down()
    {
        echo "m170126_101431_update_user_status_deleted cannot be reverted.\n";

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
