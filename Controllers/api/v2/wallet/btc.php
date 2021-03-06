<?php
/**
 * BTC Wallet Controller
 *
 * @version 1
 * @author Mark Harding
 */
namespace Minds\Controllers\api\v2\wallet;

use Minds\Core;
use Minds\Core\Entities\Actions;
use Minds\Helpers;
use Minds\Interfaces;
use Minds\Api\Factory;
use Minds\Core\Payments;
use Minds\Entities;

class btc implements Interfaces\Api
{
    /**
     * @param array $pages
     */
    public function get($pages)
    {
        return Factory::response([]);
    }

    /**
     * @param array $pages
     */
    public function post($pages)
    {
        return Factory::response([]);
    }

    public function put($pages)
    {
        return Factory::response([]);
    }

    public function delete($pages)
    {
        return Factory::response([]);
    }
}
