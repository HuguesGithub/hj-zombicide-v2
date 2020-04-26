<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe RuleServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.27
 */
class RuleServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var RuleDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new RuleDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_SETTING));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_DESCRIPTION));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getRulesWithFilters($arrFilters=array(), $orderby=self::FIELD_CODE, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @return string
   */
  public function getRuleNoSettingSelect($value, $prefix='id', $classe=self::CST_FORMCONTROL)
  {
    $Rules = $this->getRulesWithFilters(array(self::FIELD_SETTING=>0));
    $arrSetLabels = array();
    foreach ($Rules as $Rule) {
      $arrSetLabels[$Rule->getId()] = $Rule->getCode();
    }
    $this->labelDefault = '---';
    $this->classe = $classe;
    return $this->getSetSelect(__FILE__, __LINE__, $arrSetLabels, $prefix.'ruleId', $value);
  }
  /**
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @return string
   */
  public function getRuleSettingSelect($value, $prefix='id', $classe=self::CST_FORMCONTROL)
  {
    $Rules = $this->getRulesWithFilters(array(self::FIELD_SETTING=>1));
    $arrSetLabels = array();
    foreach ($Rules as $Rule) {
      $arrSetLabels[$Rule->getId()] = $Rule->getCode();
    }
    $this->labelDefault = '---';
    $this->classe = $classe;
    return $this->getSetSelect(__FILE__, __LINE__, $arrSetLabels, $prefix.'settingId', $value);
  }
}
