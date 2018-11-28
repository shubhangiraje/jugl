<?php

namespace app\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\File;
use yii\widgets\InputWidget;


class ImageIdWidget extends InputWidget
{
    public function run() {
        $view=$this->getView();
        \app\components\FileApiAsset::register($view);

        $id=Html::getInputId($this->model,$this->attribute);
        $preview_id=$id.'_preview';
        $upload_id=$id.'_upload';
        $code = '<div class="upload-image-box">';
        //echo $this->attribute;
        //die;
        $code.=Html::activeHiddenInput($this->model,$this->attribute, ['class'=>'upload-file-input']);

        $code.='<div id="'.$preview_id.'" class="upload-file-preview">';
        $attrName=preg_replace('%\[.*?\]%','',$this->attribute);
        $file=File::findOne($this->model[$attrName  ]);

        if ($file) {
            $code .= Yii::$app->controller->renderPartial("/admin-file/image", ["file" => $file]);
        }
        $code.='</div>';

        $code.='
        <div id="'.$upload_id.'">
            <div class="b-upload b-upload_multi">
                    <div style="display:none;" class="uploader-progress" data-fileapi="active.show">
                        <div class="progress progress-striped">
                            <div class="uploader-progress-bar progress-bar progress-bar-info" data-fileapi="progress"
                                 role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
        
                <div class="btn-upload btn-upload-success btn-upload-small js-fileapi-wrapper btn btn-primary" data-fileapi="active.hide">
                    <span>'.Yii::t('app','Browse').'</span>
                    <input type="file" name="filedata">
                </div>
            </div>
        </div>
        ';
        $code.='</div>';

        $view->registerJs("
            $('form').on('beforeSubmit',function(event) {
                if ($(event.target).find('.uploader-progress:visible').size()>0) {
                    alert('".Yii::t('app','Upload in progess. Please wait and try again')."');
                    return false;
                }
            });
        ",View::POS_READY,'ImageIdWidgetFromBeforeSubmit');

        $view->registerJs("
            $('#$upload_id').fileapi({
                url: '".Url::to(['admin-file/upload'])."',
                data: {
                    _csrf: $('meta[name=csrf-token]').attr('content'),
                    tpl: 'image'
                },
                multiple: false,
                autoUpload: true,
                accept: 'image/*',
                elements: {
                    ctrl: { upload: '.js-upload' },
                    empty: { show: '.b-upload__hint' },
                    emptyQueue: { hide: '.js-upload' },
                    list: '.js-files',
                    file: {
                        tpl: '.js-file-tpl',
                        preview: {
                            el: '.b-thumb__preview',
                            width: 200,
                            height: 200
                        },
                        upload: { show: '.progress', hide: '.b-thumb__rotate' },
                        complete: { hide: '.progress' },
                        progress: '.progress .bar'
                    }
                },
                onFileComplete: function(evt,uiEvt) {
                    $('div[data-id='+FileAPI.uid(uiEvt.file)+']').hide();
                    if (!uiEvt.result.tpl) return;
        
                    // add received row to end of table
                    $('#$preview_id').html(uiEvt.result.tpl);
                    $('#$id').val(uiEvt.result.id);
                }
             });
        ");

        $view->registerJs("
            $('.upload-image-box').on('click', '.btn-delete-file', function() {
                $(this).closest('.upload-image-box').find('.upload-file-input').val('');
                $(this).parent().html('');
            });
        ");

        return $code;
    }
}