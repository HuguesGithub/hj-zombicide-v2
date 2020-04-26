<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SkillServices
 * @author Hugues.
 * @since 1.00.00
 * @version 1.04.27
 */
class SkillServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var SkillDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new SkillDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayWideFilter($arrFilters, self::FIELD_NAME));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayWideFilter($arrFilters, self::FIELD_DESCRIPTION));
    array_push($this->arrParams[self::SQL_WHERE], $this->addFilter($arrFilters, self::FIELD_OFFICIAL));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSkillsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }

  public function insertSkill($Skill)
  { return $this->insert(__FILE__, __LINE__, $Skill); }
  public function selectSkill($id)
  { return $this->select(__FILE__, __LINE__, $id); }
  public function updateSkill($Skill)
  { return $this->update(__FILE__, __LINE__, $Skill); }
}
