<?php

use yii\db\Migration;

class m180306_073522_add_field_earned_total_for_user extends Migration
{
    public function up()
    {
        /*
        $this->execute('ALTER TABLE `user` ADD `earned_total` decimal(17,5) NOT NULL DEFAULT \'0.00000\' AFTER `balance_earned`;');

        $this->execute('
            update user
                join (
                  select user_id,sum(sum) as sum_total
                  from balance_log
                  where sum>0 and type!=\'PAYIN\'
                  group by user_id
                ) as tmp on (tmp.user_id=user.id)
            set earned_total=tmp.sum_total
        ');
        */
    }

    public function down()
    {
        echo "m180306_073522_add_field_earned_total_for_user cannot be reverted.\n";

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
