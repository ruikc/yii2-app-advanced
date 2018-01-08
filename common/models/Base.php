<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class Base extends ActiveRecord
{

    const STATUS_DELETED = 0; //静态变量，状态为删除
    const STATUS_ACTIVE = 10; //静态变量，状态为启用
    const STATUS_UNACTIVE = 20; //静态变量，状态为禁用
    const STATUS_CANCEL = 30; //静态变量，状态为取消
    const STATUS_WAIT = 40; //静态变量，状态为等待


    //是否强制走主库，默认不走
    public static $isMaster = false;

    /**
     * 重写getDb添加强制走主库的操作
     * 如果主库不存在，会抛出异常
     * @name: getDb
     * @throws
     * @return \yii\db\Connection
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 下午1:14
     */
    public static function getDb() {
        if (self::$isMaster) {
            $db = Yii::$app->db->getMaster();
            if (!$db) {
                throw new NotSupportedException('没有找到主库信息,请先配置主库');
            }
            return $db;
        }
        return parent::getDb();
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 得到状态操作
     * @name: getStatus
     * @param $status
     * @return mixed
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 17/11/1 下午3:01
     */
    public function getStatus($status = '') {
        $result = [
            static::STATUS_DELETED => '已删除',
            static::STATUS_ACTIVE => '已上架',
            static::STATUS_UNACTIVE => '未上架',
        ];
        if ($result) {
            return $result[$status];
        } else {
            unset($result[static::STATUS_DELETED]);
            return $result;
        }
    }

    /**
     * 通过缓存查找
     * @name: cacheFind
     * @param $id
     * @param string $key
     * @param bool $clean
     * @return array|mixed|null|ActiveRecord
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/4 下午3:27
     */
    public static function cacheFind($id, $key = 'id', $clean = false) {
        if (is_array($id)) {
            $cachekey = 'cache_' . static::tableName() . '_' . $key . '_' . json_encode($id);
            $where = $id;
        } else {
            $cachekey = 'cache_' . static::tableName() . '_' . $key . '_' . $id;
            $where = [$key => $id];
        }
        $result = Yii::$app->cache->get($cachekey);
        if ($result == null || $clean) {
            $result = static::find()->andWhere($where)->asArray()->one();
            Yii::$app->cache->set($cachekey, $result);
        }
        return $result;
    }

    /**
     * 清除缓存的操作
     * @name: cacheDelete
     * @param $id 主键ID
     * @param $key 主键列
     * @return void
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 17/11/23 下午1:07
     */
    public static function cacheDelete($id, $key = 'id') {
        if (is_array($id)) {
            $cachekey = 'cache_' . static::tableName() . '_' . $key . '_' . json_encode($id);
        } else {
            $cachekey = 'cache_' . static::tableName() . '_' . $key . '_' . $id;
        }
        Yii::$app->cache->delete($cachekey);
    }

    /**
     * 返回表所有记录
     * @name: lists
     * @param array $where
     * @param string $orderby
     * @param bool $isArray
     * @return array|ActiveRecord[]
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午9:15
     */
    public static function lists($where = [], $select = '', $isArray = false, $isMaster = false, $orderBy = 'created_at DESC') {

        self::$isMaster = $isMaster;
        $model = new static();
        if (!$select) {
            $select = $model->getOldAttributes();
        }
        return $model::find()->andWhere($where)->select($select)->orderBy($orderBy)
            ->asArray($isArray)
            ->all();
    }

    /**
     * 返回表单条记录
     * @name: info
     * @param array $where
     * @param string $orderby
     * @param bool $isArray
     * @return array|null|ActiveRecord
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午9:15
     */
    public static function info($where = [], $select = '', $isArray = false, $isMaster = false, $orderBy = 'created_at DESC') {
        self::$isMaster = $isMaster;
        $model = new static();
        if (!$select) {
            $select = $model->getOldAttributes();
        }
        return $model::find()->andWhere($where)->select($select)->orderBy($orderBy)
            ->asArray($isArray)
            ->one();
    }

    /**
     * 分页返回表记录
     * @name: listPage
     * @param array $where
     * @param int $pageSize
     * @param string $orderBy
     * @return ActiveDataProvider
     * @author: rickeryu <lhyfe1987@163.com>
     * @time: 2017/12/7 上午9:30
     */
    public static function listPage($where = [], $select = '', $isMaster = false, $orderBy = 'created_at DESC', $pageSize = 20) {
        $model = new static();
        self::$isMaster = $isMaster;
        if (!$select) {
            $select = $model->getOldAttributes();
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $model::find()->andWhere($where)->select($select)->orderBy($orderBy),
            'pagination' => ['pageSize' => $pageSize],
        ]);
        return $dataProvider;
    }


}
