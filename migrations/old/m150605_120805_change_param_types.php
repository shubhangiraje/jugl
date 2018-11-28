<?php

use yii\db\Schema;
use yii\db\Migration;

class m150605_120805_change_param_types extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `param`
            CHANGE `type` `type` enum('LIST','INT') COLLATE 'utf8_general_ci' NOT NULL AFTER `title`;
        ");
    }

    public function down()
    {
        echo "m150605_120805_change_param_types cannot be reverted.\n";

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
