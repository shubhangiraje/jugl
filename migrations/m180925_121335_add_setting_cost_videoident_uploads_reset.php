<?php

use yii\db\Migration;

class m180925_121335_add_setting_cost_videoident_uploads_reset extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('VIDEOIDENT_UPLOADS_RESET_JUGL_COST', 'Preis fürs Zurücksetzen der Anzahl der Videoidentifikationen', 2, '1000');
        ");
    }

    public function down()
    {
        echo "m180925_121335_add_setting_cost_videoident_uploads_reset cannot be reverted.\n";

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
