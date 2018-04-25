<?php
/**
 * Created by PhpStorm.
 * User: theo
 * Date: 25/04/18
 * Time: 14:54
 */

namespace Sourcecode;


class SitemapParser extends \SimpleXMLElement
{

    static $urlNb;
    static $currentUrlNb;

    static $statusCode;

    public function parse($output)
    {
        if ($this->getName() == 'urlset') {
            static::$statusCode = array();

            static::$urlNb = $this->count();

            static::$currentUrlNb = 0;

            fputcsv($output, array('status code', 'url'), ';', '"');

        } else if ($this->getName() == 'loc') {
            $currentCode = $this->_getUrlStatusCode($this->__toString());
            fputcsv($output, array($currentCode, $this->__toString()), ';', '"');
            if (!isset(static::$statusCode[$currentCode]))
                static::$statusCode[$currentCode] = 1;
            else
                static::$statusCode[$currentCode]++;

            $this->_progressBar(++static::$currentUrlNb, static::$urlNb);
        }

        foreach ($this->children() as $child)
            $child->parse($output);

        if ($this->getName() == 'urlset')
            var_dump(static::$statusCode);
    }

    protected function _getUrlStatusCode($url)
    {
        try {
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => false,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_POST => false,
                CURLOPT_HTTPHEADER => array('Content-type: application/json')
            );

            $curl = curl_init();

            curl_setopt_array($curl, $options);

            curl_exec($curl);

            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            return $statusCode;
        } catch (\Exception $e) {
            echo ($e->getMessage());
            return 500;
        }
    }

    protected function _progressBar($done, $total)
    {
        $perc = floor(($done / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");
        fwrite(STDERR, $write);
    }
}