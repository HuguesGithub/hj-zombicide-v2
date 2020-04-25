<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SkillServices
 * @author Hugues.
 * @version 1.02.00
 * @since 1.00.00
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

  private function buildFilters($arrF)
  {
    $arrParams = array();
    array_push($arrParams, (!empty($arrF[self::FIELD_CODE]) && !is_array($arrF[self::FIELD_CODE])) ? $arrF[self::FIELD_CODE] : '%');
    array_push($arrParams, (!empty($arrF[self::FIELD_NAME]) && !is_array($arrF[self::FIELD_NAME])) ? '%'.$arrF[self::FIELD_NAME].'%' : '%');
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_DESCRIPTION) ? '%'.$arrF[self::FIELD_DESCRIPTION].'%' : '%'));
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSkillsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

  public function selectSkill($id)
  { return $this->select(__FILE__, __LINE__, $id); }
}
