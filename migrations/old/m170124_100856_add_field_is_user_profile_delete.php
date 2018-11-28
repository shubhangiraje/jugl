<?php

use yii\db\Migration;

class m170124_100856_add_field_is_user_profile_delete extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `is_user_profile_delete` tinyint(1) NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m170124_100856_add_field_is_user_profile_delete cannot be reverted.\n";

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
