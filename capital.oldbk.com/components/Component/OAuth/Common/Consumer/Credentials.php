<?php
namespace components\Component\OAuth\Common\Consumer;

use OAuth\Common\Consumer\Credentials as BaseCredentials;
use OAuth\Common\Consumer\CredentialsInterface as BaseCredentialsInterface;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.10.2015
 */
class Credentials extends BaseCredentials implements CredentialsInterface, BaseCredentialsInterface
{
    protected $consumerPublicKey;

    /**
     * @param string $consumerId
     * @param string $consumerPublicKey
     * @param string $consumerSecret
     * @param string $callbackUrl
     */
    public function __construct($consumerId, $consumerPublicKey, $consumerSecret, $callbackUrl)
    {
        $this->consumerPublicKey = $consumerPublicKey;

        parent::__construct($consumerId, $consumerSecret, $callbackUrl);
    }

    /**
     * @return string
     */
    public function getConsumerPublicKey()
    {
        return $this->consumerPublicKey;
    }

    /**
     * @param string $consumerPublicKey
     *
     * @return $this
     */
    public function setConsumerPublicKey($consumerPublicKey)
    {
        $this->consumerPublicKey = $consumerPublicKey;
        return $this;
    }
}