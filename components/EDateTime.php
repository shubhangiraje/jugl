<?php

namespace app\components;

use DateTime;
use yii\base\Arrayable;

class EDateTime extends DateTime
{
    public static $formatDateTime = 'd.m.Y H:i';
    public static $formatDate = 'd.m.Y';
    public static $formatSqlDateTime = 'Y-m-d H:i:s';
    public static $formatSqlDate = 'Y-m-d';
    public static $formatJsDateTime = 'Y-m-d\TH:i:s\Z';
    public static $formatJsDate = 'Y-m-d';

    public $type;

    public function __construct($time = "now", $timezone = null, $type = 'datetime')
    {
        parent::__construct($time, $timezone);
        $this->type = $type;
    }

    public function __toString()
    {
        return parent::format($this->type == 'date' ? self::$formatDate : self::$formatDateTime);
    }

    public function modifiedCopy($modify)
    {
        $datetime=clone $this;
        return $datetime->modify($modify);
    }

    public function sqlDateTime()
    {
        return $this->format(self::$formatSqlDateTime);
    }

    public function sqlDate()
    {
        return $this->format(self::$formatSqlDate);
    }

    public function sql()
    {
        return $this->format($this->type=="date" ? self::$formatSqlDate:self::$formatSqlDateTime);
    }

    public function jsDate() {
        return $this->format(static::$formatJsDate);
    }

    public function js() {
        static $utcTimeZone;

        if (!$utcTimeZone) {
            $utcTimeZone=new \DateTimeZone('UTC');
        }

        $dt=clone $this;
        return $dt->setTimeZone($utcTimeZone)->format(static::$formatJsDateTime);
    }

    public function fields() {
        return [];
    }

    public function extraFields() {

    }
}