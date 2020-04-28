<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorServices
 * @author Hugues.
 * @since 1.04.27
 * @version 1.04.28
 */
class SurvivorServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var SurvivorDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new SurvivorDaoImpl();
  }
  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayWideFilter($arrFilters, self::FIELD_NAME));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_ZOMBIVOR));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayWideFilter($arrFilters, self::FIELD_ULTIMATE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_EXPANSIONID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_BACKGROUND));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_LIVEABLE));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSurvivorsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    if (isset($arrFilters[self::FIELD_EXPANSIONID]) && strpos($arrFilters[self::FIELD_EXPANSIONID], ',')!==false) {
      return $this->Dao->selectEntriesInExpansions($this->arrParams);
    } else {
      return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
    }
  }

  public function selectSurvivor($id)
  { return $this->select(__FILE__, __LINE__, $id); }
}
