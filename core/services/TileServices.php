<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TileServices
 * @author Hugues.
 * @since 1.04.07
 * @version 1.07.22
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

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_EXPANSIONID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_ACTIVETILE));
  }

  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getTilesWithFilters($arrFilters=array(), $orderby=self::FIELD_CODE, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
}
