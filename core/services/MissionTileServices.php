<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionTileServices
 * @author Hugues.
 * @since 1.04.07
 * @version 1.07.25
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

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_MISSIONID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_COORDX));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_COORDY));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getMissionTilesWithFilters($arrFilters=array(), $orderby=self::FIELD_ID, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }

  public function insertMissionTile($MissionTile)
  { return $this->insert(__FILE__, __LINE__, $MissionTile); }
}
