<?php

namespace Dotdigitalgroup\B2b\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;

class NegotiableQuote extends AbstractModel implements NegotiableQuoteInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime $dateTime,
        Context $context,
        \Magento\Framework\Registry $registry,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dateTime = $dateTime;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote::class);
        parent::_construct();
    }

    /**
     * Prepare data to be saved to database.
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $this->setUpdatedAt($this->dateTime->formatDate(true));

        return $this;
    }

    /**
     * Get quote id.
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * Set quote id.
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(self::QUOTE_ID, $quoteId);
        return $this;
    }

    /**
     * Get website id.
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * Set website id.
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID, $websiteId);
        return $this;
    }

    /**
     * Get quote imported.
     *
     * @return bool
     */
    public function getQuoteImported()
    {
        return $this->getData(self::QUOTE_IMPORTED);
    }

    /**
     * Get expiration date.
     *
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * Set quote imported.
     *
     * @param bool $imported
     * @return $this
     */
    public function setQuoteImported($imported)
    {
        $this->setData(self::QUOTE_IMPORTED, $imported);
        return $this;
    }

    /**
     * Set expiration date.
     *
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->setData(self::EXPIRATION_DATE);
        return $this;
    }
}
