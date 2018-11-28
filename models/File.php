<?php

namespace app\models;

use app\components\Thumb;
use yii\db\Expression;
use Yii;

class File extends \app\models\base\File
{
    public function getUrl()
    {
        return $this->link;
    }

    public function getFrontFileData() {
        $data=$this->toArray(['link','name','size']);
        $data['link']=$data['link'];
        $data['id']=static::getProtectedId($this->id);

        return $data;
    }

    public function getFrontImageData($thumbs) {
        $data=$this->toArray(['id']);
        $data['id']=static::getProtectedId($this->id);
        if (!empty($thumbs)) {
            foreach($thumbs as $thumb) {
                $data['thumbs'][$thumb]=$this->getThumbUrl($thumb);
            }
        }

        return $data;
    }

    public function getThumbUrl($thumbType)
    {
        $url=Thumb::createUrl($this->getUrl(), $thumbType);

        if (Yii::$app->controller instanceof \app\components\ExtApiController) {
            $url=Yii::$app->request->hostInfo.$url;
        }

        return $url;
    }

    public function getProtectionCode()
    {
        return sha1($this->id.rand().Yii::$app->params['fileProtectionCode']);
    }

    public static function getProtectedId($id)
    {
        if (!$id) return null;
        return $id.sha1($id.Yii::$app->params['fileIdProtectionCode']);
    }

    public static function getIdFromProtected($protectedId)
    {
        $id=substr($protectedId, 0, strlen($protectedId)-40);
        if (!$id || self::getProtectedId($id)!==$protectedId) {
            return null;
        }

        return $id;
    }

    public function calculateUrlDir()
    {
        return Yii::$app->params['fileUrl'].$this->calculateSubfolder();
    }

    public function calculateDir()
    {
        return Yii::getAlias(Yii::$app->params['fileAlias']).$this->calculateSubfolder($this->id);
    }

    public function calculateSubfolder()
    {
        $padded_id = $this->id;
        while (strlen($padded_id) % 2 != 0)
            $padded_id = '0' . $padded_id;

        $folder_parts = str_split($padded_id, 2);
        array_pop($folder_parts);

        $res = '/';
        foreach ($folder_parts as $fp) {
            $res.=$fp . '/';
        }

        return $res;
    }

    public static function upload($file) {
        if (!$file->getHasError()) {
            $trx=Yii::$app->db->beginTransaction();

            $model=new File;
            $model->dt=new Expression('NOW()');
            $model->link='fake';
            $model->ext=strtolower(preg_replace('%^.*\.%','',$file->name));
            $model->name=$file->name;
            $model->size=$file->size;

            $model->save();

            $dir = $model->calculateDir();
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return Yii::t('app', "Can't create dir %dir%", array('%dir%' => $dir));
                }
            }

            $fn = $model->id . '_' . $model->getProtectionCode() . '.' . $file->extension;

            $fullFn = $dir . $fn;

            /*
            if (!Thumb::process($file->tempName, $fullFn, $thumb)) {
                $this->returnError(Yii::t('app', "Can't resize and/or save file"));
            }
            */

            if (!$file->saveAs($fullFn)) {
                return Yii::t('app', "Can't save file");
            }

            $model->link = $model->calculateUrlDir() . $fn;
            $model->save();

            $trx->commit();

            return $model;
        } else {
            return Yii::t('app', "Error while file upload");
        }
    }


}
