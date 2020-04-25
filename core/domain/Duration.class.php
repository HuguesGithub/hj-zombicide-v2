<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Duration
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class Duration extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Durée minimale estimée
   * @var int $minDuration
   */
  protected $minDuration;
  /**
   * Durée maximal estimée (nulle si une seule durée donnée)
   * @var int $maxDuration
   */
  protected $maxDuration;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return int
   */
  public function getMinDuration()
  { return $this->minDuration; }
  /**
   * @return int
   */
  public function getMaxDuration()
  { return $this->maxDuration; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id = $id; }
  /**
   * @param int $minDuration
   */
  public function setMinDuration($minDuration)
  { $this->minDuration = $minDuration; }
  /**
   * @param int $maxDuration
   */
  public function setMaxDuration($maxDuration)
  { $this->maxDuration = $maxDuration; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Duration'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Duration
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Duration(), self::getClassVars(), $row); }
  /**
   * @return string
   */
  public function getStrDuree()
  { return $this->minDuration.($this->maxDuration == 0 ? '' : ' à '.$this->maxDuration).' minutes'; }
  /**
   * @return DurationBean
   */
  public function getBean()
  { return new DurationBean($this); }
}
