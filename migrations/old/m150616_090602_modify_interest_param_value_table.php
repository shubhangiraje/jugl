<?php

use yii\db\Schema;
use yii\db\Migration;

class m150616_090602_modify_interest_param_value_table extends Migration
{
    public function up()
    {
        $this->execute("
          DROP TABLE IF EXISTS `interest_param_value`;
        ");

        $this->execute("
            CREATE TABLE `interest_param_value` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `interest_id` int(11) NOT NULL,
              `param_id` int(11) NOT NULL,
              `param_value_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `param_id` (`param_id`),
              KEY `param_value_id` (`param_value_id`),
              KEY `interest_id` (`interest_id`),
              CONSTRAINT `interest_param_value_ibfk_1` FOREIGN KEY (`interest_id`) REFERENCES `interest` (`id`),
              CONSTRAINT `interest_param_value_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `param` (`id`),
              CONSTRAINT `interest_param_value_ibfk_3` FOREIGN KEY (`param_value_id`) REFERENCES `param_value` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150616_090602_modify_interest_param_value_table cannot be reverted.\n";

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
