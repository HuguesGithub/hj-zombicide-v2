<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SpawnServices
 * @author Hugues.
 * @since 1.04.27
 * @version 1.04.27
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

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_EXPANSIONID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_SPAWNNUMBER));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSpawnsWithFilters($arrFilters=array(), $orderby=self::FIELD_SPAWNNUMBER, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
}
