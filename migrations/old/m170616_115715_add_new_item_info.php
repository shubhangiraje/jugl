<?php

use yii\db\Migration;

class m170616_115715_add_new_item_info extends Migration
{
    public function up()
    {
        $this->execute("INSERT INTO `info` (`id`, `view`, `title_de`, `title_en`, `title_ru`, `description_de`, `description_en`, `description_ru`)
            VALUES ('30', 'view-offers-index', 'Kaufen / verkaufen / Interessen angeben', 'Buy / sell, indicate interests', 'Купить / продать / указать интересы', '<p>Du m&ouml;chtest etwas kaufen oder verkaufen? Dann ist hier der richtige Platz.</p>', '<p>Would you like to sell or buy anything? Here is the right palce for this.</p>', '<p>Ты хочешь что-нибудь продать или купить? Здесь самое место для этого.</p>');");
    }

    public function down()
    {
        echo "m170616_115715_add_new_item_info cannot be reverted.\n";

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
