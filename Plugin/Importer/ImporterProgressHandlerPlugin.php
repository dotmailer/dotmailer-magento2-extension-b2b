<?php

namespace Dotdigitalgroup\B2b\Plugin\Importer;

use Dotdigitalgroup\Email\Model\Sync\Importer\ImporterProgressHandler;
use Dotdigitalgroup\B2b\Model\Sync\NegotiableQuote;

class ImporterProgressHandlerPlugin
{
    /**
     * Append B2B_Quotes imports to V3 set.
     *
     * @param ImporterProgressHandler $subject
     * @param array $result
     * @return array
     */
    public function afterGetInProgressGroups(ImporterProgressHandler $subject, array $result)
    {
        $result[ImporterProgressHandler::VERSION_2]
        [ImporterProgressHandler::TRANSACTIONAL]
        [ImporterProgressHandler::PROGRESS_GROUP_TYPES][] = NegotiableQuote::IMPORT_TYPE_B2B_QUOTES;
        return $result;
    }
}
