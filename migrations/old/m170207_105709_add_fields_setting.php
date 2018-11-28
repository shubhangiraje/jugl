<?php

use yii\db\Migration;

class m170207_105709_add_fields_setting extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `setting`
                CHANGE `type` `type` enum('int','float','string','bool') COLLATE 'utf8_general_ci' NOT NULL AFTER `title`;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
                VALUES ('VALIDATE_OFFER_WITH_BONUS', 'Werbung mit Werbebonus vor Upload kontrollieren', 4, 'false');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
                VALUES ('VALIDATE_OFFER_WITHOUT_BONUS', 'Werbung ohne Werbebonus vor Upload kontrollieren', 4, 'false');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
                VALUES ('VALIDATE_SEARCH_REQUEST', 'Suchaufträge vor dem Upload kontrollieren', 4, 'false');
        ");

    }

    public function down()
    {
        echo "m170207_105709_add_fields_setting cannot be reverted.\n";

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
