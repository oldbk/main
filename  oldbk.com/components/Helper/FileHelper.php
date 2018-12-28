<?php
namespace components\Helper;


/**
 * Class FileHelper
 * @package components\Helper
 */
class FileHelper
{
    /**
     * @param $content
     * @param string $file_name
     * @param string $ext
     */
    public static function write($content, $file_name = 'log', $ext = '')
    {
        try {
            $filePath = static::logDir() . DIRECTORY_SEPARATOR . $file_name . ($ext ? '.' . $ext : '');

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
     * @param $content
     * @param string $file_name
     * @param string $ext
     */
    public static function write2($content, $file_name = 'log', $ext = '')
    {
        try {
            $filePath = static::logDir() . DIRECTORY_SEPARATOR . $file_name . ($ext ? '.' . $ext : '');

            $h = fopen($filePath, "a");
            fwrite($h, $content);
            fclose($h);
        } catch (\Exception $ex) {

        }
    }

    /**
     * @param string $file_name
     * @param string $ext
     * @return bool|null|string
     */
	public static function open2($file_name = 'log', $ext = '')
	{
		try {
            $filePath = static::logDir() . DIRECTORY_SEPARATOR . $file_name . ($ext ? '.' . $ext : '');

			return file_get_contents($filePath);
		} catch (\Exception $ex) {

		}

		return null;
	}

    /**
     * @param string $file_name
     * @param string $ext
     * @return bool
     */
	public static function exists($file_name = 'log', $ext = '')
	{
		try {
            $filePath = static::logDir() . DIRECTORY_SEPARATOR . $file_name . ($ext ? '.' . $ext : '');

			return file_exists($filePath);
		} catch (\Exception $ex) {

		}

		return false;
	}

    /**
     * @param $content
     * @param string $file_name
     * @param string $ext
     */
    public static function writeArray($content, $file_name = 'log', $ext = '')
    {
        try {
            $filePath = static::logDir() . DIRECTORY_SEPARATOR . $file_name . ($ext ? '.' . $ext : '');

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
     */
    public static function writeException(\Exception $ex, $file_name = 'log', $ext = '')
    {
        if($ex->getCode() > 0) {
            self::writeArray(array(
                'message'   => $ex->getMessage(),
                'line'      => $ex->getLine(),
                'code'      => $ex->getCode(),
                'trace'     => $ex->getTraceAsString()
            ), $file_name, $ext);
        }
    }

    /**
     * @return bool|string
     */
    public static function logDir()
    {
        return (defined('LOG_DIR')
            ? LOG_DIR
            : implode(DIRECTORY_SEPARATOR, ['', 'www', 'data', 'logs']));
    }
}