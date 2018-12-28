<?php
namespace components\Validator;


use components\Enum\DenyLogin;
use components\Exceptions\RegistrationException;
use Illuminate\Support\Str;


/**
 * Class RegistrationValidator
 * @package components\Validator
 */
class RegistrationValidator
{

    /**
     * Extend validator with new rule
     */
    public static function registerRegistrationRules()
    {

        \Validator::extend(
            'valid_login_klan',
            function ($attribute, $value, $parameters)
            {
                return
                    (stripos($value, 'klan') === false) &&
                    (stripos($value, "mklan") === false);
            }
        );


        \Validator::extend(
            'valid_deny_login',
            function ($attribute, $value, $parameters)
            {
                return DenyLogin::isDenny($value) ? false : true;
            }
        );

        \Validator::extend(
            'underscore',
            function ($attribute, $value, $parameters)
            {
                if (
                    preg_match("/^(_| )/", $value) ||
                    preg_match("/(_| )$/", $value)
                ) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'multi_underscore',
            function ($attribute, $value, $parameters)
            {
                if (
                    preg_match("/__/", $value) ||
                    preg_match("/--/", $value) ||
                    preg_match("/  /", $value)
                ) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'same_characters',
            function ($attribute, $value, $parameters)
            {
                if (
                    preg_match("/(.)\\1\\1\\1/", $value) ||
                    preg_match("/[\d]{5,}/", $value)
                ) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'characters',
            function ($attribute, $value, $parameters)
            {
                if (
                    preg_match("~[a-zA-Z]~", $value) &&
                    preg_match("~[а-яА-ЯёЁ]~", $value)
                ) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'basic_rule',
            function ($attribute, $value, $parameters)
            {
                if (!preg_match("~^[a-zA-Zа-яА-Я0-9-][a-zA-Zа-яА-Я0-9_ -]+[a-zA-Zа-яА-Я0-9-]$~", $value)) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'custom_min',
            function ($attribute, $value, $parameters)
            {
                if (Str::length($value, 'windows-1251') < $parameters[0]) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'custom_max',
            function ($attribute, $value, $parameters)
            {
                if (Str::length($value, 'windows-1251') > $parameters[0]) {
                    return false;
                }

                return true;
            }
        );

        \Validator::extend(
            'login_rvs',
            function ($attribute, $value, $parameters)
            {
                return $value !== '/?RVS';
            }
        );

    }

    public static function validate($params)
    {
        $messages = [
            'login.unique' => "К сожалению персонаж с ником <B>{$params['login']}</B> уже зарегистрирован.",
            'login.required' => 'Введите имя персонажа!',
            'login.login_rvs' => 'Запрещено использовать такой логин',
            'login.custom_min' => 'Логин может содержать от 4 символов',
            'login.custom_max' => 'Логин может содержать до 20 символов',
            'login.valid_login_klan' => "Регистрация персонажа с ником <B>{$params['login']}</B> запрещена!",
            'login.valid_deny_login' => "Регистрация персонажа с ником <B>{$params['login']}</B> запрещена!",
            'login.underscore' => "Логин не может начинаться или заканчиваться пробелом или символом '_'.",
            'login.multi_underscore' => "В логине не должно присутствовать подряд более 1 символа '_' или '-' и более 1 пробела.",
            'login.same_characters' => "В логине не должно присутствовать подряд более 3-х других одинаковых символов или более 4-х цифр.",
            'login.characters' => "Логин может состоять только из букв русского или английского алфавита, цифр, символов '_',  '-' и пробела.",
            'login.basic_rule' => "Логин может содержать от 4 до 20 символов, и состоять только из букв русского или английского алфавита, цифр, символов '_',  '-' и пробела. <br>Логин не может начинаться или заканчиваться пробелом или символом '_'.<br>Также в логине не должно присутствовать подряд более 1 символа '_' или '-' и более 1 пробела, а также более 3-х других одинаковых символов или более 4-х цифр!",

            'email.required' => 'Введите Ваш email',
            'email.email' => 'Неверный формат почты!',

            'psw.required' => 'Введите пароль!',
            'psw.min' => 'Пароль должен быть от 6 символов!',
            'psw.max' => 'Пароль должен быть до 20 символов!',
            'psw2.same' => 'Пароли не совпадают!',

            'sex.required' => 'Укажите ваш пол!',
        ];


        static::registerRegistrationRules();

        $validator = \Validator::make(
            $params,
            [
                'login' => [
                    'bail',
                    'required',
                    'custom_min:4',
                    'custom_max:20',
                    'valid_login_klan',
                    'valid_deny_login',
                    'underscore',
                    'multi_underscore',
                    'same_characters',
                    'characters',
                    'basic_rule',
                    'unique:users,login'
                ],

                'email' => [
                    'bail',
                    'required',
                    'email',
                ],

                'sex' => [
                    'required',
                    'in:0,1',
                ],

                'psw' => [
                    'required',
                    'custom_min:6',
                    'custom_max:20',
                ],
                'psw2' => [
                    'required',
                    'custom_min:6',
                    'custom_max:20',
                    'same:psw'
                ],
            ],
            $messages
        );

        if ($validator->fails()) {
            throw new RegistrationException($validator->errors()->first());
        }

    }
}