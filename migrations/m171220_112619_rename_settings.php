<?php

use yii\db\Migration;

class m171220_112619_rename_settings extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 1: Monday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_1_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 2: Tuesday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_2_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 3: Wednesday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_3_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 4: Thursday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_4_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 5: Friday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_5_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 6: Saturday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_6_JUGLS';
        ");

        $this->execute("
            UPDATE `setting` SET
            `title` = 'Cash for likes 7: Sunday (Jugls)'
            WHERE `name` = 'CASH_FOR_LIKES_7_JUGLS';
        ");

    }

    public function down()
    {
        echo "m171220_112619_rename_settings cannot be reverted.\n";

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
