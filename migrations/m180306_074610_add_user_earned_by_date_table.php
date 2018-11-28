<?php

use yii\db\Migration;

class m180306_074610_add_user_earned_by_date_table extends Migration
{
    public function up()
    {
        /*
        $this->execute('CREATE TABLE `user_earned_by_date` (
            `user_id` bigint(20) NOT NULL,
            `dt` date NOT NULL,
            `sum` decimal(17,5) NOT NULL,
            PRIMARY KEY (`user_id`,`dt`),
            CONSTRAINT `user_earned_by_date_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');

        $this->execute('
            insert into user_earned_by_date(user_id,dt,sum) 
                select user_id,DATE(dt) as dtdate,sum(sum) as sum_total
                from balance_log
                where sum>0 and type!=\'PAYIN\'
                group by user_id,dtdate
            ');
        */

    }

    public function down()
    {
        echo "m180306_074610_add_user_earned_by_date_table cannot be reverted.\n";

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
