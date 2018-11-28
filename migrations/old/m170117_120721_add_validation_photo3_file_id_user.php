<?php

use yii\db\Migration;

class m170117_120721_add_validation_photo3_file_id_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `validation_photo3_file_id` bigint(20) NULL AFTER `validation_photo2_file_id`,
                ADD FOREIGN KEY (`validation_photo3_file_id`) REFERENCES `file` (`id`);
        ");
    }

    public function down()
    {
        echo "m170117_120721_add_validation_photo3_file_id_user cannot be reverted.\n";

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
