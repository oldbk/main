<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.01.2016
 */

namespace components\Helper;


use components\Component\Config;
use components\Component\Slim\Slim;
use components\Component\VarDumper;

class FileHelper
{

	/**
	 * @param $content
	 * @param string $file_name
	 * @param string $ext
	 * @deprecated @use Monolog $app->logger
	 */
    public static function write($content, $file_name = 'log', $ext = 'txt')
    {
        try {
            $filePath = rtrim(APP_PHP_LOG, '/').'/'.$file_name.'.'.$ext;

            $date = new \DateTime();
            $text = "--------START ".$date->format('d.m.Y H:i:s')."-------\n";
            $text .= $content;
            $text .= "\n--------END ".$date->format('d.m.Y H:i:s')."-------\n\n\n";

            $h = fopen($filePath, "a");
            fwrite($h, $text);
            fclose($h);
        } catch (\Exception $ex) {

        }
    }

	/**
	 * @param $id
	 * @return string
	 */
	public static function storage($id)
	{
		$hash = md5($id);
		$chars = substr($hash, 0, 2);

		return sprintf('%s/%s', $chars, $hash);
	}

	/**
	 * @param $id
	 * @return string
	 */
	public static function hashId($id)
	{
		return md5($id);
	}

    public static function save($filename, $content)
	{
		try {
			$dir = dirname($filename);
			if(!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}

			$h = fopen($filename, "w");
			fwrite($h, $content);
			fclose($h);
		} catch (\Exception $ex) {
			return false;
		}

		return true;
	}

	/**
	 * @param $content
	 * @param string $file_name
	 * @param string $ext
	 * @deprecated @use Monolog $app->logger
	 */
    public static function write2($content, $file_name = 'log', $ext = 'txt')
    {
        try {
            $filePath = rtrim(APP_PHP_LOG, '/').'/'.$file_name.'.'.$ext;

            $h = fopen($filePath, "a");
            fwrite($h, $content);
            fclose($h);
        } catch (\Exception $ex) {

        }
    }

	/**
	 * @param $content
	 * @param string $file_name
	 * @param string $ext
	 * @deprecated @use Monolog $app->logger
	 */
    public static function writeArray($content, $file_name = 'log', $ext = 'txt')
    {
        try {
            $filePath = rtrim(APP_PHP_LOG, '/').'/'.$file_name.'.'.$ext;

            $date = new \DateTime();
            $text = "--------START ".$date->format('d.m.Y H:i:s')."-------\n";
            ob_start();
            print_r($content);
            $text .= ob_get_clean();
            $text .= "--------END ".$date->format('d.m.Y H:i:s')."-------\n\n\n";

            $h = fopen($filePath, "a");
            fwrite($h, $text);
            fclose($h);
        } catch (\Exception $ex) {

        }
    }

	/**
	 * @param \Exception $ex
	 * @param string $file_name
	 * @param string $ext
	 * @deprecated @use Monolog $app->logger
	 */
    public static function writeException(\Exception $ex, $file_name = 'log', $ext = 'txt')
    {
        if($ex->getMessage()) {
            self::writeArray(array(
                'message'   => $ex->getMessage(),
                'line'      => $ex->getLine(),
                'code'      => $ex->getCode(),
                'trace'     => $ex->getTraceAsString()
            ), $file_name, $ext);
        }
    }

	/**
	 * @param Slim $app
	 * @param array $data
	 * @param string $err_level
	 * @deprecated @use Monolog $app->logger
	 */
    public static function monolog($app, $data, $err_level)
	{
		$app->logger->addRecord($err_level, isset($data['message'])?$data['message']:null, $data);
	}

	/**
	 * @param $app
	 * @param \Exception $ex
	 * @param $err_level
	 * @deprecated @use Monolog $app->logger
	 */
	public static function monologException($app, \Exception $ex, $err_level)
	{
		try {
			$data = [
				'message'   => $ex->getMessage(),
				'line'      => $ex->getLine(),
				'code'      => $ex->getCode(),
				'trace'     => $ex->getTraceAsString()
			];
			self::monolog($app, $data, $err_level);
		} catch (\Exception $ex) {
			self::writeException($ex);
		}
	}
}