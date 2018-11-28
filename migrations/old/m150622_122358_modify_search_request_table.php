<?php

use yii\db\Schema;
use yii\db\Migration;

class m150622_122358_modify_search_request_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
            CHANGE `active_till` `active_till` date NOT NULL AFTER `address`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `search_request`
            ADD `create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150622_122358_modify_search_request_table cannot be reverted.\n";

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
