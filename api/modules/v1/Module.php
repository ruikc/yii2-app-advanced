<?php

namespace api\modules\v1;

use yii\web\Response;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        //设置模块返回
        \Yii::$app->setComponents([
            'response' => [
                'class' => 'yii\web\Response',
                'format' => Response::FORMAT_JSON,
                'charset' => 'UTF-8',
                'on beforeSend' => function ($event) {
                    $response = $event->sender;
                    if (!isset($response->data['data'])) {
                        $data['data'] = $response->data;
                    } else {
                        $data = $response->data;
                    }
                    $response->data = array_merge(['status' => $response->isSuccessful ? 0 : 2], $data);
                    $response->statusCode = 200;
                },
            ],
        ]);

        // custom initialization code goes here
    }
}
