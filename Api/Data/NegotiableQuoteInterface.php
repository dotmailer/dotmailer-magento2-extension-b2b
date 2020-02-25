<?php

namespace Dotdigitalgroup\B2b\Api\Data;

interface NegotiableQuoteInterface
{
    const QUOTE_ID = 'quote_id';
    const WEBSITE_ID = 'website_id';
    const QUOTE_IMPORTED = 'quote_imported';

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @return bool
     */
    public function getQuoteImported();

    /**
     * @param $quoteId
     */
    public function setQuoteId($quoteId);

    /**
     * @param $websiteId
     */
    public function setWebsiteId($websiteId);

    /**
     * @param $imported
     */
    public function setQuoteImported($imported);
}
