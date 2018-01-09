<?php
namespace common\models;

use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $mobile;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户昵称已经存在.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['mobile', 'trim'],
            ['mobile', 'required'],
            ['mobile', 'string', 'length' => 11],
            ['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '手机号码已经存在.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->mobile = $this->mobile;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateAccessToken();
        
        return $user->save() ? $user : null;
    }
}
