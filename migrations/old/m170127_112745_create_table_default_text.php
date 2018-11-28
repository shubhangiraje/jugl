<?php

use yii\db\Migration;

class m170127_112745_create_table_default_text extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `default_text` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `text` mediumtext NOT NULL,
                `category` varchar(128) DEFAULT NULL,
                  PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");
    }

    public function down()
    {
        echo "m170127_112745_create_table_default_text cannot be reverted.\n";

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
