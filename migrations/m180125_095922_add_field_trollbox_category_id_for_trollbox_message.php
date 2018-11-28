<?php

use yii\db\Migration;

class m180125_095922_add_field_trollbox_category_id_for_trollbox_message extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `trollbox_message` ADD `trollbox_category_id` bigint NULL;");
        $this->execute("ALTER TABLE `trollbox_message` ADD INDEX `trollbox_category_id` (`trollbox_category_id`);");
        $this->execute("ALTER TABLE `trollbox_message` ADD FOREIGN KEY (`trollbox_category_id`) REFERENCES `trollbox_category` (`id`)");
    }

    public function down()
    {
        echo "m180125_095922_add_field_trollbox_category_id_for_trollbox_message cannot be reverted.\n";

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
