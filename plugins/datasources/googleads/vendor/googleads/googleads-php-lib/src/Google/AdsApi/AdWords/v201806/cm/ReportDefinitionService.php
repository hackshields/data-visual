<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ReportDefinitionService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('ApiError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\AuthorizationError', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ClientTermsError', 'CollectionSizeError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\CollectionSizeError', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DatabaseError', 'DateError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DateError', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DistinctError', 'EnumValuePair' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\EnumValuePair', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\InternalApiError', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NotEmptyError', 'NotWhitelistedError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NotWhitelistedError', 'NullError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NullError', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\OperatorError', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RejectedError', 'ReportDefinitionError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ReportDefinitionError', 'ReportDefinitionField' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ReportDefinitionField', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RequiredError', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SoapResponseHeader', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\StringLengthError', 'getReportFieldsResponse' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\getReportFieldsResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/cm/v201806/ReportDefinitionService?wsdl')
    {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        $options = array_merge(array('features' => 1), $options);
        parent::__construct($wsdl, $options);
    }
    /**
     * Returns the available report fields for a given report type.
     * When using this method the {@code clientCustomerId} header field is
     * optional. Callers are discouraged from setting the clientCustomerId
     * header field in calls to this method as its presence will trigger an
     * authorization error if the caller does not have access to the customer
     * with the included ID.
     *
     * {@link ReportDefinitionField} encapsulates the field name, the
     * field data type, and the enum values (if the field's type is
     * {@code enum}).
     * ReportDefinitionField information.
     *
     * @param \Google\AdsApi\AdWords\v201806\cm\ReportDefinitionReportType $reportType Constant: string - Valid values: KEYWORDS_PERFORMANCE_REPORT, AD_PERFORMANCE_REPORT, URL_PERFORMANCE_REPORT, ADGROUP_PERFORMANCE_REPORT, CAMPAIGN_PERFORMANCE_REPORT, ACCOUNT_PERFORMANCE_REPORT, GEO_PERFORMANCE_REPORT, SEARCH_QUERY_PERFORMANCE_REPORT, AUTOMATIC_PLACEMENTS_PERFORMANCE_REPORT, CAMPAIGN_NEGATIVE_KEYWORDS_PERFORMANCE_REPORT, CAMPAIGN_NEGATIVE_PLACEMENTS_PERFORMANCE_REPORT, DESTINATION_URL_REPORT, SHARED_SET_REPORT, CAMPAIGN_SHARED_SET_REPORT, SHARED_SET_CRITERIA_REPORT, CREATIVE_CONVERSION_REPORT, CALL_METRICS_CALL_DETAILS_REPORT, KEYWORDLESS_QUERY_REPORT, KEYWORDLESS_CATEGORY_REPORT, CRITERIA_PERFORMANCE_REPORT, CLICK_PERFORMANCE_REPORT, BUDGET_PERFORMANCE_REPORT, BID_GOAL_PERFORMANCE_REPORT, DISPLAY_KEYWORD_PERFORMANCE_REPORT, PLACEHOLDER_FEED_ITEM_REPORT, PLACEMENT_PERFORMANCE_REPORT, CAMPAIGN_NEGATIVE_LOCATIONS_REPORT, GENDER_PERFORMANCE_REPORT, AGE_RANGE_PERFORMANCE_REPORT, CAMPAIGN_LOCATION_TARGET_REPORT, CAMPAIGN_AD_SCHEDULE_TARGET_REPORT, PAID_ORGANIC_QUERY_REPORT, AUDIENCE_PERFORMANCE_REPORT, DISPLAY_TOPICS_PERFORMANCE_REPORT, USER_AD_DISTANCE_REPORT, SHOPPING_PERFORMANCE_REPORT, PRODUCT_PARTITION_REPORT, PARENTAL_STATUS_PERFORMANCE_REPORT, PLACEHOLDER_REPORT, AD_CUSTOMIZERS_FEED_ITEM_REPORT, LABEL_REPORT, FINAL_URL_REPORT, VIDEO_PERFORMANCE_REPORT, TOP_CONTENT_PERFORMANCE_REPORT, CAMPAIGN_CRITERIA_REPORT, CAMPAIGN_GROUP_PERFORMANCE_REPORT, LANDING_PAGE_REPORT, MARKETPLACE_PERFORMANCE_REPORT, UNKNOWN
     * @return \Google\AdsApi\AdWords\v201806\cm\ReportDefinitionField[]
     * @throws \Google\AdsApi\AdWords\v201806\cm\ApiException
     */
    public function getReportFields($reportType)
    {
        return $this->__soapCall('getReportFields', array(array('reportType' => $reportType)))->getRval();
    }
}

?>