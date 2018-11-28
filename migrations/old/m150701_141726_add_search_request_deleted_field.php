<?php

use yii\db\Schema;
use yii\db\Migration;

class m150701_141726_add_search_request_deleted_field extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
            ADD `deleted` tinyint(1) NOT NULL DEFAULT '0',
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150701_141726_add_search_request_deleted_field cannot be reverted.\n";

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
