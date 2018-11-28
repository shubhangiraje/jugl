<?php

use yii\db\Migration;

class m180117_131916_add_become_member_settings extends Migration
{
    public function up()
    {
        $this->execute("
            insert into setting(name,title,type,value) values 
            ('BONUS_BECOME_MEMBER_IAM_STANDART_REGISTER_STANDART','Midglied werden: Basismitglied als Basismitglied eingeladen','float',0),
            ('BONUS_BECOME_MEMBER_IAM_STANDART_REGISTER_VIP','Midglied werden: Basismitglied als Premiummitglied eingeladen','float',50),
            ('BONUS_BECOME_MEMBER_IAM_STANDART_REGISTER_VIP_PLUS','Midglied werden: Basismitglied als PremiumPlusmitglied eingeladen','float',200),
            ('BONUS_BECOME_MEMBER_IAM_VIP_REGISTER_STANDART','Midglied werden: Premiummitglied als Basismitglied eingeladen','float',0),
            ('BONUS_BECOME_MEMBER_IAM_VIP_REGISTER_VIP','Midglied werden: Premiummitglied als Premiummitglied eingeladen','float',100),
            ('BONUS_BECOME_MEMBER_IAM_VIP_REGISTER_VIP_PLUS','Midglied werden: Premiummitglied als PremiumPlusmitglied eingeladen','float',500),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_REGISTER_STANDART','Midglied werden: PremiumPlusmitglied als Basismitglied eingeladen','float',0),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_REGISTER_VIP','Midglied werden: PremiumPlusmitglied als Premiummitglied eingeladen','float',150),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_REGISTER_VIP_PLUS','Midglied werden: PremiumPlusmitglied als PremiumPlusmitglied eingeladen','float',2000),
            ('BONUS_BECOME_MEMBER_IAM_STANDART_UPGRADE_STANDART_VIP','Midglied werden: Basismitglied upgraded auf Premium und ich bin Basismitglied','float',50),
            ('BONUS_BECOME_MEMBER_IAM_VIP_UPGRADE_STANDART_VIP','Midglied werden: Basismitglied upgraded auf Premium und ich bin Premiummitglied','float',100),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_UPGRADE_STANDART_VIP','Midglied werden: Basismitglied upgraded auf Premium und ich bin PremiumPlusmitglied','float',150),
            ('BONUS_BECOME_MEMBER_IAM_STANDART_UPGRADE_STANDART_VIP_PLUS','Midglied werden: Basismitglied upgraded auf PremiumPlus und ich bin Basismitglied','float',200),
            ('BONUS_BECOME_MEMBER_IAM_VIP_UPGRADE_STANDART_VIP_PLUS','Midglied werden: Basismitglied upgraded auf PremiumPlus und ich bin Premiummitglied','float',500),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_UPGRADE_STANDART_VIP_PLUS','Midglied werden: Basismitglied upgraded auf PremiumPlus und ich bin PremiumPlusmitglied','float',2000),
            ('BONUS_BECOME_MEMBER_IAM_STANDART_UPGRADE_VIP_VIP_PLUS','Midglied werden: Premiummitglied upgraded auf PremiumPlus und ich bin Basismitglied','float',200),
            ('BONUS_BECOME_MEMBER_IAM_VIP_UPGRADE_VIP_VIP_PLUS','Midglied werden: Premiummitglied upgraded auf PremiumPlus und ich bin Premiummitglied','float',500),
            ('BONUS_BECOME_MEMBER_IAM_VIP_PLUS_UPGRADE_VIP_VIP_PLUS','Midglied werden: Premiummitglied upgraded auf PremiumPlus und ich bin PremiumPlusmitglied','float',2000)
        ");
    }

    public function down()
    {
        echo "m180117_131916_add_become_member_settings cannot be reverted.\n";

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
