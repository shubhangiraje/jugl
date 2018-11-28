<?php

use yii\db\Migration;

class m180228_144350_add_offer_draft_table extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE `offer_draft` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_id` bigint(20) NOT NULL,
          `data` mediumtext NULL,
          `create_dt` timestamp NULL,
          FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=\'InnoDB\' COLLATE \'utf8_general_ci\';');
    }

    public function down()
    {
        echo "m180228_144350_add_offer_draft_table cannot be reverted.\n";

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
