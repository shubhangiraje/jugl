<?php

use yii\db\Migration;

class m171129_084231_user_referral_modifications extends Migration
{
    public function up()
    {

	/*
        $this->execute("
          delete from user_referral
        ");

        $this->execute("
          ALTER TABLE `user_referral` DROP `id`;
        ");

        $this->execute("
            ALTER TABLE `user_referral`
            ADD PRIMARY KEY `user_id_referral_user_id` (`user_id`, `referral_user_id`),
            DROP INDEX `user_id`;
        ");

        $this->execute("
            insert into user_referral (user_id,referral_user_id,level) 
            (select u.parent_id,u.id,1 from user u join user u2 on (u2.id=u.parent_id) where u.parent_id is not null)
        ");
	*/
        $modifiedRows=0;
        $round=1;
        while (true) {
            echo "execute user_referral round ".$round++."\n";
            $newModifiedRows=Yii::$app->db->createCommand("
                insert into user_referral (user_id,referral_user_id,level)
                select tmp.user_id,tmp.referral_user_id,tmp.level
                from (
                    select ur2.user_id,ur.referral_user_id,max(ur.level+1) as level
                    from user_referral ur
                    join user_referral ur2 on (ur2.referral_user_id=ur.user_id)
                    group by ur2.user_id,ur.referral_user_id
                ) as tmp
                left outer join user_referral ur3 on (ur3.user_id=tmp.user_id and ur3.referral_user_id=tmp.referral_user_id and ur3.level=tmp.level)
                where ur3.user_id is null
                on duplicate key update level=values(level);
            ")->execute();

            if ($newModifiedRows==0) {
                break;
            }
            echo "affected $newModifiedRows rows\n";
            $modifiedRows=$newModifiedRows;
        }
    }

    public function down()
    {
        echo "m171129_084231_user_referral_modifications cannot be reverted.\n";

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
