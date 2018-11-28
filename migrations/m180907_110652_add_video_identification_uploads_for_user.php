<?php

use yii\db\Migration;

class m180907_110652_add_video_identification_uploads_for_user extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `user` ADD `video_identification_uploads` int(11) NOT NULL DEFAULT \'0\' AFTER `video_identification_score`;');
    }

    public function down()
    {
        echo "m180907_110652_add_video_identification_uploads_for_user cannot be reverted.\n";

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
