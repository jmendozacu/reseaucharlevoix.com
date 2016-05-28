<?php
/**
 * AdminNotification helper Class
 *
 * @category Vianetz
 * @package Vianetz_Core
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 */
class Vianetz_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param      $host
     * @param      $path
     * @param      $referer
     * @param      $dataToSend
     * @param bool $proxyHost
     * @param bool $proxyPort
     *
     * @return string
     */
    public function postToHost($host, $path, $referer, $dataToSend, $proxyHost = false, $proxyPort = false)
    {
        $result = '';

        $request = '';
        foreach ($dataToSend as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        if (empty($proxyHost) || empty($proxyPort) || $proxyHost === false || $proxyPort === false) {
            $fp = fsockopen($host, 80);
            fputs($fp, "POST $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
        } else {
            $fp = fsockopen($proxyHost, $proxyPort);
            fputs($fp, "POST http://$host$path HTTP/1.1\r\n");
            fputs($fp, "Host: $proxyHost\r\n");
        }

        fputs($fp, "Referer: $referer\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($request) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $request);

        while ( !feof($fp) ) {
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        // Remove POST headers
        $response = substr($result, strpos($result, "\r\n\r\n") + 4);

        return $response;
    }
}