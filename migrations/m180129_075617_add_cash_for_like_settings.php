<?php

use yii\db\Migration;

class m180129_075617_add_cash_for_like_settings extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('CASH_FOR_LIKES_MIN_FOLLOWERS',	'Cash for likes: Vergütung ab Anzahl Abonnenten',	'int',	'0'),
            ('CASH_FOR_LIKES_MIN_POST_LIKES',	'Cash for likes: Vergütung ab Anzahl Likes pro Post',	'int',	'1');
        ");
    }

    public function down()
    {
        echo "m180129_075617_add_cash_for_like_settings cannot be reverted.\n";

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
