<?php

namespace app\components;

class SLogger {
	const INFO=0;
	const WARN=1;
	const ERR=2;
	const CRIT=3;
	const DISABLED=4;

	private static $dir='../runtime/';
	private static $logFile='slogger_log';
	private static $lockFile='slogger_lock';
	private static $adminEmail='pavel@kreado.com';
	private static $maxLogFileSize=104857600;
	private static $emailMaxLines=20;
	private static $emailMaxSize=102400;
	//public static $logLevel=self::INFO;
    public static $logLevel=self::DISABLED;

	private static $levelTexts=array(
		self::INFO=>'INFO',
		self::WARN=>'WARN',
		self::ERR=>'ERR ',
		self::CRIT=>'CRIT',
	);

	public static function extLog($msg,$level=SLogger::INFO) {
		self::log($msg.' (session '.session_id().' url '.$GLOBALS['URL'].')',$level);
	}

	public static function log($msg,$level=SLogger::INFO) {
	    if ($level<self::$logLevel) {
	        return;
        }

		$time=explode(' ',microtime());
		$str=date('Y-m-d H:i:s',$time[1]);
		$str.=sprintf(".%03d %05d ",intval($time[0]*1000),getmypid());
		$str.='['.self::$levelTexts[$level].']: '.$msg."\n";

		$lock=fopen(self::$dir.self::$lockFile,'w');
		if (!$lock) throw new Exception("Can't open lock file");
		if (!flock($lock,LOCK_EX)) throw new Exception("Can't do lock");

		$logFileName=self::$dir.self::$logFile;

		clearstatcache(true,$logFileName);
		if (@filesize($logFileName)>self::$maxLogFileSize) {
			@rename($logFileName,$logFileName.date('_Y-m-d_H:i:s',$time[1]).sprintf(".%03d",intval($time[0]*1000)));
		}

		$log=fopen($logFileName,'a+');
		if (!$log) throw new Exception("Can't open log file");

		fputs($log,$str);

		fclose($log);

		if ($level>=self::ERR) {
			$logContents=file($logFileName);
		}

		flock($lock,LOCK_UN);
		fclose($lock);

		if ($level>=self::ERR) {
			$logPart='';
			for($i=0;$i<self::$emailMaxLines && strlen($logPart)<self::$emailMaxSize;$i++)
				$logPart=array_pop($logContents).$logPart;

			mail(self::$adminEmail,'SLogger of '.$_SERVER['HTTP_HOST'],$logPart);
		}
	}
}
