<?php

use yii\db\Migration;

/**
 * Class m180109_022843_createTableAdmin
 */
class m180109_022843_createTableAdmin extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin}}', [
            'id' => $this->primaryKey()->comment('用户ID'),
            'username' => $this->string(20)->notNull()->unique()->defaultValue('')->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('授权码'),
            'password_hash' => $this->string()->notNull()->defaultValue('')->comment('登录密码'),
            'password_reset_token' => $this->string()->unique()->comment('找回密码'),
            'mobile' => $this->string(11)->notNull()->unique()->defaultValue('')->comment('手机号码'),
            'access_token' => $this->string()->notNull()->unique()->defaultValue('')->comment('登录授权'),
            'expired_at' => $this->integer()->notNull()->unique()->defaultValue(0)->comment('过期时间'),

            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('用户状态'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('更新时间'),
        ], $tableOptions);

        $this->addCommentOnTable('{{%admin}}','管理员信息表');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%admin}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180109_022843_createTableAdmin cannot be reverted.\n";

        return false;
    }
    */
}
