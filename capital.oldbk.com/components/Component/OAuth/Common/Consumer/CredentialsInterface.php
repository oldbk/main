<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.10.2015
 */

namespace components\Component\OAuth\Common\Consumer;


interface CredentialsInterface
{
    /**
     * @return string
     */
    public function getConsumerPublicKey();
}