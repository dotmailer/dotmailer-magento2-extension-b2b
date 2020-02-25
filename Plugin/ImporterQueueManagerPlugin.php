<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\Sync\NegotiableQuote;
use Dotdigitalgroup\Email\Model\Sync\Importer\ImporterQueueManager;

class ImporterQueueManagerPlugin
{
    /**
     * @param ImporterQueueManager $importerQueueManager
     * @param array $additionalImportTypes
     * @return array
     */
    public function beforeGetBulkQueue(
        ImporterQueueManager $importerQueueManager,
        array $additionalImportTypes = []
    ) {
        return [
            'additionalImportTypes' => [
                NegotiableQuote::IMPORT_TYPE_B2B_QUOTES
            ]
        ];
    }
}
