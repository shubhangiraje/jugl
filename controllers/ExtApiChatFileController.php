<?php

namespace app\controllers;

use yii\web\UploadedFile;
use yii\db\Expression;
use app\models\ChatFile;
use Yii;


class ExtApiChatFileController extends \app\components\ExtApiController {

    private function upload($file) {
        if ($file && ! $file->getHasError()) {
            $trx=Yii::$app->db->beginTransaction();

            // android returns filename without extension
            $ext=$file->extension;
            if ($ext=='') {
                $ext='jpg';
            }

            $model=new ChatFile;
            $model->user_id=Yii::$app->user->id;
            $model->dt=new Expression('NOW()');
            $model->link='fake';
            $model->ext=$ext;
            $model->name=$file->name;
            $model->size=$file->size;

            $model->save();

            $dir = $model->calculateDir();
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return Yii::t('app', "Can't create dir %dir%", array('%dir%' => $dir));
                }
            }

            if (preg_match('%^mov$%i',$ext) || preg_match('%^VID.*\.3gp$%',$file->name)) {
                // repack to mp4 container
                $fn = $model->id . '_' . $model->getProtectionCode() . '.mp4';
                $fullFn = $dir . $fn;

                if(preg_match('%^mov$%i',$ext)) {
                    exec("ffmpeg -i {$file->tempName} -vcodec copy -acodec copy $fullFn",$output,$result);
                } else {
                    exec("ffmpeg -i {$file->tempName} -acodec libmp3lame -ac 2 -ab 96k -ar 48000 $fullFn",$output,$result);
                }

                if ($result) {
                    \Yii::error("failed converting $file->tempName to mov, result: $result, output:\n" . join("\n", $output));
                    return Yii::t('app', "Can't convert file");
                }

                $model->name=preg_replace('/\\.[^.]{3}$/','.mp4',$file->name);
                $model->ext='mp4';
                $model->size=filesize($fullFn);

            } elseif (preg_match('%^(m4a|3gp|wav)$%i',$ext)) {
                // recode to mp3
                $fn = $model->id . '_' . $model->getProtectionCode() . '.mp3';
                $fullFn = $dir . $fn;

                exec("ffmpeg -i {$file->tempName} -vn -ar 44100 -ac 1 -ab 96k -f mp3 $fullFn",$output,$result);

                if ($result) {
                    \Yii::error("failed converting $file->tempName to mp3, result: $result, output:\n" . join("\n", $output));
                    return Yii::t('app', "Can't convert file");
                }

                $model->name=preg_replace('/\\.[^.]{3}$/','.mp3',$file->name);
                $model->ext='mp3';
                $model->size=filesize($fullFn);

            } else {
                // simple copy
                $fn = $model->id . '_' . $model->getProtectionCode() . '.' . $ext;
                $fullFn = $dir . $fn;

                if (!$file->saveAs($fullFn)) {
                    return Yii::t('app', "Can't save file");
                }
            }

            $model->link = $model->calculateUrlDir() . $fn;
            $model->save();

            $trx->commit();

            return $model;
        } else {
            return Yii::t('app', "Error while file upload");
        }
    }

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');

        try {
            $model=$this->upload($file);
        } catch (Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        if (is_string($model)) {
            return ['error' => $model];
        } else {
            $data=$model->toArray(['name','size','ext','link']);
            $data['id']=ChatFile::getProtectedId($model->id);
            $data['thumbs']['chat']=$model->getThumbUrl('chat');

            return $data;
        }
    }

}
