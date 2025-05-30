<?php

namespace Dotdigitalgroup\B2b\Helper;

use Magento\Company\Api\Data\CompanyCustomerInterface;
use Magento\Company\Api\Data\CompanyInterface;

interface ConfigInterface
{
    /*
     * Config paths for B2B data fields
     */
    public const XML_PATH_CONNECTOR_B2B_CUSTOMER_TYPE = 'connector_data_mapping/b2b_extra_data/customer_type';
    public const XML_PATH_CONNECTOR_B2B_COMPANY = 'connector_data_mapping/b2b_extra_data/company';
    public const XML_PATH_CONNECTOR_B2B_COMPANY_STATUS = 'connector_data_mapping/b2b_extra_data/company_status';
    public const XML_PATH_CONNECTOR_B2B_STORE_CREDIT_BALANCE =
        'connector_data_mapping/b2b_extra_data/store_credit_balance';
    public const XML_PATH_CONNECTOR_SYNC_SHARED_CATALOG_ENABLED = 'sync_settings/sync/shared_catalog_enabled';
    public const XML_PATH_CONNECTOR_SYNC_QUOTE_ENABLED = 'sync_settings/sync/quote_enabled';

    /**
     * Map of B2B config fields to EC data fields
     */
    public const CONTACT_B2B_DATA_FIELDS = [
        'customer_type' => [
            'name' => 'CUSTOMER_TYPE',
            'type' => 'string',
            'visibility' => 'private'
        ],
        'company' => [
            'name' => 'COMPANY',
            'type' => 'string',
            'visibility' => 'private'
        ],
        'company_status' => [
            'name' => 'COMPANY_STATUS',
            'type' => 'string',
            'visibility' => 'private'
        ],
        'store_credit_balance' => [
            'name' => 'STORE_CREDIT_BALANCE',
            'type' => 'numeric',
            'visibility' => 'private'
        ],
        'shared_catalog_name' => [
            'name' => 'SHARED_CATALOG_NAME',
            'type' => 'string',
            'visibility' => 'private',
        ],
        'sales_representative' => [
            'name' => 'SALES_REPRESENTATIVE',
            'type' => 'string',
            'visibility' => 'private',
        ],
        'sales_rep_email' => [
            'name' => 'SALES_REP_EMAIL',
            'type' => 'string',
            'visibility' => 'private',
        ],
    ];

    /**
     * Company status value labels
     */
    public const COMPANY_STATUS_LABELS = [
        CompanyInterface::STATUS_PENDING => 'Pending',
        CompanyInterface::STATUS_APPROVED => 'Approved',
        CompanyInterface::STATUS_BLOCKED => 'Blocked',
        CompanyInterface::STATUS_REJECTED => 'Rejected',
    ];

    /*
     * Customer type value labels
     */
    public const CUSTOMER_TYPE_COMPANY_ADMIN ='Company admin';
    public const CUSTOMER_TYPE_COMPANY_USER = 'Company user';
    public const CUSTOMER_TYPE_INDIVIDUAL_USER = 'Individual user';

    public const SHARED_CATALOG_NAME_DEFAULT = 'Default';
}
