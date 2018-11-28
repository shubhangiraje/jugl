<?php

use yii\db\Migration;

class m171211_123920_add_new_field_for_table_info extends Migration
{
    public function up()
    {
        $this->execute("INSERT INTO `info` (`view`, `title_de`, `title_en`, `title_ru`, `description_de`, `description_en`, `description_ru`)
            VALUES ('view-manage-network', 'Netzwerk verwalten', 'Netzwerk verwalten', 'Netzwerk verwalten', '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>', '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>', '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>');");
    }

    public function down()
    {
        echo "m171211_123920_add_new_field_for_table_info cannot be reverted.\n";

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
