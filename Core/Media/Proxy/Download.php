<?php

/**
 * Minds Media Proxy Download.
 *
 * @author emi
 */

namespace Minds\Core\Media\Proxy;

use Minds\Core\Di\Di;
use Minds\Helpers\File;
use Minds\Core\Http\Curl\Client;
use Minds\Traits\MagicAttributes;

/**
 * @method self setSrc(string $src)
 */
class Download
{
    use MagicAttributes;

    /** @var Client $http */
    protected $http;

    /** @var array */
    protected $blacklist;

    /** @var string $src */
    protected $src;

    /** @var int $timeout */
    protected $timeout = 2;

    /** @var int $limitkb */
    protected $limitKb;

    public function __construct($http = null, $blacklist = null)
    {
        $this->http = $http ?: Di::_()->get('Http');
        $this->blacklist = $blacklist ?: Di::_()->get('Config')->get('internal_blacklist');
    }

    /**
     * Checks if src is a supported URL.
     *
     * @return bool
     */
    public function isValidSrc()
    {
        if (!filter_var($this->src, FILTER_VALIDATE_URL)) {
            return false;
        }

        $re = '/(http[s]?:\/\/)([\w\W]*)/';
        $matches = [];
        preg_match($re, $this->src, $matches);

        if (!count($matches) > 0) { // this means it will use either http or https protocols
            return false;
        }

        $url = $matches[2];

        //check if internal subnet
        if (strpos($url, '10.', 0) === 0
            || strpos($url, '192.168.', 0) === 0
            || strpos($url, '172.16.', 0) === 0
        ) {
            return false;
        }

        //check if blacklisted
        foreach ($this->blacklist as $domain) {
            if (strpos($url, $domain, 0) !== false) {
                return false;
            }
        }

        return true;
    }

    public function downloadBinaryString()
    {
        if (!$this->src || !$this->isValidSrc()) {
            throw new \Exception('Invalid URL');
        }

        $content = $this->http->get($this->src, [
            'curl' => [
                CURLOPT_USERAGENT => 'MindsMediaProxy/3.0 (+http://www.minds.com/)',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_NOSIGNAL => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CONNECTTIMEOUT_MS => $this->timeout * 1000,
                CURLOPT_TIMEOUT_MS => $this->timeout * 1000,
            ],
            'limit' => $this->limitKb ? $this->limitKb : null,
        ]);

        if (!$content) {
            throw new \Exception('Invalid image');
        }

        $mime = File::getMime($content);

        if (!$mime) {
            throw new \Exception('Cannot read image MIME');
        } elseif (strpos($mime, 'image/') !== 0) {
            throw new \Exception('Content is not an image');
        }

        return $content;
    }

    public function download()
    {
        $content = $this->downloadBinaryString();

        $resource = imagecreatefromstring($content);

        if (!$resource) {
            throw new \Exception('Image type not supported');
        }

        return $resource;
    }
}
