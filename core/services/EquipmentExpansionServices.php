<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentExpansionServices
 * @author Hugues.
 * @since 1.04.15
 * @version 1.04.15
 */
class EquipmentExpansionServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var EquipmentExpansionDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new EquipmentExpansionDaoImpl();
  }

  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters['equipmentCardId']) ? $arrFilters['equipmentCardId'] : '%');
    $arrParams[] = (isset($arrFilters[self::FIELD_EXPANSIONID]) ? $arrFilters[self::FIELD_EXPANSIONID] : '%');
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getEquipmentExpansionsWithFilters($arrFilters=array(), $orderby=self::FIELD_ID, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters($file, $line, $arrParams);
  }
}
