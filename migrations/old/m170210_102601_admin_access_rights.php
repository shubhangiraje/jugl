<?php

use yii\db\Migration;

class m170210_102601_admin_access_rights extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `admin`
            ADD `access_dashboard` tinyint(1) NOT NULL DEFAULT '0' AFTER `failed_logins`,
            ADD `access_user_view` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_dashboard`,
            ADD `access_user_update` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_user_view`,
            ADD `access_user_validation` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_user_update`,
            ADD `access_payouts` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_user_validation`,
            ADD `access_interests` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_payouts`,
            ADD `access_search_request_view` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_interests`,
            ADD `access_search_request_validate` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_search_request_view`,
            ADD `access_search_request_update` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_search_request_validate`,
            ADD `access_offer_view` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_search_request_update`,
            ADD `access_offer_validate` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_offer_view`,
            ADD `access_offer_update` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_offer_validate`,
            ADD `access_settings` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_offer_update`,
            ADD `access_broadcast` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_settings`,
            ADD `access_news` tinyint(1) NOT NULL DEFAULT '0' AFTER `access_broadcast`,
            DROP `access_users`,
            DROP `access_payouts`,
            DROP `access_users_validation`;
        ");
    }

    public function down()
    {
        echo "m170210_102601_admin_access_rights cannot be reverted.\n";

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
