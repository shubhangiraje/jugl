<?php

namespace app\models;

use app\components\Thumb;
use Yii;

class ChatFile extends \app\models\base\ChatFile
{
    public function getUrl()
    {
        return $this->link;
    }

    public function getFrontFileData() {
        $data=$this->toArray(['link','name','size']);
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
        return sha1($this->id.rand().Yii::$app->params['chatFileProtectionCode']);
    }

    public static function getProtectedId($id)
    {
        if (!$id) return null;
        return $id.sha1($id.Yii::$app->params['chatFileIdProtectionCode']);
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
        return Yii::$app->params['chatFileUrl'].$this->calculateSubfolder();
    }

    public function calculateDir()
    {
        return Yii::getAlias(Yii::$app->params['chatFileAlias']).$this->calculateSubfolder($this->id);
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
}
