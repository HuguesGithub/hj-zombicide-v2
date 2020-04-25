<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionTileServices
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.07
 */
class MissionTileServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var MissionTileDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new MissionTileDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters[self::FIELD_MISSIONID]) ? $arrFilters[self::FIELD_MISSIONID] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_COORDX]) ? $arrFilters[self::FIELD_COORDX] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_COORDY]) ? $arrFilters[self::FIELD_COORDY] : '%');
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getMissionTilesWithFilters($arrFilters=array(), $orderby='id', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

}
