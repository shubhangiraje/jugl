<?php

use yii\db\Schema;
use yii\db\Migration;

class m151211_124845_add_indexes extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `invitation`
            ADD INDEX `address` (`address`);
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `phone` (`phone`);
        ");
    }

    public function down()
    {
        echo "m151211_124845_add_indexes cannot be reverted.\n";

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
