<?php

/**
 * Etherscan Service
 *
 * @author Nico, Martin
 */

namespace Minds\Core\Blockchain\Services;

use Minds\Core\Di\Di;
use Minds\Core\Config;
use Minds\Core\Http\Curl\Json\Client;

class Etherscan
{
    /** @var Client $http */
    protected $http;

    /** @var string $address */
    protected $address;

    /** @var string $apiKey */
    protected $apiKey;

    /** @var string */
    protected $contractAddress;

    /**
     * Etherscan constructor.
     * @param Http\Json $http
     * @param Config $config
     */
    public function __construct($http = null, $config = null)
    {
        $this->http = $http ?: Di::_()->get('Http\Json');
        $config = $config ?: Di::_()->get('Config');
        $blockchainConfig = $config->get('blockchain');
        $this->apiKey = ($blockchainConfig['etherscan'] ?? [])['api_key'] ?? null;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setContractAddress($address)
    {
        $this->contractAddress = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getContractAddress()
    {
        return $this->contractAddress;
    }

    /**
     * Method to get balance eth by address.
     * @return float
     * @throws \Exception
     */
    public function getAccountBalance()
    {
        $balance = $this->request("module=account&action=balance&address={$this->address}&tag=latest&apikey={$this->apiKey}");
        return $balance['result'];
    }

    /**
     * Method to get eth total supply by contract.
     * @return float
     * @throws \Exception
     */
    public function getContractTotalSupply()
    {
        $balance = $this->request("module=stats&action=tokenSupply&contractaddress={$this->contractAddress}&apikey={$this->apiKey}");
        return $balance;
    }

    /**
     * Method to get eth total supply.
     * @return float
     * @throws \Exception
     */
    public function getTotalSupply()
    {
        $balance = $this->request("module=stats&action=tokenSupply&tokenname=MINDS&apikey={$this->apiKey}");
        return $balance;
    }

    /**
     * Proxy method to get transaction count, gives 0x1 as result.
     * @return float
     * @throws \Exception
     */
    public function getTransactionsCount()
    {
        $balance = $this->request("module=proxy&action=eth_getTransactionCount&tag=latest&address={$this->address}&apikey={$this->apiKey}");
        return $balance;
    }

    /**
     * Method to make the actual request by a given endpoint.
     * Get transacionts
     *
     * @param integer $from
     * @param integer $to
     * @param integer $page
     * @param integer $count
     * @return array
     */
    public function getTransactions($from, $to, $page=null, $count=100)
    {
        $endpoint = "module=account&action=txlist&address={$this->address}&startblock={$from}&endblock={$to}&sort=desc".($page ? "&page=$page&offset=$count" : '');
        $endpoint .= "&apikey={$this->apiKey}";

        $transactions = $this->request($endpoint);

        return $transactions['result'];
    }

    /**
     * Get internal transacionts
     *
     * @param integer $from
     * @param integer $to
     * @param integer $page
     * @param integer $count
     * @return array
     */
    public function getInternalTransactions($from, $to, $page=null, $count=100)
    {
        $endpoint = "module=account&action=txlistinternal&address={$this->address}&startblock={$from}&endblock={$to}&sort=desc".($page ? "&page=$page&offset=$count" : '');
        $endpoint .= "&apikey={$this->apiKey}";

        $transactions = $this->request($endpoint);

        return $transactions['result'];
    }

    /**
     * Get Token Transfer Events
     *
     * @param integer $from
     * @param integer $to
     * @param integer $page
     * @param integer $count
     * @return array
     */
    public function getTokenTransactions($from, $to, $page=null, $count=100)
    {
        $endpoint = "module=account&action=tokentx&address={$this->address}&startblock={$from}&endblock={$to}&sort=desc".($page ? "&page=$page&offset=$count" : '');
        $endpoint .= "&apikey={$this->apiKey}";

        $transactions = $this->request($endpoint);

        return $transactions['result'];
    }

    /**
     * Get transaction by hash
     *
     * @param string $hash
     * @return array
     */
    public function getTransaction($hash)
    {
        $result = $this->request("module=proxy&action=eth_getTransactionReceipt&txhash={$hash}&apikey={$this->apiKey}");
        return $result['result'];
    }

    /**
     * Return the number of the last block of the blockchain
     *
     * @return integer
     */
    public function getLastBlockNumber()
    {
        $result = $this->request("module=proxy&action=eth_blockNumber&apikey={$this->apiKey}");
        return hexdec($result['result']);
    }

    /**
     * Return the last block of the blockchain
     *
     * @return int
     */
    public function getLastBlock()
    {
        $number = $this->getLastBlockNumber();
        if ($number) {
            return $this->getBlock($number);
        } else {
            return 0;
        }
    }

    /**
     * Get a block by number
     *
     * @param integer $number
     * @return int
     */
    public function getBlock($number)
    {
        $result = $this->request("module=block&action=getblockreward&blockno={$number}&apikey={$this->apiKey}");
        return $result['result'];
    }

    /**
     * Return the block number from a unix timestamp
     * @param int $unixTimestamp
     * @return int
     */
    public function getBlockNumberByTimestamp(int $unixTimestamp): int
    {
        $result = $this->request("module=block&action=getblocknobytime&timestamp=$unixTimestamp&closest=before&apikey={$this->apiKey}");
        return (int) $result['result'];
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws \Exception
     */
    protected function request($endpoint)
    {
        $response = $this->http->get("https://api.etherscan.io/api?{$endpoint}");

        if (!is_array($response)) {
            throw new \Exception('Invalid response');
        }

        return $response;
    }
}
