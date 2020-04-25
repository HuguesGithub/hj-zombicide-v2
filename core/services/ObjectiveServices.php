<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ObjectiveServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
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

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters['code']) ? $arrFilters['code'] : '%');
    $arrParams[] = (isset($arrFilters['description']) ? $arrFilters['description'] : '%');
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
  public function getObjectivesWithFilters($file, $line, $arrFilters=array(), $orderby='code', $order='asc')
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
  public function getObjectiveSelect($file, $line, $value='', $prefix='id', $classe='form-control', $multiple=false, $defaultLabel='---')
  {
    $Objectives = $this->getObjectivesWithFilters($file, $line);
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
