<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe LevelServices
 * @author Hugues.
 * @since 1.04.16
 * @version 1.04.16
 */
class LevelServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var LevelDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new LevelDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    array_push($arrParams, (isset($arrFilters[self::FIELD_NAME]) ? $arrFilters[self::FIELD_NAME] : '%'));
    return $arrParams;
  }

  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getLevelsWithFilters($arrFilters=array(), $orderby=self::FIELD_ID, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

}
