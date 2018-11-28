<?php

use yii\db\Schema;
use yii\db\Migration;

class m150605_115122_add_param_sorting extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `param`
            ADD `sort_order` int NULL;
        ");
    }

    public function down()
    {
        echo "m150605_115122_add_param_sorting cannot be reverted.\n";

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
