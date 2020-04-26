<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ObjectiveServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.27
 */
class ObjectiveServices extends LocalServices
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
    $this->Dao = new ObjectiveDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_DESCRIPTION));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getObjectivesWithFilters($arrFilters=array(), $orderby=self::FIELD_CODE, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @param bool $multiple
   * @param string $defaultLabel
   * @return string
   */
  public function getObjectiveSelect($value='', $prefix='id', $classe='form-control', $multiple=false, $defaultLabel='---')
  {
    $Objectives = $this->getObjectivesWithFilters();
    $arrSetLabels = array();
    foreach ($Objectives as $Objective) {
      $arrSetLabels[$Objective->getId()] = $Objective->getCode();
    }
    $this->labelDefault = $defaultLabel;
    $this->classe = $classe;
    $this->multiple = $multiple;
    return $this->getSetSelect($file, $line, $arrSetLabels, $prefix.'objectiveId', $value);
  }
}
