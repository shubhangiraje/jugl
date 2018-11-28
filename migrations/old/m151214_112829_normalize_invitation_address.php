<?php

use yii\db\Schema;
use yii\db\Migration;

class m151214_112829_normalize_invitation_address extends Migration
{
    public function up()
    {
        foreach(\app\models\Invitation::find()->each() as $invitation) {
            $invitation->normalizeAddress();
            $invitation->save();
        }
    }

    public function down()
    {
        echo "m151214_112829_normalize_invitation_address cannot be reverted.\n";

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
