<?php

use yii\db\Migration;

class m171212_093725_add_setting_accepted_auto_offer_and_search_request_for_vip_plus extends Migration
{
    public function up()
    {
        $this->execute("INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('ACCEPTED_AUTO_OFFER_AND_SEARCH_REQUEST_FOR_VIP_PLUS', 'Automatisch Aufträge/Werbung akzeptieren für PremiumPlus-Mitglieder', 4, '1');");
    }

    public function down()
    {
        echo "m171212_093725_add_setting_accepted_auto_offer_and_search_request_for_vip_plus cannot be reverted.\n";

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
