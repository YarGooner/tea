<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 02.03.2019
 * Time: 15:18
 */
namespace admin\components\widgets;

use yii\helpers\Html;
use common\components\Dictionary;
use common\components\UserUrlManager;
use kartik\daterange\DateRangePicker;
use mihaildev\elfinder\InputFile;
use yii\helpers\Url;
use yii2mod\editable\EditableColumn;

class AdminWidgetHelper
{


    //
    static public function getFixedWidthColumn( $attr = 'id', $width = '20px'){
        return [
            'attribute' => $attr,
            'options' => ['style' => 'color:red; width:'.$width.'; white-space: no-wrap;'],
        ];
    }


    //
    static public function getLinkToItem( $attr, $path ){
        return [
            'attribute' => $attr,
            'value' => function($data) use( $attr, $path ){
//              return Url::toRoute( '/'.$path, [ 'id' => $data->id ]);
                return Html::a( $data->$attr, ['/'.$path.'/view', 'id' => $data->$attr ], [ 'style' => 'font-weight:bold;', 'target' => '_blank' ] );
            },
            'format' => 'raw',
        ];
    }


    //
    static public function getDictionaryItem($attr, $items ){
        if( is_string($items) ) {
            $filter_items = Dictionary::getList($items, true );
            $items = Dictionary::getList($items, true, true );
        }else{
            $filter_items = $items;
        }
        return [
            'attribute' => $attr,
            'value' => function($model) use( $attr, $items ) {
                return $items[$model->$attr];
            },
            'format' => 'raw',
            'filter' => $filter_items,
        ];
    }


    //
    static public function getDropdownColumn( $attr, $items, $editable = true ){
        if( is_string($items) ) $items = Dictionary::getList($items, true );
        if( $editable ) {
            return [
                'class' => EditableColumn::class,
                'attribute' => $attr,
                'url' => ['change-' . $attr],
                'type' => 'select',
                'editableOptions' => function ($model) use ($attr, $items) {
                    return [
                        'source' => $items,
                        'value' => $model->$attr,
                    ];
                },
                'value' => function ($data) use ($attr, $items) {
                    return $items[$data->$attr];
                },
                'filter' => $items,
            ];
        }else{
            return [
                'attribute' => $attr,
                'value' => function ($data) use ($attr, $items) {
                    return $items[$data->$attr];
                },
                'filter' => $items,
            ];
        }
    }


    //
    static public function getImageItem( $attr ){
        return  [
            'attribute' => $attr,
            'value' => function($data) use ( $attr ){
                return $data->$attr ? "<img src=".UserUrlManager::setAbsoluteUploadsPath($data->$attr)." style='max-width:50px;max-height:50px;'>" : '<span style="color:gray;">не задано</span>';
            },
            'format' => 'html'
        ];
    }


    //
    static public function getFileUploader( $form, $model, $attr ){
        return
        '<div class="col-xs-4">'.
            $form->field($model, $attr)->widget(InputFile::className(), [
                'language'      => 'ru',
                'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                'options'       => ['class' => 'form-control'],
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple'      => false       // возможность выбора нескольких файлов
            ])
        .'</div>';
    }


    //
    static public function getEditableItem( $attr, $width = null )
    {
        if( $width && !is_string($width) ) $width = $width.'px';
        return [
            'class' => EditableColumn::class,
            'attribute' => $attr,
            'url' => ['change-'.$attr ],
            'options' => ['style' => $width ? 'width:'.$width.'; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' : ''],
        ];
    }

    static public function getDataRangeItem( $attr, $searchModel )
    {
        return [
            'attribute' => $attr,
            'value' => $attr,
            'format' => 'datetime',
//                'value' => function($data){
//                    if($data->last_login_at > 0) {
//                        return date('Y-m-d H:i:s', +$data->last_login_at);
//                    }
//                    return '';
//                },
            'filter' => DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute' => $attr,
                    'pluginOptions'=> [
                        'timePicker'=>true,
//                            'locale'=>['format' => 'Y-MM-DD HH:mm:ss'],
//                              "opens"=>"left",
                    ],
                ]). HTML::error($searchModel,$attr) // TRICK
        ];
    }

}