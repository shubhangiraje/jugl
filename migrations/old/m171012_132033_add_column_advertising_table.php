<?php

use yii\db\Migration;

class m171012_132033_add_column_advertising_table extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `banner` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `advertising_total_views`,
			ADD `link` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `banner`;
        ");
    }

    public function down()
    {
        echo "m171012_132033_add_column_advertising_table cannot be reverted.\n";

        return false;
    }
}
