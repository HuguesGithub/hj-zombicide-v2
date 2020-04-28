<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Spawn
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class Spawn extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Id de l'extension
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * Numéro de la carte
   * @var int $spawnNumber
   */
  protected $spawnNumber;
  /**
   * Id technique du type de Spawn
   * @var int $spawnTypeId
   */
  protected $spawnTypeId;
  /**
   * Id technique de catégorie du Zombie
   * @var int $zombieCategoryId
   */
  protected $zombieCategoryId;
  /**
   * Type de Zombie Bleu
   * @var int $blueZombieTypeId
   */
  protected $blueZombieTypeId;
  /**
   * Nombre de Zombies apparaissant en Blue
   * @var int $blueQuantity
   */
  protected $blueQuantity;
  /**
   * Type de Zombie Yellow
   * @var int $yellowZombieTypeId
   */
  protected $yellowZombieTypeId;
  /**
   * Nombre de Zombies apparaissant en Blue
   * @var int $yellowQuantity
   */
  protected $yellowQuantity;
  /**
   * Type de Zombie Yellow
   * @var int $orangeZombieTypeId
   */
  protected $orangeZombieTypeId;
  /**
   * Nombre de Zombies apparaissant en Orange
   * @var int $orangeQuantity
   */
  protected $orangeQuantity;
  /**
   * Type de Zombie Red
   * @var int $redZombieTypeId
   */
  protected $redZombieTypeId;
  /**
   * Nombre de Zombies apparaissant en Red
   * @var int $redQuantity
   */
  protected $redQuantity;
  /**
   * @return $id
   */
  public function getId()
  {return $this->id; }
  /**
   * @return $expansionId
   */
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @return $spawnNumber
   */
  public function getSpawnNumber()
  { return $this->spawnNumber; }
  /**
   * @return $spawnTypeId
   */
  public function getSpawnTypeId()
  { return $this->spawnTypeId; }
  /**
   * @return $zombieCategoryId
   */
  public function getZombieCategoryId()
  { return $this->zombieCategoryId; }
  /**
   * @return $blueZombieTypeId
   */
  public function getBlueZombieTypeId()
  { return $this->blueZombieTypeId; }
  /**
   * @return $blueQuantity
   */
  public function getBlueQuantity()
  { return $this->blueQuantity; }
  /**
   * @return $yellowZombieTypeId
   */
  public function getYellowZombieTypeId()
  { return $this->yellowZombieTypeId; }
  /**
   * @return $yellowQuantity
   */
  public function getYellowQuantity()
  { return $this->yellowQuantity; }
  /**
   * @return $orangeZombieTypeId
   */
  public function getOrangeZombieTypeId()
  { return $this->orangeZombieTypeId; }
  /**
   * @return $orangeQuantity
   */
  public function getOrangeQuantity()
  { return $this->orangeQuantity; }
  /**
   * @return $redZombieTypeId
   */
  public function getRedZombieTypeId()
  { return $this->redZombieTypeId; }
  /**
   * @return $redQuantity
   */
  public function getRedQuantity()
  { return $this->redQuantity; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $expansionId
   */
  public function setExpansionId($expansionId)
  { $this->expansionId=$expansionId; }
  /**
   * @param int $spawnNumber
   */
  public function setSpawnNumber($spawnNumber)
  { $this->spawnNumber=$spawnNumber; }
  /**
   * @param int $spawnTypeId
   */
  public function setSpawnTypeId($spawnTypeId)
  { $this->spawnTypeId=$spawnTypeId; }
  /**
   * @param int $zombieCategoryId
   */
  public function setZombieCategoryId($zombieCategoryId)
  { $this->zombieCategoryId=$zombieCategoryId; }
  /**
   * @param int $blueZombieTypeId
   */
  public function setBlueZombieTypeId($blueZombieTypeId)
  { $this->blueZombieTypeId=$blueZombieTypeId; }
  /**
   * @param int $blueQuantity
   */
  public function setBlueQuantity($blueQuantity)
  { $this->blueQuantity=$blueQuantity; }
  /**
   * @param int $yellowZombieTypeId
   */
  public function setYellowZombieTypeId($yellowZombieTypeId)
  { $this->yellowZombieTypeId=$yellowZombieTypeId; }
  /**
   * @param int $yellowQuantity
   */
  public function setYellowQuantity($yellowQuantity)
  { $this->yellowQuantity=$yellowQuantity; }
  /**
   * @param int $orangeZombieTypeId
   */
  public function setOrangeZombieTypeId($orangeZombieTypeId)
  { $this->orangeZombieTypeId=$orangeZombieTypeId; }
  /**
   * @param int $orangeQuantity
   */
  public function setOrangeQuantity($orangeQuantity)
  { $this->orangeQuantity=$orangeQuantity; }
  /**
   * @param int $redZombieTypeId
   */
  public function setRedZombieTypeId($redZombieTypeId)
  { $this->redZombieTypeId=$redZombieTypeId; }
  /**
   * @param int $redQuantity
   */
  public function setRedQuantity($redQuantity)
  { $this->redQuantity=$redQuantity; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Spawn'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Spawn
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Spawn(), self::getClassVars(), $row); }
  /**
   * @return string
   */
  public function getImgUrl()
  { return '/wp-content/plugins/zombicide/web/rsc/images/spawns/'.(str_pad($this->spawnNumber, 4, '0', STR_PAD_LEFT)).'-thumb.jpg'; }

  public function getBean()
  { return new SpawnBean($this); }
}
