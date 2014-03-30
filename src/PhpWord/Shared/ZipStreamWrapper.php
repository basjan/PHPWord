<?php
/**
 * PHPWord
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2014 PHPWord
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt LGPL
 */

namespace PhpOffice\PhpWord\Shared;

use PhpOffice\PhpWord\Exceptions\Exception;
use PhpOffice\PhpWord\Settings;

/**
 * Zip stream wrapper
 *
 * @codeCoverageIgnore  Legacy from PHPExcel
 */
class ZipStreamWrapper
{
    /**
     * Internal ZipAcrhive
     *
     * @var \ZipAcrhive
     */
    private $_archive;

    /**
     * Filename in ZipAcrhive
     *
     * @var string
     */
    private $_fileNameInArchive = '';

    /**
     * Position in file
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Data
     *
     * @var mixed
     */
    private $_data = '';

    /**
     * Register wrapper
     */
    public static function register()
    {
        @stream_wrapper_unregister("zip");
        @stream_wrapper_register("zip", __CLASS__);
    }

    /**
     * Open stream
     *
     * @param string $path
     * @param string $mode
     * @param string $options
     * @param string $opened_path
     */
    public function streamOpen($path, $mode, $options, &$opened_path)
    {
        // Check for mode
        if ($mode{0} != 'r') {
            throw new Exception('Mode ' . $mode . ' is not supported. Only read mode is supported.');
        }

        // Parse URL
        $url = @parse_url($path);

        // Fix URL
        if (!is_array($url)) {
            $url['host'] = substr($path, strlen('zip://'));
            $url['path'] = '';
        }
        if (strpos($url['host'], '#') !== false) {
            if (!isset($url['fragment'])) {
                $url['fragment'] = substr($url['host'], strpos($url['host'], '#') + 1) . $url['path'];
                $url['host'] = substr($url['host'], 0, strpos($url['host'], '#'));
                unset($url['path']);
            }
        } else {
            $url['host'] = $url['host'] . $url['path'];
            unset($url['path']);
        }

        // Open archive
        $zipClass = Settings::getZipClass();
        $this->_archive = new $zipClass();
        $this->_archive->open($url['host']);

        $this->_fileNameInArchive = $url['fragment'];
        $this->_position = 0;
        $this->_data = $this->_archive->getFromName($this->_fileNameInArchive);

        return true;
    }

    /**
     * Stat stream
     */
    public function streamStat()
    {
        return $this->_archive->statName($this->_fileNameInArchive);
    }

    /**
     * Read stream
     *
     * @param int $count
     */
    public function streamRead($count)
    {
        $ret = substr($this->_data, $this->_position, $count);
        $this->_position += strlen($ret);
        return $ret;
    }

    /**
     * Tell stream
     */
    public function streamTell()
    {
        return $this->_position;
    }

    /**
     * EOF stream
     */
    public function streamEof()
    {
        return $this->_position >= strlen($this->_data);
    }

    /**
     * Seek stream
     *
     * @param int $offset
     * @param mixed $whence
     */
    public function streamSeek($offset, $whence)
    {
        switch ($whence) {
            case \SEEK_SET:
                if ($offset < strlen($this->_data) && $offset >= 0) {
                    $this->_position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case \SEEK_CUR:
                if ($offset >= 0) {
                    $this->_position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case \SEEK_END:
                if (strlen($this->_data) + $offset >= 0) {
                    $this->_position = strlen($this->_data) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }
}