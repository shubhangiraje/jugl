<?php

namespace app\components;

use yii\helpers\VarDumper;
use yii\base\Exception;

class ActiveRecord extends \yii\db\ActiveRecord
{
/*
    protected $dateTimeFields=[];
*/
    protected static function cachedAttributeLabels($classes=[]) {
        static $labels;

        if (empty($classes)) $classes=[self::className()];

        $result=[];
        foreach($classes as $class) {
            if (!isset($attributes[$class])) {
                $model=new $class;

                // get attributes labels
                $labels[$class]=$model->attributeLabels();

                // get labels for other attributes
                foreach($model->attributes as $attribute=>$value) {
                    if (!isset($labels[$class][$attribute])) {
                        $labels[$class][$attribute] = $model->getAttributeLabel($attribute);
                    }
                }
            }
            $result=array_merge($result,$labels[$class]);
        }

        return $result;
    }

    public static function getCachedAttributeLabel($attribute) {
        $labels=self::cachedAttributeLabels();
        return $labels[$attribute];
    }

    public static function getEncodedAttributeLabel($attribute) {
        return \yii\helpers\Html::encode(static::getCachedAttributeLabel($attribute));
    }

    public function save($runValidation = true, $attributes = null)
    {
        if (!parent::save($runValidation, $attributes))
            throw new \Exception(VarDumper::dumpAsString($this->getErrors()));

        return true;
    }

    protected function deleteRules(&$rules, $rulesToDelete)
    {
        foreach ($rules as $k => $v) {
            foreach ($rulesToDelete as $kk=>$vv) {
                if ($v[0] == $vv[0] && $v[1]==$vv[1]) {
                    unset($rules[$k]);
                    unset($rulesToDelete[$kk]);
                    break;
                }
            }
        }

        if (!empty($rulesToDelete)) {
            throw new Exception("Can't delete rules ".Json::encode($rulesToDelete));
        }
    }

    protected function addRules(&$rules, $rulesToAdd)
    {
        foreach ($rulesToAdd as $v) {
            $rules[]=$v;
        }
    }

    /*
        private function convertDateTimeFieldsToEString()
        {
            foreach($this->dateTimeFields as $name=>$type) {
                if (is_object($this->$name) && get_class($this->$name)=='app\components\EDateTime') {
                    $this->$name=new EString($this->$name->sql());
                }
            }
        }

        private function convertDateTimeFieldsToEDateTime()
        {
            foreach($this->dateTimeFields as $name=>$type) {
                if (is_string($this->$name) || get_class($this->$name)=='app\components\EString') {
                    $this->setAttribute($name, strval($this->$name));
                }
            }
        }

        public function setAttribute($name, $value)
        {
            if (is_string($value) && isset($this->dateTimeFields[$name])) {
                $value=$this->convertStringToEDateTime($value, $this->dateTimeFields[$name]);
            }
            return parent::setAttribute($name, $value);
        }

        public function setAttributes($values, $safeOnly = true) {
            parent::setAttributes($values, $safeOnly);
            $this->convertDateTimeFieldsToEDateTime();
        }

        private function convertStringToEDateTime($val, $type)
        {
            if (!$val) {
                return null;
            }
            try {
                $val=new EDateTime($val, null, $type);
            } catch (Exception $e) {
            }
            return $val;
        }

        public function afterFind()
        {
            $this->convertDateTimeFieldsToEDateTime();
        }

        public function beforeSave($insert)
        {
            $this->convertDateTimeFieldsToEString();

            return parent::beforeSave($insert);
        }

        public function afterSave($insert, $changedAttributes)
        {
            $this->convertDateTimeFieldsToEDateTime();
        }
    */


    public function relinkByIds($ids,$class,$relation) {
        if (!$ids) $ids=[];

        // collect current items
        $idsToDelete=[];
        foreach($this->$relation as $item) $idsToDelete[$item['id']]=true;

        // add new items
        foreach($ids as $id) {
            if ($idsToDelete[$id]) {
                unset($idsToDelete[$id]);
            } else {
                $item=$class::findOne(['id'=>$id]);
                $this->link($relation,$item);
            }
        }

        // remove old items
        foreach($idsToDelete as $id=>$v) {
            $item=$class::findOne(['id'=>$id]);
            $this->unlink($relation,$item,true);
        }
    }

    public function relinkFilesWithSortOrder($files,$relation,$linkRelation) {
        $this->relinkFiles($files,$relation);

        // get updated link relation models
        $models=static::findOne(['id'=>$this->id])->$linkRelation;

        // fix sort_order if needed
        $sort_order=0;
        foreach($models as $model) {
            if ($model->sort_order!==$sort_order) {
                $model->sort_order=$sort_order;
                $model->save();
            }

            $sort_order++;
        }
    }

    public function relinkFiles($files,$relation) {
        $ids=[];
        foreach($files as $file) {
            $fileId=\app\models\File::getIdFromProtected($file['id']);
            if ($fileId) {
                $ids[]=$fileId;
            }
        }
        
        $this->relinkByIds($ids,'\app\models\File',$relation);
    }
    /*

        public function getFrontFilesRelationData($relation) {
            $data=[];

            foreach($this->$relation as $file) {
                $data[]=$file->getFrontFileData();
            }

            return $data;
        }
*/
        public function getFrontImagesRelationData($relation,$thumbs) {
            $data=[];

            foreach($this->$relation as $file) {
                $data[]=$file->getFrontImageData($thumbs);
            }

            return $data;
        }
    /*
        public function getFrontRelationIdList($relation) {
            $data=[];

            foreach($this->$relation as $item) {
                $data[]=$item->id;
            }

            return $data;
        }

        public function toArray(array $fields = [], array $expand = [], $recursive = true) {
            $data=parent::toArray($fields,$expand,$recursive);

            foreach($data as $k=>$v) {
                if (isset($this->dateTimeFields[$k])) {
                    $data[$k]=strval($this->$k);
                }
            }

            return $data;
        }
        */

    public function lockForUpdate() {
        $sql=static::find()->where($this->getPrimaryKey(true))->createCommand()->getRawSql();
        $sql.=' for update';

        \Yii::$app->db->createCommand($sql)->execute();
        $this->refresh();
    }
}
