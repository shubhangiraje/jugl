<?php

use yii\db\Migration;

class m170915_172726_change_column_type_interests_and_offer_offer_view_bonus_and_search_request_bonus extends Migration
{
    public function up()
    {
			$this->execute("
            ALTER TABLE `interest`
            CHANGE `offer_view_bonus` `offer_view_bonus` decimal(14,2) NULL AFTER `sort_order` ,
            CHANGE `search_request_bonus` `search_request_bonus` decimal(14,2) NULL  AFTER `offer_view_bonus`;
        ");
    }

    public function down()
    {
        $this->execute("
            ALTER TABLE `interest`
            CHANGE `offer_view_bonus` `offer_view_bonus` int(11) NULL AFTER `sort_order` ,
            CHANGE `search_request_bonus` `search_request_bonus` int(11) NULL AFTER `offer_view_bonus`;
        ");
		
		
		//echo "m170915_172726_change_column_type_interests_and_offer_offer_view_bonus_and_search_request_bonus cannot be reverted.\n"
        //return false;
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
