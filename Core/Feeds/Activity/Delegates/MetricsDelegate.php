<?php

namespace Minds\Core\Feeds\Activity\Delegates;

use Minds\Core;
use Minds\Core\Analytics\Metrics\Event;
use Minds\Core\Wire\Paywall\PaywallEntityInterface;
use Minds\Entities\Activity;
use Minds\Helpers\Counters;

class MetricsDelegate
{
    /**
     * On adding a new post
     * @param Activity $activity
     * @return void
     */
    public function onAdd(Activity $activity): void
    {
        if ($activity->isRemind() || $activity->isQuotedPost()) {
            $remind = $activity->getRemind();

            // Submit to events engine

            $event = new Event();
            $event->setType('action')
                ->setAction('remind')
                ->setProduct('platform')
                ->setUserGuid((string) $activity->getOwnerGuid())
                ->setUserPhoneNumberHash(Core\Session::getLoggedInUser()->getPhoneNumberHash())
                ->setEntityGuid((string) $remind->getGuid())
                ->setEntityContainerGuid((string) $remind->getContainerGuid())
                ->setEntityType($remind->getType())
                ->setEntitySubtype((string) $remind->getSubtype())
                ->setEntityOwnerGuid((string) $remind->getOwnerGuid());

            if ($remind instanceof PaywallEntityInterface) {
                $wireThreshold = $remind->getWireThreshold();
                if ($wireThreshold['support_tier'] ?? null) {
                    $event->setSupportTierUrn($wireThreshold['support_tier']['urn']);
                }
            }

            $event->push();
        }

        if ($activity->isRemind()) {
            // Update remind counters (legacy support)
            Counters::increment($remind->getGuid(), 'remind');
        }
    }

    /**
     * On activity deleted
     * @param Activity $activity
     * @return void
     */
    public function onDelete(Activity $activity): void
    {
        if ($activity->isRemind()) {
            $remind = $activity->getRemind();
            if (!$remind) {
                return; // Original post may have been deleted too
            }
            // Update remind counters (legacy support)
            Counters::decrement($remind->getGuid(), 'remind');
        }
    }
}
