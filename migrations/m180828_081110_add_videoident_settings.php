<?php

use yii\db\Migration;

class m180828_081110_add_videoident_settings extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('VIDEOIDENT_AUTO_ACCEPT_SCORE',	'Automatische Entscheidung „Echt“',	'int',	'5'),
            ('VIDEOIDENT_AUTO_REJECT_SCORE',	'Automatische Entscheidung „Nicht Echt“',	'int',	'-5'),
            ('VIDEOIDENT_MIN_SCORE_FOR_VOTING',	'Mindestanzahl an Pluspunkten für die Abstimmung',	'int',	'5'),
            ('VIDEOIDENT_SCORE_MINUS_IF_VOTE_UNMATCH',	'Minuspunkte für falsche Video Ident Abstimmung',	'int',	'1'),
            ('VIDEOIDENT_SCORE_PLUS_IF_VOTE_MATCH',	'Pluspunkte für richtige Video Ident Abstimmung:',	'int',	'1');
        ");
    }

    public function down()
    {
        echo "m180828_081110_add_videoident_settings cannot be reverted.\n";

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
