<?php

use yii\db\Migration;

class m170321_074141_add_field_dt_parent_change extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user` ADD `dt_parent_change` timestamp NULL;");
    }

    public function down()
    {
        echo "m170321_074141_add_field_dt_parent_change cannot be reverted.\n";

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
