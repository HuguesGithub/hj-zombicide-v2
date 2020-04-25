<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Origine
 * @author Hugues.
 * @since 1.04.00
 * @version 1.04.00
 */
class Origine extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Nom de l'origine
   * @var string $name
   */
  protected $name;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return string
   */
  public function getName()
  { return $this->name; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $name
   */
  public function setName($name)
  { $this->name=$name; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Origine'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Origine
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Origine(), self::getClassVars(), $row); }
  /**
   * @return OrigineBean
   */
  public function getBean()
  { return new OrigineBean($this); }
}
