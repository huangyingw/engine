<?php
/**
 * ResolverDelegate.
 *
 * @author emi
 */

namespace Minds\Core\Entities\Delegates;

use Minds\Common\Urn;

interface ResolverDelegate
{
    /**
     * @param Urn $urn
     * @return boolean
     */
    public function shouldResolve(Urn $urn): bool;

    /**
     * @param Urn[] $urns
     * @param array $opts
     * @return array
     */
    public function resolve(array $urns, array $opts = []): ?array;

    /**
     * @param string $urn
     * @param mixed $entity
     * @return mixed
     */
    public function map($urn, $entity);

    /**
     * @param mixed $entity
     * @return string|null
     */
    public function asUrn($entity): ?string;
}
