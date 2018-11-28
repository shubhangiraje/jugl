<?php

use yii\db\Schema;
use yii\db\Migration;

class m150622_121009_modify_param_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `param`
            ADD `required` tinyint(1) NOT NULL AFTER `type`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150622_121009_modify_param_table cannot be reverted.\n";

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
