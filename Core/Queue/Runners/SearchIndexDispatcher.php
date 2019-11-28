<?php

/**
 * Search Index Dispatcher
 *
 * @author emi
 */

namespace Minds\Core\Queue\Runners;

use Minds\Core;
use Minds\Core\Di\Di;
use Minds\Core\Queue;
use Minds\Core\Queue\Interfaces\QueueRunner;

class SearchIndexDispatcher implements QueueRunner
{
    /**
     * Runs the queue
     */
    public function run()
    {
        $client = Queue\Client::build();

        $client
            ->setQueue("SearchIndexDispatcher")
            ->receive(function (Queue\Message $message) {
                /** @var Core\Events\Dispatcher $dispatcher */
                $dispatcher = Di::_()->get('EventsDispatcher');

                $data = $message->getData();
                $dispatcher->trigger('search:index:dispatch', 'all', $data);
            });
    }
}
