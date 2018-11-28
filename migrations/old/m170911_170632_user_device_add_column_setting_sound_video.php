<?php

use yii\db\Migration;

class m170911_170632_user_device_add_column_setting_sound_video extends Migration
{
    public function up()
    {
		$this->execute("
				ALTER TABLE `user_device`
				ADD `setting_sound_video` tinyint(1) NOT NULL DEFAULT '1';
		");
    }

    public function down()
    {
        echo "m170911_170632_user_device_add_column_setting_sound_video cannot be reverted.\n";

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
