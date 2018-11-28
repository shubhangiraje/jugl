<?php

use yii\db\Migration;

class m171017_110621_add_cash_for_likes_setting extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_1_JUGLS', 'Cash for likes Monday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_2_JUGLS', 'Cash for likes Tuesday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_3_JUGLS', 'Cash for likes Wednesday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_4_JUGLS', 'Cash for likes Thursday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_5_JUGLS', 'Cash for likes Friday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_6_JUGLS', 'Cash for likes Saturday (Jugls)', 2, '0');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('CASH_FOR_LIKES_7_JUGLS', 'Cash for likes Sunday (Jugls)', 2, '0');
        ");
    }

    public function down()
    {
        echo "m171017_110621_add_cash_for_likes_setting cannot be reverted.\n";

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
