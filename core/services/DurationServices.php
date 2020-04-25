<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe DurationServices
 * @author Hugues.
 * @since 1.04.16
 * @version 1.04.16
 */
class DurationServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var DurationDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new DurationDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters[self::FIELD_MINDURATION]) ? $arrFilters[self::FIELD_MINDURATION] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_MAXDURATION]) ? $arrFilters[self::FIELD_MAXDURATION] : '%');
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getDurationsWithFilters($arrFilters=array(), $orderby=self::FIELD_MINDURATION, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

  public function selectDuration($id)
  { return $this->select(__FILE__, __LINE__, $id); }

}
