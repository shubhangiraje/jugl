<?php

use yii\db\Migration;

class m170303_140525_add_user_team_request_pk extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_team_request`
ADD `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
        ");
    }

    public function down()
    {
        echo "m170303_140525_add_user_team_request_pk cannot be reverted.\n";

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
