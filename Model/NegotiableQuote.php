<?php

namespace Dotdigitalgroup\B2b\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;

class NegotiableQuote extends AbstractExtensibleModel implements NegotiableQuoteInterface
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
     * NegotiableQuote constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime $dateTime,
        Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dateTime = $dateTime;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
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
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @param $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {

        $this->setData(self::QUOTE_ID, $quoteId);
        return $this;
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @param $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID, $websiteId);
        return $this;
    }

    /**
     * @return bool
     */
    public function getQuoteImported()
    {
        return $this->getData(self::QUOTE_IMPORTED);
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * @param $imported
     * @return $this
     */
    public function setQuoteImported($imported)
    {
        $this->setData(self::QUOTE_IMPORTED, $imported);
        return $this;
    }

    /**
     * @param $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->setData(self::EXPIRATION_DATE);
        return $this;
    }
}
