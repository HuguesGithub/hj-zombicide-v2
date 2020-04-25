<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WeaponProfile
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class WeaponProfile extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Quelle portée minimale
   * @var int $minRange
   */
  protected $minRange;
  /**
   * Quelle portée maximale
   * @var int $maxRange
   */
  protected $maxRange;
  /**
   * Combien de dés
   * @var int $nbDice
   */
  protected $nbDice;
  /**
   * Quel seuil de réussite
   * @var int $successRate
   */
  protected $successRate;
  /**
   * Combien de dégâts
   * @var int $damageLevel
   */
  protected $damageLevel;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return int
   */
  public function getMinRange()
  { return $this->minRange; }
  /**
   * @return int
   */
  public function getMaxRange()
  { return $this->maxRange; }
  /**
   * @return int
   */
  public function getNbDice()
  { return $this->nbDice; }
  /**
   * @return int
   */
  public function getSuccessRate()
  { return $this->successRate; }
  /**
   * @return int
   */
  public function getDamageLevel()
  { return $this->damageLevel; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id = $id; }
  /**
   * @param int $minRange
   */
  public function setMinRange($minRange)
  { $this->minRange = $minRange; }
  /**
   * @param int $maxRange
   */
  public function setMaxRange($maxRange)
  { $this->maxRange = $maxRange; }
  /**
   * @param int $nbDice
   */
  public function setNbDice($nbDice)
  { $this->nbDice = $nbDice; }
  /**
   * @param int $successRate
   */
  public function setSuccessRate($successRate)
  { $this->successRate = $successRate; }
  /**
   * @param int $damageLevel
   */
  public function setDamageLevel($damageLevel)
  { $this->damageLevel = $damageLevel; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('WeaponProfile'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return WeaponProfile
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new WeaponProfile(), self::getClassVars(), $row); }
  /**
   * @return WeaponProfileBean
   */
  public function getBean()
  { return new WeaponProfileBean($this); }
}
