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
class BudgetService extends \Google\AdsApi\Common\AdsSoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array('ApiError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApiError', 'ApiException' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApiException', 'ApplicationException' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ApplicationException', 'AuthenticationError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\AuthenticationError', 'AuthorizationError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\AuthorizationError', 'Budget' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Budget', 'BudgetError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\BudgetError', 'BudgetOperation' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\BudgetOperation', 'BudgetPage' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\BudgetPage', 'BudgetReturnValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\BudgetReturnValue', 'ClientTermsError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ClientTermsError', 'ComparableValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ComparableValue', 'DatabaseError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DatabaseError', 'DateError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DateError', 'DateRange' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DateRange', 'DateRangeError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DateRangeError', 'DistinctError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DistinctError', 'DoubleValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\DoubleValue', 'EntityCountLimitExceeded' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\EntityCountLimitExceeded', 'EntityNotFound' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\EntityNotFound', 'FieldPathElement' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\FieldPathElement', 'IdError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\IdError', 'InternalApiError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\InternalApiError', 'ListReturnValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ListReturnValue', 'LongValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\LongValue', 'Money' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Money', 'NewEntityCreationError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NewEntityCreationError', 'NotEmptyError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NotEmptyError', 'NullError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NullError', 'NumberValue' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\NumberValue', 'Operation' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Operation', 'OperationAccessDenied' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\OperationAccessDenied', 'OperatorError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\OperatorError', 'OrderBy' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\OrderBy', 'Page' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Page', 'Paging' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Paging', 'Predicate' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Predicate', 'QueryError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\QueryError', 'QuotaCheckError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\QuotaCheckError', 'RangeError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RangeError', 'RateExceededError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RateExceededError', 'ReadOnlyError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\ReadOnlyError', 'RejectedError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RejectedError', 'RequestError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RequestError', 'RequiredError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\RequiredError', 'Selector' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\Selector', 'SelectorError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SelectorError', 'SizeLimitError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SizeLimitError', 'SoapHeader' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SoapHeader', 'SoapResponseHeader' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\SoapResponseHeader', 'StringFormatError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\StringFormatError', 'StringLengthError' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\StringLengthError', 'getResponse' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\getResponse', 'mutateResponse' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\mutateResponse', 'queryResponse' => 'Google\\AdsApi\\AdWords\\v201806\\cm\\queryResponse');
    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://adwords.google.com/api/adwords/cm/v201806/BudgetService?wsdl')
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
     * Returns a list of budgets that match the selector.
     *
     * occurred while retrieving results.
     *
     * @param \Google\AdsApi\AdWords\v201806\cm\Selector $selector
     * @return \Google\AdsApi\AdWords\v201806\cm\BudgetPage
     * @throws \Google\AdsApi\AdWords\v201806\cm\ApiException
     */
    public function get(\Google\AdsApi\AdWords\v201806\cm\Selector $selector)
    {
        return $this->__soapCall('get', array(array('selector' => $selector)))->getRval();
    }
    /**
     * Applies the list of mutate operations.
     *
     * @param \Google\AdsApi\AdWords\v201806\cm\BudgetOperation[] $operations
     * @return \Google\AdsApi\AdWords\v201806\cm\BudgetReturnValue
     * @throws \Google\AdsApi\AdWords\v201806\cm\ApiException
     */
    public function mutate(array $operations)
    {
        return $this->__soapCall('mutate', array(array('operations' => $operations)))->getRval();
    }
    /**
     * Returns the list of budgets that match the query.
     *
     * @param string $query
     * @return \Google\AdsApi\AdWords\v201806\cm\BudgetPage
     * @throws \Google\AdsApi\AdWords\v201806\cm\ApiException
     */
    public function query($query)
    {
        return $this->__soapCall('query', array(array('query' => $query)))->getRval();
    }
}

?>