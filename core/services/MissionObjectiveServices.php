<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionObjectiveServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
 */
class MissionObjectiveServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var MissionRuleDaoImpl $Dao
   */
  protected $Dao;

  public function __construct()
  {
    parent::__construct();
    $this->Dao = new MissionObjectiveDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    array_push($arrParams, !empty($arrFilters['missionId']) ? $arrFilters['missionId'] : '%');
    array_push($arrParams, $arrFilters['objectiveId']!='' ? $arrFilters['objectiveId'] : '%');
    array_push($arrParams, !empty($arrFilters['title']) ? $arrFilters['title'] : '%');
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
  public function getMissionObjectivesWithFilters($file, $line, $arrFilters=array(), $orderby='id', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters($file, $line, $arrParams);
  }

}
