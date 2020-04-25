<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentExpansion
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class EquipmentExpansion extends LocalDomain
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
   * Id technique de l'Expansion
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * Nombre de cartes dans l'extension
   * @var int $quantity
   */
  protected $quantity;
  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->EquipmentServices          = new EquipmentServices();
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
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @ return int
   */
  public function getQuantity()
  { return $this->quantity; }
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
   * @param int $expansionId
   */
  public function setExpansionId($expansionId)
  { $this->expansionId = $expansionId; }
  /**
   * @param int $quantity
   */
  public function setQuantity($quantity)
  { $this->quantity = $quantity; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('EquipmentExpansion'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return EquipmentExpansion
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new EquipmentExpansion(), self::getClassVars(), $row); }
  /**
   * @param Equipment $Equipment
   */
  public function setEquipment($Equipment)
  { $this->Equipment = $Equipment; }
  
  /**
   * @return Equipment
   */
  public function getEquipment()
  {
    if ($this->Equipment == null) {
      $this->Equipment = $this->EquipmentServices->select(__FILE__, __LINE__, $this->equipmentCardId);
    }
    return $this->Equipment;
  }
  /**
   * @param Skill $Skill
   * @return array
   */
  public static function getFromStartingSkill($Skill)
  {
    $EquipmentExpansions = array();
    $EquipmentExpansionServices = new EquipmentExpansionServices();
    // Combien on va en récupérer ? 1 ou 2 selon les profils
    $nb = 1;
    // Dans le cas où un Survivant commence avec un équipement de départ
    switch ($Skill->getCode()) {
      case 'STARTS_WITH_ARBALETE' :
        $args = array(self::CST_EQUIPMENTCARDID=>60);
      break;
      case 'STARTS_WITH_BASEBALL_BAT' :
        $args = array(self::CST_EQUIPMENTCARDID=>3);
      break;
      case 'STARTS_WITH_CHAINSAW' :
        $args = array(self::CST_EQUIPMENTCARDID=>29);
      break;
      case 'STARTS_WITH_FLAMETHROWER' :
        $args = array(self::CST_EQUIPMENTCARDID=>38);
      break;
      case 'STARTS_WITH_FLASHLIGHT' :
        $args = array(self::CST_EQUIPMENTCARDID=>15);
      break;
      case 'STARTS_WITH_2_KATANAS' :
        $nb = 2;
      case 'STARTS_WITH_KATANA' :
        $args = array(self::CST_EQUIPMENTCARDID=>14);
      break;
      case 'STARTS_WITH_2_KUKRIS' :
        $args = array(self::CST_EQUIPMENTCARDID=>52);
        $nb = 2;
      break;
      case 'STARTS_WITH_2_MACHETES' :
        $args = array(self::CST_EQUIPMENTCARDID=>17);
        $nb = 2;
      break;
      case 'STARTS_WITH_MAGNUM' :
        $args = array(self::CST_EQUIPMENTCARDID=>40);
      break;
      case 'STARTS_WITH_2_MOLOTOV' :
        $args = array(self::CST_EQUIPMENTCARDID=>19);
        $nb = 2;
      break;
      case 'STARTS_WITH_PISTOL' :
        $args = array(self::CST_EQUIPMENTCARDID=>24);
      break;
      case 'STARTS_WITH_SAWED_OFF' :
        $args = array(self::CST_EQUIPMENTCARDID=>10);
      break;
      case 'STARTS_WITH_RIFLE' :
        $args = array(self::CST_EQUIPMENTCARDID=>5);
      break;
      case 'STARTS_WITH_SHOTGUN' :
        $args = array(self::CST_EQUIPMENTCARDID=>11);
      break;
      case 'STARTS_WITH_SUBMG' :
        $args = array(self::CST_EQUIPMENTCARDID=>26);
      break;
      default :
        // Un cas qui n'aurait pas encore été prévu...
      break;
    }
    // On récupère tous les objets de ce type de la pioche et on mélange
    $EEs = $EquipmentExpansionServices->getEquipmentExpansionsWithFilters(__FILE__, __LINE__, $args);
    shuffle($EEs);
    // On prend autant que nécessaire et on le met de côté pour l'ajouter à l'équipement.
    for ($i=0; $i<$nb; $i++) {
      $EquipmentExpansion = array_shift($EEs);
      $EquipmentExpansion->setQuantity(1);
      array_push($EquipmentExpansions, $EquipmentExpansion);
    }
    return $EquipmentExpansions;
  }
}
