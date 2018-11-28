<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_132809_add_jugl_exchange_rate extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('EXCHANGE_JUGLS_PER_EURO', 'Jugls per one EUR', 2, '100');
        ");
    }

    public function down()
    {
        echo "m150921_132809_add_jugl_exchange_rate cannot be reverted.\n";

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
