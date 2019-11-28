<?php
/**
 * Aggregate base
 */
namespace Minds\Core\Trending\Aggregates;

use Minds\Core\Di\Di;

class Aggregate
{
    protected $client;
    protected $type;
    protected $subtype;
    protected $from;
    protected $to;
    protected $limit = 50;

    public function __construct($client = null)
    {
        $this->client = $client ?: Di::_()->get('Database\ElasticSearch');
        $this->from = strtotime('-24 hours') * 1000;
        $this->to = time() * 1000;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;
        return $this;
    }

    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function get()
    {
        return [];
    }
}
