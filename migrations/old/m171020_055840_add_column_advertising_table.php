<?php

use yii\db\Migration;

class m171020_055840_add_column_advertising_table extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `advertising_display_name` varchar(255) NOT NULL AFTER `advertising_name`;
        ");
		
		$this->execute("
            INSERT INTO 
			advertising (advertising_name, advertising_display_name, advertising_type, advertising_total_bonus, provider, banner_height, banner_width, advertising_position, advertising_total_views, advertising_total_clicks, banner, link, user_bonus, status)
			VALUES ('SponsorAds Medium Rectangle (300x250)', 'Banner Werbung', 'click', '100', 'sponsorads', '250', '300', 'forum-top', '0', '1000', '', 'https://www.sponsorads.de/script.php?s=268831', '10.00', '1')
        ");
		
		$this->execute("
            INSERT INTO 
			advertising (advertising_name, advertising_display_name, advertising_type, advertising_total_bonus, provider, banner_height, banner_width, advertising_position, advertising_total_views, advertising_total_clicks, banner, link, user_bonus, status)
			VALUES ('SponsorAds Leaderboard (728x90)', 'Banner Werbung', 'click', '80', 'sponsorads', '90', '728', 'forum-bottom', '0', '1000', '', 'https://www.sponsorads.de/script.php?s=268867', '8.00', '1')
        ");
    }

    public function down()
    {
        echo "m171020_055840_add_column_advertising_table cannot be reverted.\n";

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
