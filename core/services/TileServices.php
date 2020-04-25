<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TileServices
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.07
 */
class TileServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var TileDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new TileDaoImpl();
  }

  private function buildFilters($arrF)
  {
    $arrParams = array();
    array_push($arrParams, (isset($arrF[self::FIELD_CODE]) ? $arrF[self::FIELD_CODE] : '%'));
    array_push($arrParams, (isset($arrF[self::FIELD_EXPANSIONID]) ? $arrF[self::FIELD_EXPANSIONID] : '%'));
    array_push($arrParams, (isset($arrF[self::FIELD_ACTIVETILE]) ? $arrF[self::FIELD_ACTIVETILE] : '%'));
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getTilesWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

  public function selectTile($id)
  { return $this->select(__FILE__, __LINE__, $id); }
}
