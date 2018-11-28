<?php

use yii\db\Migration;

class m170421_140449_update_field_company_manager_for_user extends Migration
{
    public function up()
    {
        $this->execute("UPDATE user SET company_manager=CONCAT(first_name,' ',last_name) WHERE is_company_name=1;");
    }

    public function down()
    {
        echo "m170421_140449_update_field_company_manager_for_user cannot be reverted.\n";

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
