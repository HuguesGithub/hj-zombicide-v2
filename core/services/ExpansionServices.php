<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionServices
 * @author Hugues.
 * @since 1.04.00
 * @version 1.07.21
 */
class ExpansionServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var ExpansionDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new ExpansionDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayWideFilter($arrFilters, self::FIELD_NAME));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_NBMISSIONS, 0));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_NBSURVIVANTS, 0));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getExpansionsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param int $id
   * @return Expansion
   */
  public function selectExpansion($id)
  { return $this->select(__FILE__, __LINE__, $id); }
  public function updateExpansion($Expansion)
  { $this->update(__FILE__, __LINE__, $Expansion); }
  public function insertExpansion($Expansion)
  { return $this->insert(__FILE__, __LINE__, $Expansion); }

}
