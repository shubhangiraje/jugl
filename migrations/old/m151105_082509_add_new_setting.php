<?php

use yii\db\Schema;
use yii\db\Migration;

class m151105_082509_add_new_setting extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('FREE_REGISTRATIONS_LIMIT', 'Maximal zugelassene Anzahl der Registrierungen auf Einladungen von einem Benutzer', 1, '100');
        ");
    }

    public function down()
    {
        echo "m151105_082509_add_new_setting cannot be reverted.\n";

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
