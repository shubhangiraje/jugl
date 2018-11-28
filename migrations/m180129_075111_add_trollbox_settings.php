<?php

use yii\db\Migration;

class m180129_075111_add_trollbox_settings extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TROLLBOX_STANDART_MESSAGES_PER_DAY',	'Post pro Tag Basis',	'int',	'999'),
            ('TROLLBOX_VIP_MESSAGES_PER_DAY',	'Post pro Tag Premium',	'int',	'999'),
            ('TROLLBOX_VIP_PLUS_MESSAGES_PER_DAY',	'Post pro Tag PremiumPlus',	'int',	'999');
        ");
    }

    public function down()
    {
        echo "m180129_075111_add_trollbox_settings cannot be reverted.\n";

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
