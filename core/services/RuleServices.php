<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe RuleServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
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

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters[self::CST_SETTING]) ? $arrFilters[self::CST_SETTING] : '%');
    $arrParams[] = (isset($arrFilters['code']) ? $arrFilters['code'] : '%');
    $arrParams[] = (isset($arrFilters[self::CST_DESCRIPTION]) ? $arrFilters[self::CST_DESCRIPTION] : '%');
    return $arrParams;
  }
  /**
   * @param string $file
   * @param string $line
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getRulesWithFilters($file, $line, $arrFilters=array(), $orderby='code', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters($file, $line, $arrParams);
  }
  /**
   * @param string $file
   * @param string $line
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @param bool $multiple
   * @param string $defaultLabel
   * @return string
   */
  public function getRuleSelect($file, $line, $value='', $prefix='id', $classe=self::CST_FORMCONTROL, $multiple=false, $defaultLabel='')
  {
    $Rules = $this->getRulesWithFilters($file, $line);
    $arrSetLabels = array();
    foreach ($Rules as $Rule) {
      $arrSetLabels[$Rule->getId()] = $Rule->getCode();
    }
    $this->labelDefault = $defaultLabel;
    $this->classe = $classe;
    $this->multiple = $multiple;
    return $this->getSetSelect($file, $line, $arrSetLabels, $prefix.'ruleId', $value);
  }
  /**
   * @param string $file
   * @param string $line
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @return string
   */
  public function getRuleNoSettingSelect($file, $line, $value, $prefix='id', $classe=self::CST_FORMCONTROL)
  {
    $Rules = $this->getRulesWithFilters($file, $line, array(self::CST_SETTING=>0));
    $arrSetLabels = array();
    foreach ($Rules as $Rule) {
      $arrSetLabels[$Rule->getId()] = $Rule->getCode();
    }
    $this->labelDefault = '---';
    $this->classe = $classe;
    return $this->getSetSelect($file, $line, $arrSetLabels, $prefix.'ruleId', $value);
  }
  /**
   * @param string $file
   * @param string $line
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @return string
   */
  public function getRuleSettingSelect($file, $line, $value, $prefix='id', $classe=self::CST_FORMCONTROL)
  {
    $Rules = $this->getRulesWithFilters($file, $line, array(self::CST_SETTING=>1));
    $arrSetLabels = array();
    foreach ($Rules as $Rule) {
      $arrSetLabels[$Rule->getId()] = $Rule->getCode();
    }
    $this->labelDefault = '---';
    $this->classe = $classe;
    return $this->getSetSelect($file, $line, $arrSetLabels, $prefix.'settingId', $value);
  }
}
