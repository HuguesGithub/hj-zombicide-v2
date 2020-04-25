<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentWeaponProfile
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class EquipmentWeaponProfile extends LocalDomain
{
  /**
   * Id technique de la jointure
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de la carte Equipement
   * @var int $equipmentCardId
   */
  protected $equipmentCardId;
  /**
   * Id technique du profil de l'arme
   * @var int $weaponProfileId
   */
  protected $weaponProfileId;
  /**
   * L'arme est-elle bruyante
   * @var int $noisy
   */
  protected $noisy;
  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes, $services);
    $this->WeaponProfileServices = new WeaponProfileServices();
  }
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @ return int
   */
  public function getEquipmentCardId()
  { return $this->equipmentCardId; }
  /**
   * @ return int
   */
  public function getWeaponProfileId()
  { return $this->weaponProfileId; }
  /**
   * @return int
   */
  public function isNoisy()
  { return $this->noisy; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $equipmentCardId
   */
  public function setEquipmentCardId($equipmentCardId)
  { $this->equipmentCardId = $equipmentCardId; }
  /**
   * @param int $weaponProfileId
   */
  public function setWeaponProfileId($weaponProfileId)
  { $this->weaponProfileId = $weaponProfileId; }
  /**
   * @param int $noisy
   */
  public function setNoisy($noisy)
  { $this->noisy = $noisy; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('EquipmentWeaponProfile'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return EquipmentWeaponProfile
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new EquipmentWeaponProfile(), self::getClassVars(), $row); }
  /**
   * @return WeaponProfile
   */
  public function getWeaponProfile()
  {
    if ($this->WeaponProfile==null) {
      $this->WeaponProfile = $this->WeaponProfileServices->select(__FILE__, __LINE__, $this->weaponProfileId);
    }
    return $this->WeaponProfile;
  }
}
