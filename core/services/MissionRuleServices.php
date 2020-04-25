<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionRuleServices
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.26
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

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    array_push($arrParams, (!empty($arrFilters[self::CST_MISSIONID]) ? $arrFilters[self::CST_MISSIONID] : '%'));
    array_push($arrParams, ($arrFilters['ruleId']!='' ? $arrFilters['ruleId'] : '%'));
    array_push($arrParams, (!empty($arrFilters[self::FIELD_TITLE]) ? $arrFilters[self::FIELD_TITLE] : '%'));
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
  public function getMissionRulesWithFilters($file, $line, $arrFilters=array(), $orderby='id', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters($file, $line, $arrParams);
  }
}
