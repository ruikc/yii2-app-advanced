<?php

namespace api\controllers\actions;

use Yii;
use yii\data\ActiveDataFilter;

/**
 * Created by PhpStorm.
 * User: zhiyuan
 * Date: 17/11/20
 * Time: 下午3:32
 */
class IndexAction extends \yii\rest\IndexAction
{
    /**
     * 重写渲染数据，按id倒序摆列
     * @name: prepareDataProvider
     * @return \yii\data\ActiveDataProvider
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 17/11/20 下午4:07
     */
    protected function prepareDataProvider() {
        $filter = new ActiveDataFilter([
            'searchModel' => $this->modelClass
        ]);
        $filterCondition = null;

        if ($filter->load(\Yii::$app->request->get())) {
            $filterCondition = $filter->build();
            if ($filterCondition === false) {
                return $filter;
            }
        }
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        if (!isset($requestParams['sort'])) {
            $requestParams['sort'] = '-id';
        }
        Yii::$app->getRequest()->setBodyParams($requestParams);

        $result = parent::prepareDataProvider();
        if ($filterCondition !== null) {
            $result->query->andWhere($filterCondition);
        }
        $result->query->andWhere(['status' => 10]);
        return $result;
    }
}