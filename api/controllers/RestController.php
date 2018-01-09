<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\CompositeAuth;

/**
 * Site controller
 */
class RestController extends ActiveController
{
    /**
     * @var array 免登录方法数组，* 代表全部方法免登录
     */
    public $except = [];
//    public $except = ['*'];

    /**
     * 重写行为，配置接口以json形式返回数据
     * 允许跨域操作
     * @name: behaviors
     * @return array
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午10:18
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        //允许跨域操作 .
        $behaviors[] = [
            'class' => Cors::className(),
        ];

        //是否需要登录操作
        if (!in_array(Yii::$app->controller->action->id, $this->except)
            && !in_array('*', $this->except)
        ) {
            $behaviors[] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * 重写动词，主要指定某个操作允许的提交方式
     * @name: verbs
     * @return array
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午10:19
     */
    public function verbs() {
        $verbs = [
            'search' => ['GET', 'POST'],
        ];
        return array_merge(parent::verbs(), $verbs);
    }

    /**
     * 重写行动,添加Search行动支持，重写index添加默认排序
     * @name: actions
     * @return array
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午10:21
     */
    public function actions() {
        $actions = parent::actions();
        $actions['index'] = [
            'class' => 'api\controllers\actions\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];
        $actions['delete'] = [
            'class' => 'api\controllers\actions\DeleteAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'params' => Yii::$app->request->get()
        ];
        return $actions;
    }

    /**
     * 结果序列化操作
     * @var array
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data', //返回序列化的主键 ['data'=>[]]
    ];

    /**
     * 扔出错误信息
     * @name: error
     * @param $model 传递模型到这
     * @param string $message 接口调用失败
     * @return void
     * @throws
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 17/11/21 上午11:13
     */
    public function error($model, $message = '接口调用失败') {
        if (count($model->errors) > 0) {
            foreach ($model->errors as $key => $val) {
                throw new NotFoundHttpException($val[0]);
                break;
            }
        }
        throw new NotFoundHttpException($message);
    }

}

