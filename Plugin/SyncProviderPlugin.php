<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\Sync\NegotiableQuoteFactory;
use Dotdigitalgroup\Email\Console\Command\Provider\SyncProvider;

class SyncProviderPlugin
{
    /**
     * @var NegotiableQuoteFactory
     */
    private $negotiableQuoteFactory;

    /**
     * @param NegotiableQuoteFactory $negotiableQuoteFactory
     */
    public function __construct(NegotiableQuoteFactory $negotiableQuoteFactory)
    {
        $this->negotiableQuoteFactory = $negotiableQuoteFactory;
    }

    /**
     * @param SyncProvider $syncProvider
     * @param array $additionalSyncs
     * @return array
     */
    public function beforeGetAvailableSyncs(SyncProvider $syncProvider, array $additionalSyncs = [])
    {
        return [
            'additionalSyncs' => get_object_vars($this),
        ];
    }
}
