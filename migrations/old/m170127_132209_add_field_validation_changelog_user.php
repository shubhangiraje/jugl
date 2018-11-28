<?php

use yii\db\Migration;

class m170127_132209_add_field_validation_changelog_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `validation_changelog` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `validation_details`;
        ");
    }

    public function down()
    {
        echo "m170127_132209_add_field_validation_changelog_user cannot be reverted.\n";

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
