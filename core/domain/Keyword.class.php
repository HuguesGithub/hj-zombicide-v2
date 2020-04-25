<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Keyword
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class Keyword extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Nom du keyword
   * @var string $name
   */
  protected $name;
  /**
   * Description du keyword
   * @var string $description
   */
  protected $description;
  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @return string
   */
  public function getName()
  { return $this->name; }
  /**
   * @return string
   */
  public function getDescription()
  { return $this->description; }
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
   * @param string $description
   */
  public function setDescription($description)
  { $this->description=$description; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Keyword'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Keyword
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Keyword(), self::getClassVars(), $row); }
  /**
   * @return KeywordBean
   */
  public function getBean()
  { return new KeywordBean($this); }
}
