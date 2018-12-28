<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 21.05.2018
 * Time: 23:15
 */

namespace components\Component\Email\providers;


class Gmail extends AbstractProvider
{
	private $_credentials_path;
	private $_secret_path;
	/** @var \Google_Client */
	private $_client;
	private $_service;

	/**
	 * Gmail constructor.
	 * @param array $_config
	 * @throws \Exception
	 * @throws \Google_Exception
	 */
	public function __construct($_config)
	{
		if(!isset($_config['credentials'])) {
			throw new \Exception('Config incorrect. Credentials');
		}
		$this->_credentials_path = ROOT_DIR.'/'.ltrim($_config['credentials'], '/');

		if(!isset($_config['secret'])) {
			throw new \Exception('Config incorrect. Secret');
		}
		$this->_secret_path = ROOT_DIR.'/'.ltrim($_config['secret'], '/');

		$this->_client = $this->setupClient();
		$this->_service = new \Google_Service_Gmail($this->_client);
	}

	/**
	 * @return \Google_Client
	 * @throws \Google_Exception
	 */
	protected function setupClient()
	{
		$client = new \Google_Client();
		$client->setApplicationName('Gmail API PHP Quickstart');
		$client->setScopes(\Google_Service_Gmail::GMAIL_SEND);
		$client->setAuthConfig($this->_secret_path);
		$client->setAccessType('offline');

		$credentials = $this->getCredentials();
		if($credentials === false) {
			$credentials = $this->appAuth($client);
		}
		$client->setAccessToken($credentials);

		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$this->putCredentials($client->getAccessToken());
		}

		return $client;
	}

	/**
	 * @param \Google_Client $client
	 * @return array
	 */
	private function appAuth($client)
	{
		$authUrl = $client->createAuthUrl();
		printf("Open the following link in your browser:\n%s\n", $authUrl);
		print 'Enter verification code: ';
		$authCode = trim(fgets(STDIN));

		// Exchange authorization code for an access token.
		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

		$this->putCredentials($accessToken);

		return $accessToken;
	}

	/**
	 * @return bool|array
	 */
	private function getCredentials()
	{
		$credentialsPath = $this->_credentials_path;
		if (!file_exists($credentialsPath)) {
			return false;
		}

		try {
			$data = json_decode(file_get_contents($credentialsPath), true);
			if(!is_array($data) || empty($data)) {
				return false;
			}

			return $data;
		} catch (\Exception $ex) {

		}

		return false;
	}

	private function putCredentials($accessToken)
	{
		if (!file_exists(dirname($this->_credentials_path))) {
			mkdir(dirname($this->_credentials_path), 0700, true);
		}
		file_put_contents($this->_credentials_path, json_encode($accessToken));
	}

	public function sendMessage($to, $subject, $message)
	{
		$raw_message = sprintf("To:%s <%s>\r\n", $to, $to);
		$raw_message .= sprintf("Subject: =?utf-8?B?%s?=\r\n", base64_encode($subject));

		// Set the right MIME & Content type
		$raw_message .= "MIME-Version: 1.0\r\n";
		$raw_message .= "Content-Type: text/html; charset=utf-8\r\n";
		$raw_message .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
		$raw_message .= $message;

		$mime = rtrim(strtr(base64_encode($raw_message), '+/', '-_'), '=');

		$message = new \Google_Service_Gmail_Message();
		$message->setRaw($mime);

		$response = $this->_service->users_messages->send('me', $message);

		return $response->getId() ? true : false;
	}
}