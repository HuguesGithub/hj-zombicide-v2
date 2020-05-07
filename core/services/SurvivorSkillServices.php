<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorSkillServices
 * @author Hugues.
 * @since 1.04.27
 * @version 1.05.07
 */
class SurvivorSkillServices extends LocalServices
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
    $this->Dao = new SurvivorSkillDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_SURVIVORID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_SKILLID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_SURVIVORTYPEID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_TAGLEVELID));
  }
  /**
   * @param string $file
   * @param string $line
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSurvivorSkillsWithFilters($arrFilters=array(), $orderby=null, $order=array(self::ORDER_ASC, self::ORDER_ASC))
  {
    if ($orderby==null) {
      $orderby = array(self::FIELD_SURVIVORTYPEID, self::FIELD_TAGLEVELID);
    }
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }

  public function deleteBulkSurvivorSkill($arrFilters)
  {
    $this->buildFilters($arrFilters);
    return $this->Dao->deleteBulkEntriesWithFilters($this->arrParams);
  }

  public function insertSurvivorSkill($SurvivorSkill)
  { $this->insert(__FILE__, __LINE__, $SurvivorSkill); }
}
