<?php
namespace app\components;

use kartik\mpdf\Pdf;


class DocumentGenerator
{
    public static function download($type,$data,$tempFile)
    {
        switch ($type) {
            case 'invoice':
                static::invoice($data,$tempFile);
                break;
        }
    }

    public static function invoice($data,$tempFile) {
        \app\components\MPdf::download('invoice',$data,$tempFile);
    }


}