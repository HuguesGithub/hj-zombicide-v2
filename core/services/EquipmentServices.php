<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentServices
 * @author Hugues.
 * @since 1.04.15
 * @version 1.04.15
 */
class EquipmentServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var EquipmentDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new EquipmentDaoImpl();
  }

  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getEquipmentsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }

  public function selectEquipment($equipmentId)
  { return $this->select(__FILE__, __LINE__, $equipmentId); }
}
