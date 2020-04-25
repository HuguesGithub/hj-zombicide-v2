<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorSkillServices
 * @author Hugues.
 * @version 1.02.00
 * @since 1.0.00
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

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters[self::FIELD_SURVIVORID]) ? $arrFilters[self::FIELD_SURVIVORID] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_SKILLID]) ? $arrFilters[self::FIELD_SKILLID] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_SURVIVORTYPEID]) ? $arrFilters[self::FIELD_SURVIVORTYPEID] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_TAGLEVELID]) ? $arrFilters[self::FIELD_TAGLEVELID] : '%');
    return $arrParams;
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
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }
}
