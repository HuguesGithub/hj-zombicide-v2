<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe DurationServices
 * @author Hugues.
 * @since 1.04.16
 * @version 1.04.27
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

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_MINDURATION));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_MAXDURATION));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getDurationsWithFilters($arrFilters=array(), $orderby=self::FIELD_MINDURATION, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param int $id
   * @return Duration
   */
  public function selectDuration($id)
  { return $this->select(__FILE__, __LINE__, $id); }

}
