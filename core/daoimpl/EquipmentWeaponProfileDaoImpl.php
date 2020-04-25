<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentWeaponProfileDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class EquipmentWeaponProfileDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('EquipementWeaponProfile'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = EquipmentWeaponProfile::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|EquipmentWeaponProfile
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new EquipmentWeaponProfile()); }
}
