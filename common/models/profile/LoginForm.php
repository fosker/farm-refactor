<?php
namespace common\models\profile;

use Yii;
use yii\base\Model;

use common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $login;
    public $password;
    public $device_id;
    private $_user = false;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // login and password are both required
            [['login', 'password','device_id'], 'required'],
            [['device_id'],'exist','targetClass'=>Device::className(),'targetAttribute'=>'id'],
            [['login'], 'string','max'=>100],
            // password is validated by validatePassword()
            ['password', 'check_password'],
            ['login', 'check_status'],
        ];
    }

    public function attributeLabels() {
        return [
            'login'=>'Логин',
            'password'=>'Пароль',
            'device_id'=>'Идентификатор устройства',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */

    public function check_status($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user && $user->status == User::STATUS_VERIFY) {
                $this->addError($attribute, 'Невозможно авторизоваться. Ваш аккаунт неверифицирован.');
            }
        }
    }

    public function check_password($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Невозможно авторизоваться. Неккоректные данные.');
            }
        }
    }
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 3600 * 24 * 30) && Yii::$app->user->identity->generateAccessToken($this->device_id);
        } else {
            return false;
        }
    }
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByLogin($this->login);
        }
        return $this->_user;
    }
}