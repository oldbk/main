<?php
namespace components\models;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.10.2015
 */
class OAuthUser
{
    const GENDER_FEMALE = 0;
    const GENDER_MALE   = 1;

    const SN_VKONTAKTE      = 1;
    const SN_FACEBOOK       = 2;
    const SN_ODNOKLASSNIKI  = 3;
    const SN_MAILRU         = 4;
    const SN_TWITTER        = 5;

    /** @var string */
    protected $email;

    /** @var string */
    protected $login;

    /** @var \DateTime */
    protected $birthday;

    /** @var int */
    protected $gender;

    /** @var string */
    protected $sn_id;

    /** @var int */
    protected $sn_type;

    /** @var int */
    protected $user_id;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     *
     * @return $this
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getSnId()
    {
        return $this->sn_id;
    }

    /**
     * @param string $sn_id
     *
     * @return $this
     */
    public function setSnId($sn_id)
    {
        $this->sn_id = $sn_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSnType()
    {
        return $this->sn_type;
    }

    /**
     * @param int $sn_type
     *
     * @return $this
     */
    public function setSnType($sn_type)
    {
        $this->sn_type = $sn_type;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function __toArray()
    {
        $attributes = array(
            'email'     => $this->getEmail(),
            'login'     => $this->getLogin(),
            'gender'    => $this->getGender(),
            'sn_id'     => $this->getSnId(),
            'sn_type'   => $this->getSnType(),
            'user_id'   => $this->getUserId(),
        );
        $attributes['birthday'] = $this->getBirthday() !== null ? $this->getBirthday()->getTimestamp() : null;

        return $attributes;
    }

    public static function getOAuthList()
    {
        return array(
            self::SN_FACEBOOK       => array(
                'img'       => 'http://oldbk.com/images/sn/facebook-32x32.png',
                'action'    => 'facebook'
            ),
            self::SN_VKONTAKTE      => array(
                'img'       => 'http://oldbk.com/images/sn/vkontakte-32%D1%8532.png',
                'action'    => 'vkontakte'
            ),
            self::SN_MAILRU         => array(
                'img'       => 'http://oldbk.com/images/sn/mailru-32x32.png',
                'action'    => 'mailru'
            ),
            self::SN_ODNOKLASSNIKI  => array(
                'img'       => 'http://oldbk.com/images/sn/odnoklassniki-32x32.png',
                'action'    => 'ok'
            ),
        );
    }
}