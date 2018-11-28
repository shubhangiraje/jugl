<?php

namespace app\components;

use yii\web\HttpException;
use yii\base\Exception;
use Yii;

class Thumb {

    public static function getSubfolder($url)
    {
        $code=sha1($url);
        //return '/'.substr($code,0,2).'/'.substr($code,2,2);
        return '/'.substr($code,0,2);
    }

    public static function createUrl($url, $thumbType, $addHost=false) {
        if ($url=='' || substr($url,0,5)=='data:') return $url;

        $url=rawurldecode($url);

        $thumb = Yii::$app->params['thumbTypes'][$thumbType];

        $url = preg_replace('/(\?.*)$/', '', $url);

        //if (substr($url,0,4)!='http')
        //    $filetime = @filemtime(Yii::getAlias('@webroot') . $url);
        //else
        $filetime = 0;

        $prefix='';
        foreach(Yii::$app->params['thumbPrefixes'] as $k=>$v)
            if (substr($url,0,strlen($v['urlPrefix']))==$v['urlPrefix']) {
                $prefix=$k;
                $url=substr_replace($url,'',0,strlen($v['urlPrefix']));
                break;
            }
        $url=$prefix.'_'.$url;

        // fix for apache slashes
        $url=rawurlencode($url);

        preg_match('/^(.*?)\.([^.]*)$/', $url, $m);

        $ext = $thumb['outputFormat'];
        if ($ext == 'auto') $ext = preg_match('%^(png|gif)$%i', $m[2]) ? 'png' : 'jpg';

        $m[1] = rawurlencode(rawurlencode($m[1]));

        $subfolder=self::getSubfolder($url);

        $url=Yii::$app->params['thumbsUrl'] . $subfolder. '/' . $m[1] . '.' . $m[2] . '_' . $thumbType . '_' . $filetime .
            ($thumb['version'] ? "_{$thumb['version']}":'') . ".$ext";

        if ($addHost) {
            $url=Yii::$app->request->hostInfo.$url;
        }

        return $url;
    }

    private static function mirrorImage ( $imgsrc)
    {
        $width = imagesx ( $imgsrc );
        $height = imagesy ( $imgsrc );

        $src_x = $width -1;
        $src_y = 0;
        $src_width = -$width;
        $src_height = $height;

        $imgdest = imagecreatetruecolor ( $width, $height );

        if ( imagecopyresampled ( $imgdest, $imgsrc, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height ) )
        {
            return $imgdest;
        }

        return $imgsrc;
    }

    private static function adjustPicOrientation($im, $full_filename) {
        $exif = @exif_read_data($full_filename);

        if($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];

            if($orientation != 1){

                $mirror = false;
                $deg    = 0;

                switch ($orientation) {
                    case 2:
                        $mirror = true;
                        break;
                    case 3:
                        $deg = 180;
                        break;
                    case 4:
                        $deg = 180;
                        $mirror = true;
                        break;
                    case 5:
                        $deg = 270;
                        $mirror = true;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 7:
                        $deg = 90;
                        $mirror = true;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg) $im = imagerotate($im, $deg, 0);
                if ($mirror) $im = static::mirrorImage($im);
            }
        }
        return $im;
    }

    public static function loadImage($filename) {

        if (preg_match('/\.mp4$/i',$filename)) {
            try {
                $frameName = preg_replace('/\.mp4$/i', '.jpg', $filename);
                if (file_exists($frameName)) unlink($frameName);

                $ffmpeg = \FFMpeg\FFMpeg::create([
                    'ffprobe.binaries'=>'/usr/bin/ffprobe',
                    'ffmpeg.binaries'=>'/usr/bin/ffmpeg'
                ]);

                $video = $ffmpeg->open($filename);
                $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(0));

                $frame->save($frameName);
                $im = imagecreatefromjpeg($frameName);

/*
                $ffprobe = \FFMpeg\FFProbe::create([
                    'ffprobe.binaries'=>'/usr/bin/ffprobe',
                ]);
                $tags=$ffprobe
                        ->streams($filename) // extracts streams informations
                        ->videos()                      // filters video streams
                        ->first()                       // returns the first video stream
                        ->get('tags');            // returns the codec_name property

                if (is_array($tags)) {
                    $im=imagerotate($im,-$tags['rotate'],0);
                }
*/
                if (file_exists($frameName)) unlink($frameName);
            } catch (Exception $e) {
                if (file_exists($frameName)) unlink($frameName);
                return false;
            }
            return $im;
        }

        if (!file_exists($filename)) {
            return false;
        }

        $data=getimagesize($filename);

        switch ($data[2]) {
            case IMAGETYPE_JPEG:
                $im = @imagecreatefromjpeg($filename);
                $im = static::adjustPicOrientation($im,$filename);
                break;
            case IMAGETYPE_GIF:
                $im = @imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $im = @imagecreatefrompng($filename);
                break;
        }
        return $im;
    }

    public static function saveImage($im,$filename,$thumb) {
        $outputFormat = $thumb['outputFormat'];

        //if ($outputFormat == 'auto')
        //    $outputFormat = preg_match('%^(png|gif)$%i', $type) ? 'png' : 'jpg';

        switch ($outputFormat) {
            case 'jpg':
                return imagejpeg($im, $filename, $thumb['qualityJPG']);
            case 'png':
                return imagepng($im, $filename, $thumb['qualityPNG']);
        }

        return false;
    }

    private static function blur($img, $radius,$sigma)
    {
        ob_start();
        imagepng($img);
        $blob = ob_get_clean();
        $image = new \Imagick();
        $image->readImageBlob($blob);
        $image->blurImage($radius,$sigma);
        return imagecreatefromstring($image->getImageBlob());
/*
        $sx = imagesx($img);
        $sy = imagesy($img);

        $wrow=$sx<<2;

        $radius=10;
        $pixels=array_fill(0,$wrow*$sy,0);
        for ($y = 0; $y < $sy; $y++)
            for ($x = 0; $x < $sx; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $r=($rgb >> 16) & 0xFF;
                $g=($rgb >> 8) & 0xFF;
                $b=$rgb & 0xFF;

                for($dy=-$radius;$dy<=$radius;$dy++)
                    for($dx=-$radius;$dx<=$radius;$dx++) {
                        $idx=($y+$dy)*$wrow+(($x+$dx)<<2);
                        $pixels[$idx]+=$r;
                        $pixels[$idx+1]+=$g;
                        $pixels[$idx+2]+=$b;
                        $pixels[$idx+3]++;
                    }
            }

        $idx=0;
        for ($y = 0; $y < $sy; $y++)
            for ($x = 0; $x < $sx; $x++) {
                imagesetpixel($img,$x,$y,imagecolorallocate($img,$pixels[$idx]/$pixels[$idx+3],$pixels[$idx+1]/$pixels[$idx+3],$pixels[$idx+2]/$pixels[$idx+3]));
                $idx+=4;
            }
*/
    }

    public static function resizeImage($sim,$thumb) {
        $sourceWidth = imagesx($sim);
        $sourceHeight = imagesy($sim);

        switch ($thumb['resizeMode']) {
            case 'originalSize':
                $im=$sim;
                break;
            case 'max':
                if ($sourceWidth / $sourceHeight < $thumb['width'] / $thumb['height']) {
                    $targetHeight = $thumb['height'];
                    $targetWidth = floor($sourceWidth / $sourceHeight * $thumb['height']);
                } else {
                    $targetWidth = $thumb['width'];
                    $targetHeight = floor($sourceHeight / $sourceWidth * $thumb['width']);
                }

                if ($targetHeight >= $sourceHeight && $targetWidth >= $sourceWidth) {
                    $targetHeight = $sourceHeight;
                    $targetWidth = $sourceWidth;
                }

                $targetOffsetX = $targetOffsetY = 0;
                $sourceOffsetX = $sourceOffsetY = 0;
                $targetRegHeight = $targetHeight;
                $targetRegWidth = $targetWidth;
                $sourceRegWidth = $sourceWidth;
                $sourceRegHeight = $sourceHeight;
                break;
            case 'resizeAndFill':
                if ($sourceWidth / $sourceHeight < $thumb['width'] / $thumb['height']) {
                    $targetHeight = $thumb['height'];
                    $targetWidth = floor($sourceWidth / $sourceHeight * $thumb['height']);
                } else {
                    $targetWidth = $thumb['width'];
                    $targetHeight = floor($sourceHeight / $sourceWidth * $thumb['width']);
                }

                $targetOffsetX = ($thumb['width']-$targetWidth)/2;
                $targetOffsetY = ($thumb['height']-$targetHeight)/2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $targetRegHeight = $targetHeight;
                $targetRegWidth = $targetWidth;
                $sourceRegWidth = $sourceWidth;
                $sourceRegHeight = $sourceHeight;
                $targetWidth=$thumb['width'];
                $targetHeight=$thumb['height'];
                break;
            case 'resizeAndCrop':
                $scale = $sourceHeight / $thumb['height'] < $sourceWidth / $thumb['width'] ?
                    $sourceHeight / $thumb['height'] : $sourceWidth / $thumb['width'];

                $targetWidth = $thumb['width'];
                $targetHeight = $thumb['height'];
                $targetRegWidth = $sourceWidth / $scale;
                $targetRegHeight = $sourceHeight / $scale;
                $targetOffsetX = ($targetWidth - $targetRegWidth) / 2;
                $targetOffsetY = ($targetHeight - $targetRegHeight) / 2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $sourceRegHeight = $sourceHeight;
                $sourceRegWidth = $sourceWidth;
                break;
            case 'resizeWithBlendedBG':
                // draw cropped background
                $scale = $sourceHeight / $thumb['height'] < $sourceWidth / $thumb['width'] ?
                    $sourceHeight / $thumb['height'] : $sourceWidth / $thumb['width'];

                $targetWidth = $thumb['width'];
                $targetHeight = $thumb['height'];
                $targetRegWidth = $sourceWidth / $scale;
                $targetRegHeight = $sourceHeight / $scale;
                $targetOffsetX = ($targetWidth - $targetRegWidth) / 2;
                $targetOffsetY = ($targetHeight - $targetRegHeight) / 2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $sourceRegHeight = $sourceHeight;
                $sourceRegWidth = $sourceWidth;

                $im = imagecreatetruecolor($targetWidth, $targetHeight);
                imagesavealpha($im, true);
                imagealphablending($im, false);
                imagecopyresampled($im, $sim, $targetOffsetX, $targetOffsetY, $sourceOffsetX, $sourceOffsetY, $targetRegWidth, $targetRegHeight, $sourceRegWidth, $sourceRegHeight);
                imagealphablending($im, true);

                imagefilledrectangle($im, 0, 0, $targetWidth, $targetHeight,
                    $thumb['bgColor'] ?
                        imagecolorallocatealpha($im, $thumb['bgColor']['r'], $thumb['bgColor']['g'], $thumb['bgColor']['b'], $thumb['bgColor']['a']):
                        imagecolorallocatealpha($im, 0, 0, 0, 127)
                );

                if ($sourceWidth / $sourceHeight < $thumb['width'] / $thumb['height']) {
                    $targetHeight = $thumb['height'];
                    $targetWidth = floor($sourceWidth / $sourceHeight * $thumb['height']);
                } else {
                    $targetWidth = $thumb['width'];
                    $targetHeight = floor($sourceHeight / $sourceWidth * $thumb['width']);
                }

                $targetOffsetX = ($thumb['width']-$targetWidth)/2;
                $targetOffsetY = ($thumb['height']-$targetHeight)/2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $targetRegHeight = $targetHeight;
                $targetRegWidth = $targetWidth;
                $sourceRegWidth = $sourceWidth;
                $sourceRegHeight = $sourceHeight;
                $targetWidth=$thumb['width'];
                $targetHeight=$thumb['height'];

                imagealphablending($im, false);
                imagecopyresampled($im, $sim, $targetOffsetX, $targetOffsetY, $sourceOffsetX, $sourceOffsetY, $targetRegWidth, $targetRegHeight, $sourceRegWidth, $sourceRegHeight);
                imagealphablending($im, true);

                break;
            case 'resizeAndDraw':
                if ($sourceWidth / $sourceHeight < $thumb['width'] / $thumb['height']) {
                    $targetHeight = $thumb['height'];
                    $targetWidth = floor($sourceWidth / $sourceHeight * $thumb['height']);
                } else {
                    $targetWidth = $thumb['width'];
                    $targetHeight = floor($sourceHeight / $sourceWidth * $thumb['width']);
                }

                $targetOffsetX = ($thumb['width']-$targetWidth)/2;
                $targetOffsetY = ($thumb['height']-$targetHeight)/2;
                $sourceOffsetX = $sourceOffsetY = 0;
                $targetRegHeight = $targetHeight;
                $targetRegWidth = $targetWidth;
                $sourceRegWidth = $sourceWidth;
                $sourceRegHeight = $sourceHeight;
                $targetWidth=$thumb['width'];
                $targetHeight=$thumb['height'];

                $im = imagecreatetruecolor($targetWidth, $targetHeight);
                imagesavealpha($im, true);
                imagealphablending($im, false);
                imagefilledrectangle($im, 0, 0, $targetWidth, $targetHeight,
                    $thumb['bgColor'] ?
                        imagecolorallocatealpha($im, $thumb['bgColor']['r'], $thumb['bgColor']['g'], $thumb['bgColor']['b'], $thumb['bgColor']['a']):
                        imagecolorallocatealpha($im, 0, 0, 0, 127) );

                imagecopyresampled($im, $sim, $targetOffsetX, $targetOffsetY, $sourceOffsetX, $sourceOffsetY, $targetRegWidth, $targetRegHeight, $sourceRegWidth, $sourceRegHeight);
                imagealphablending($im, true);

                if ($targetOffsetX>0) {
                    imagecopyresampled($im,$sim,0,0,0,0,$targetOffsetX+1,$targetHeight,1,$sourceHeight);
                    imagecopyresampled($im,$sim,$targetWidth-$targetOffsetX-1,0,$sourceWidth-1,0,$targetOffsetX+2,$targetHeight,1,$sourceHeight);
                }
                if ($targetOffsetY>0) {
                    imagecopyresampled($im,$sim,0,0,0,0,$targetWidth,$targetOffsetY+1,$sourceWidth,1);
                    imagecopyresampled($im,$sim,0,$targetHeight-$targetOffsetY-1,0,$sourceHeight-1,$targetWidth,$targetOffsetY+2,$sourceWidth,1);
                }

                break;
            default:
                return;
        }

        if (!isset($im)) {
            $im = imagecreatetruecolor($targetWidth, $targetHeight);
            imagesavealpha($im, true);
            imagealphablending($im, false);
            imagefilledrectangle($im, 0, 0, $targetWidth, $targetHeight,
                $thumb['bgColor'] ?
                    imagecolorallocatealpha($im, $thumb['bgColor']['r'], $thumb['bgColor']['g'], $thumb['bgColor']['b'], $thumb['bgColor']['a']):
                    imagecolorallocatealpha($im, 0, 0, 0, 127) );

            imagecopyresampled($im, $sim, $targetOffsetX, $targetOffsetY, $sourceOffsetX, $sourceOffsetY, $targetRegWidth, $targetRegHeight, $sourceRegWidth, $sourceRegHeight);
            imagealphablending($im, true);
        }

        if (is_array($thumb['filters']))
            foreach($thumb['filters'] as $filter) {
                switch($filter[0]) {
                    case IMG_FILTER_SMOOTH:
                        $count=$filter[2];
                        if ($count<1) $count=1;
                        for($i=0;$i<$count;$i++) {
                            imagefilter($im, $filter[0], $filter[1]);
                        }
                        break;
                    case IMG_FILTER_GAUSSIAN_BLUR:
                        $count=$filter[1];
                        if ($count<1) $count=1;
                        for($i=0;$i<$count;$i++) {
                            imagefilter($im, $filter[0]);
                        }
                        break;
                    case 'blur':
                        $im=static::blur($im,$filter[1],$filter[2]);
                        break;
                    default:
                        throw new CHttpException(500,"unknown filter");
                }
            }

        if ($thumb['pngWatermark']) {
            $wm=imagecreatefrompng($thumb['pngWatermark']);
            imagecopy($im,$wm,0,0,0,0,$targetWidth,$targetHeight);
        }

        return $im;
    }

    public static function process($source_filename,$dest_filename,$thumbType) {

        $thumb=Yii::$app->params['thumbTypes'][$thumbType];
        if (!$thumb) return false;

        $sim=self::loadImage($source_filename);
        if (!$sim) {
            $sim = imagecreatetruecolor(1, 1);
            imagefilledrectangle($sim, 0, 0, 1, 1,
                    imagecolorallocatealpha($sim, 0, 0, 0, 127)
            );
        }
        if (!$sim) return false;

        $im=self::resizeImage($sim,$thumb);
        if (!$im) return false;

        if (!$thumb['dontSave']) {
            if (!self::saveImage($im,$dest_filename,$thumb)) return false;

            if ($thumb['useOriginal']===true ||
                (is_callable($thumb['useOriginal']) && call_user_func_array($thumb['useOriginal'],[
                        $sim,$source_filename,
                        $im,$dest_filename
                ]))) {
                file_put_contents($dest_filename,file_get_contents($source_filename));
            }
        } else {
            ob_start();
            $res=self::saveImage($im,null,$thumb);
            $image=ob_get_clean();
            if (!$res) return false;

            if ($thumb['useOriginal']===true ||
                (is_callable($thumb['useOriginal']) && call_user_func_array($thumb['useOriginal'],[
                        $sim,$source_filename,
                        $im,$dest_filename
                    ]))) {
                $image=file_get_contents($source_filename);
            }

            return $image;
        }

        return true;
    }

    public static function generate($url) {
        if (preg_match('%^(.*)/%',$url,$m));
        $url=substr($url,strlen($m[1])+1);
        $subfolder='/'.$m[1];

        if (!preg_match($f = '%^((.*)\.(jpe?g|gif|png|mp4)_([^_]+)_(0)(_\d*)?\.(jpg|png))$%i', $url, $m))
            throw new HttpException(404);

        $thumbType=$m[4];
        $thumb = Yii::$app->params['thumbTypes'][$thumbType];
        if (!$thumb)
            throw new HttpException(404);

        $source_url=rawurldecode($m[2]) . '.' . $m[3];

        if ($subfolder!=self::getSubfolder($source_url)) throw new HttpException(404);

        // fix for apache slashes
        $source_url=rawurldecode($source_url);

        if (!preg_match('/^([^_]*)_(.*)$/',$source_url,$pref))
            throw new HttpException(404);

        if ($pref[1]!='') $source_url=Yii::$app->params['thumbPrefixes'][$pref[1]]['urlPrefix'].$pref[2];

        // if external link
        if (substr($source_url,0,4)=='http') {
            $source_folder=Yii::getAlias(Yii::$app->params['externalAlias']).self::getSubfolder($source_url).'/';
            $source_filename=$source_folder.rawurlencode($source_url);

            // if image was not downloaded before
            if (!file_exists($source_filename)) {
                // create subfolders
                if (!file_exists($source_folder))
                    if (!mkdir($source_folder,0755,true)) throw new HttpException(500);

                // download file
                $content=file_get_contents($source_url);
                file_put_contents($source_filename,$content);

                // resize file
                //self::process($source_filename,$source_filename,Yii::$app->params['thumbPrefixes'][$pref[1]]['thumbType']);
            }
        } else {
            $source_filename = Yii::getAlias('@webroot') . $source_url;
        }

        $dest_folder=Yii::getAlias(Yii::$app->params['thumbsAlias']).$subfolder;

        if (!file_exists($dest_folder))
            if (!mkdir($dest_folder,0755,true)) {
                throw new HttpException(500);
            }

        $res=self::process($source_filename,$dest_folder.'/' . $m[1],$thumbType);
        if (!$res) {
            throw new HttpException(500);
        }

        if (!$thumb['dontSave']) {
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            header("Location: " . Yii::$app->params['thumbsUrl'] . $subfolder.'/'.rawurlencode($url));
        } else {
            header('Content-type: image/jpeg');
            header('Content-length: '.strlen($res));
            echo $res;
        }
        exit;
    }

}
