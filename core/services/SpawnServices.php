<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SpawnServices
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class SpawnServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var SpawnDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new SpawnDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrFilters, self::FIELD_EXPANSIONID) ? $arrFilters[self::FIELD_EXPANSIONID] : '%'));
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrFilters, self::CST_SPAWNNUMBER) ? $arrFilters[self::CST_SPAWNNUMBER] : '%'));
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSpawnsWithFilters($arrFilters=array(), $orderby=self::CST_SPAWNNUMBER, $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }
}
