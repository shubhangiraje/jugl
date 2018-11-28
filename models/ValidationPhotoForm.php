<?php

namespace app\models;

use Yii;

class ValidationPhotoForm extends \app\components\Model {
    public $validation_type;
    public $validation_photo1_file_id;
    public $validation_photo2_file_id;
    public $validation_photo3_file_id;

    public function attributeLabels() {
    }

    public function rules() {
        return [
            [['validation_type'],'required','message'=>Yii::t('app','Please select validation type')],
            [['validation_photo1_file_id'],'required','message'=>Yii::t('app','Bitte lade die Vorderseite Deines Ausweises hoch.')],
            [['validation_photo2_file_id'],'required','message'=>Yii::t('app','Bitte lade die Rückseite Deines Ausweises hoch.')],
            [['validation_photo3_file_id'],'required','message'=>Yii::t('app','Bitte lade ein Bild von Dir mit einem Ausweis (Vorderseite) und einem Zettel in der Hand, auf dem "jugl.net" und Dein Name steht')],
            [['validation_photo1_file_id','validation_photo2_file_id','validation_photo3_file_id'],'app\components\FileIdValidator']
        ];
    }
}