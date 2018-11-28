<?php

use yii\db\Migration;

class m170525_135856_add_user_stat_awaiting_feedbacks extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user`
ADD `stat_awaiting_feedbacks` int(11) NOT NULL DEFAULT '0' AFTER `stat_new_search_requests_offers`;
        ");
    }

    public function down()
    {
        echo "m170525_135856_add_user_stat_awaiting_feedbacks cannot be reverted.\n";

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
