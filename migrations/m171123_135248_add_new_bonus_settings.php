<?php

use yii\db\Migration;

class m171123_135248_add_new_bonus_settings extends Migration
{
    public function up()
    {
        $this->execute("
            insert into setting(name,title,type,value) values 
            ('BONUS_IAM_STANDART_REGISTER_STANDART','Basismitglied als Basismitglied eingeladen','float',100),
            ('BONUS_IAM_STANDART_REGISTER_VIP','Basismitglied als Premiummitglied eingeladen','float',200),
            ('BONUS_IAM_STANDART_REGISTER_VIP_PLUS','Basismitglied als PremiumPlusmitglied eingeladen','float',300),
            ('BONUS_IAM_VIP_REGISTER_STANDART','Premiummitglied als Basismitglied eingeladen','float',101),
            ('BONUS_IAM_VIP_REGISTER_VIP','Premiummitglied als Premiummitglied eingeladen','float',201),
            ('BONUS_IAM_VIP_REGISTER_VIP_PLUS','Premiummitglied als PremiumPlusmitglied eingeladen','float',301),
            ('BONUS_IAM_VIP_PLUS_REGISTER_STANDART','PremiumPlusmitglied als Basismitglied eingeladen','float',102),
            ('BONUS_IAM_VIP_PLUS_REGISTER_VIP','PremiumPlusmitglied als Premiummitglied eingeladen','float',202),
            ('BONUS_IAM_VIP_PLUS_REGISTER_VIP_PLUS','PremiumPlusmitglied als PremiumPlusmitglied eingeladen','float',302),
            ('BONUS_IAM_STANDART_UPGRADE_STANDART_VIP','Basismitglied upgraded auf Premium und ich bin Basismitglied','float',110),
            ('BONUS_IAM_VIP_UPGRADE_STANDART_VIP','Basismitglied upgraded auf Premium und ich bin Premiummitglied','float',111),
            ('BONUS_IAM_VIP_PLUS_UPGRADE_STANDART_VIP','Basismitglied upgraded auf Premium und ich bin PremiumPlusmitglied','float',112),
            ('BONUS_IAM_STANDART_UPGRADE_STANDART_VIP_PLUS','Basismitglied upgraded auf PremiumPlus und ich bin Basismitglied','float',110),
            ('BONUS_IAM_VIP_UPGRADE_STANDART_VIP_PLUS','Basismitglied upgraded auf PremiumPlus und ich bin Premiummitglied','float',111),
            ('BONUS_IAM_VIP_PLUS_UPGRADE_STANDART_VIP_PLUS','Basismitglied upgraded auf PremiumPlus und ich bin PremiumPlusmitglied','float',112),
            ('BONUS_IAM_STANDART_UPGRADE_VIP_VIP_PLUS','Premiummitglied upgraded auf PremiumPlus und ich bin Basismitglied','float',110),
            ('BONUS_IAM_VIP_UPGRADE_VIP_VIP_PLUS','Premiummitglied upgraded auf PremiumPlus und ich bin Premiummitglied','float',111),
            ('BONUS_IAM_VIP_PLUS_UPGRADE_VIP_VIP_PLUS','Premiummitglied upgraded auf PremiumPlus und ich bin PremiumPlusmitglied','float',112)
        ");
    }

    public function down()
    {
        echo "m171123_135248_add_new_bonus_settings cannot be reverted.\n";

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
