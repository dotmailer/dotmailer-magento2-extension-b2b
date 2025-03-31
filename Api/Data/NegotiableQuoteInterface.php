<?php

namespace Dotdigitalgroup\B2b\Api\Data;

interface NegotiableQuoteInterface
{
    public const QUOTE_ID = 'quote_id';
    public const WEBSITE_ID = 'website_id';
    public const QUOTE_IMPORTED = 'quote_imported';
    public const EXPIRATION_DATE = 'expiration_date';

    /**
     * Get quote id.
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Get website id.
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Get quote imported.
     *
     * @return bool
     */
    public function getQuoteImported();

    /**
     * Set quote id.
     *
     * @param int $quoteId
     */
    public function setQuoteId($quoteId);

    /**
     * Set website id.
     *
     * @param int $websiteId
     */
    public function setWebsiteId($websiteId);

    /**
     * Set quote imported.
     *
     * @param bool $imported
     */
    public function setQuoteImported($imported);

    /**
     * Set expiration date.
     *
     * @param string $expirationDate
     */
    public function setExpirationDate($expirationDate);
}
