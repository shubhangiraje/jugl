<?php

use yii\db\Migration;

class m170510_140127_add_table_faq extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `faq` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `question` varchar(256) NOT NULL,
          `response` mediumtext NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        echo "m170510_140127_add_table_faq cannot be reverted.\n";

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
