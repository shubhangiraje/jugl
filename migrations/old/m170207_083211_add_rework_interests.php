<?php

use yii\db\Migration;

class m170207_083211_add_rework_interests extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `interest`
            ADD `type` enum('OFFER','SEARCH_REQUEST') NOT NULL;
        ");


        $this->execute("
            insert into interest (id,parent_id,title,file_id,sort_order,offer_view_bonus,search_request_bonus,type) 
            select id*1000,parent_id*1000,title,file_id,sort_order,offer_view_bonus,search_request_bonus,'SEARCH_REQUEST' from interest 
        ");

        $this->execute("
            update search_request_interest
            set 
            level1_interest_id=1000*level1_interest_id,
            level2_interest_id=1000*level2_interest_id,
            level3_interest_id=1000*level3_interest_id
        ");

        $this->execute("
            ALTER TABLE `user_interest`
            ADD `type` enum('OFFER','SEARCH_REQUEST') NOT NULL;
        ");

        $this->execute("
            insert into user_interest(user_id,level1_interest_id,level2_interest_id,level3_interest_id,type) 
            select user_id,level1_interest_id*1000,level2_interest_id*1000,level3_interest_id*1000,'SEARCH_REQUEST' from user_interest
        ");

        $this->execute("
            insert into user_offer_request_completed_interest(user_id,interest_id) 
            select user_id,interest_id*1000 from user_offer_request_completed_interest
        ");

        $this->execute("
            insert into interest_param_value(interest_id,param_id,param_value_id) 
            select interest_id*1000,param_id,param_value_id from interest_param_value
        ");

    }

    public function down()
    {
        echo "m170207_083211_add_rework_interests cannot be reverted.\n";

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
