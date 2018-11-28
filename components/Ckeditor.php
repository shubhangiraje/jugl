<?php

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;

class Ckeditor extends \dosamigos\ckeditor\CKEditor {

    public $clientOptions = [];

    public function init() {
        \yii\widgets\InputWidget::init();
        $this->initOptions();
    }

    protected function initOptions() {

        $this->getView()->registerJs('CKEDITOR.dtd.$removeEmpty.span = 0;', \yii\web\View::POS_END);
        
        $options=[
//            'language' => 'de',
            'resize_enabled'=>true,
            'allowedContent'=>true,
            'fillEmptyBlocks'=>false,
            'filebrowserImageUploadUrl'=>'/api-file/ck-upload',
            'filebrowserUploadUrl'=> '/api-file/ck-upload',
            'extraPlugins'=>'justify',
            'toolbar'=> [
                ['name'=> 'clipboard', 'items'=> [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] ],
                ['name'=> 'editing', 'items'=> [ 'Scayt' ] ],
                ['name'=> 'links', 'items'=> [ 'Link', 'Unlink', 'Anchor' ] ],
                ['name'=> 'insert', 'items'=> [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] ],
                ['name'=> 'tools', 'items'=> [ 'Maximize' ] ],
                '/',
                ['name'=> 'basicstyles', 'items'=> [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] ],
                ['name'=> 'paragraph', 'items'=> [ 'NumberedList', 'BulletedList'] ],
                ['name'=> 'justify', 'items'=> [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] ],
                ['name'=> 'document', 'items'=> [ 'Source' ] ],
                //['name'=> 'styles', 'items'=> [ /*'Styles',*/ 'Format' ] ],
                //['name'=> 'about', 'items'=> [ 'About' ] ]
            ]
        ];

        $this->clientOptions = ArrayHelper::merge($options, $this->clientOptions);

    }


}