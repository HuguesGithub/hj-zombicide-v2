<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentServices
 * @author Hugues.
 * @since 1.04.15
 * @version 1.04.27
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
   * @param int $equipmentId
   * @return Equipment
   */
  public function selectEquipment($equipmentId)
  { return $this->select(__FILE__, __LINE__, $equipmentId); }
}
