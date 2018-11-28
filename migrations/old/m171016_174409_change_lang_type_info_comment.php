<?php

use yii\db\Migration;

class m171016_174409_change_lang_type_info_comment extends Migration
{
    public function up()
    {	
		$this->execute("
            UPDATE `info_comment`
			SET `lang` = 64 ;
			;
        ");
		$this->execute("
            ALTER TABLE `info_comment`
			CHANGE `lang` `lang` int(3) COLLATE 'utf8_general_ci' NOT NULL ;
			;
        ");
		$this->execute("
            UPDATE `info_comment`
			SET `lang` = 64 ;
			;
        ");
    
    }

    public function down()
    {
        echo "m171016_174409_change_lang_type_info_comment cannot be reverted.\n";

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
