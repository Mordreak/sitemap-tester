<?php
/**
 * Created by PhpStorm.
 * User: theo
 * Date: 25/04/18
 * Time: 14:29
 */

namespace Sourcecode;

use Sourcecode\SitemapParser;

class CommandParser
{
    const DEFAULT_ARGS = array(
        'f' => PWD . '/sitemap.xml',
        'o' => PWD . '/output.csv'
    );

    protected $_arguments = null;

    protected $_input = null;
    protected $_output = null;

    public function __construct()
    {
        $this->_arguments = getopt('f:o:h');
        $this->_parse();
    }

    protected function _parse()
    {
        try {

            if (isset($this->_arguments['h'])) {
                $this->_help();
                exit(0);
            }

            if (!isset($this->_arguments['f']))
                $this->_arguments['f'] = self::DEFAULT_ARGS['f'];

            if (!isset($this->_arguments['o']))
                $this->_arguments['o'] = self::DEFAULT_ARGS['o'];

            if (!file_exists($this->_arguments['f']))
                throw new \InvalidArgumentException("The sitemap file doesn't exist or could not be found!\n");

            $this->_input = simplexml_load_file($this->_arguments['f'], SitemapParser::class);

            $this->_output = fopen($this->_arguments['o'], 'w');

        } catch (\InvalidArgumentException $e) {
            echo "\033[0;31m" . $e->getMessage() . "\033[0m";
            $this->_help();
            exit(1);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    protected function _help()
    {
        echo "Usage: php check.php (-f sitemap.xml -o output.csv -h)\n";
        echo "Options:\n";
        echo "-f : specify here an input file (default: sitemap.xml)\n";
        echo "-o : specify here an ouput file (default: output.csv)\n";
        echo "-h : displays this help message\n";
    }

    public function getInput()
    {
        return $this->_input;
    }

    public function getOutput()
    {
        return $this->_output;
    }
}