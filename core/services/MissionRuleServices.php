<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionRuleServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.27
 */
class MissionRuleServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var MissionRuleDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new MissionRuleDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_MISSIONID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_RULEID));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_TITLE));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getMissionRulesWithFilters($arrFilters=array(), $orderby=self::FIELD_ID, $order=self::ORDER_ASC )
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
}
