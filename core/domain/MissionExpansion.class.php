<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionExpansion
 * @author Hugues.
 * @since 1.04.27
 * @version 1.05.02
 */
class MissionExpansion extends LocalDomain
{
  /**
   * Id technique de la jointure
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de la Mission
   * @var int $missionId
   */
  protected $missionId;
  /**
   * Id technique de l'Expansion
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @ return int
   */
  public function getMissionId()
  { return $this->MissionId; }
  /**
   * @ return int
   */
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $missionId
   */
  public function setMissionId($missionId)
  { $this->missionId = $missionId; }
  /**
   * @param int $expansionId
   */
  public function setExpansionId($expansionId)
  { $this->expansionId = $expansionId; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('MissionExpansion'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return MissionExpansion
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new MissionExpansion(), self::getClassVars(), $row); }
  /**
   * @return array EquipmentExpansion
   */
  public function getEquipmentExpansions()
  {
    if ($this->EquipmentExpansions == null) {
      $arrFilters = array(self::FIELD_EXPANSIONID=>$this->expansionId);
      $this->EquipmentExpansions = $this->EquipmentExpansionServices->getEquipmentExpansionsWithFilters($arrFilters);
    }
    return $this->EquipmentExpansions;
  }
  /**
   * @param Expansion $Expansion
   */
  public function setExpansion($Expansion)
  { $this->Expansion=$Expansion; }

}
