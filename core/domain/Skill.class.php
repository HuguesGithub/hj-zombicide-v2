<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Skill
 * @author Hugues.
 * @since 1.00.00
 * @version 1.04.26
 */
class Skill extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Code de la donnée
   * @var string $code
   */
  protected $code;
  /**
   * Nom de la donnée
   * @var string $name
   */
  protected $name;
  /**
   * Description de la donnée
   * @var string $description
   */
  protected $description;
  /**
   * La donnée est-elle officielle ?
   * @var int $official
   */
  protected $official;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return string
   */
  public function getCode()
  { return $this->code; }
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
   * @return int
   */
  public function isOfficial()
  { return $this->official; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $code
   */
  public function setCode($code)
  { $this->code=$code; }
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
   * @param int $official
   */
  public function setOfficial($official)
  { $this->official = $official; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Skill'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Skill
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Skill(), self::getClassVars(), $row); }
  /**
   * @return string
   */
  public function getWpPostUrl()
  { return '/page-competences/?skillId='.$this->id; }
  /**
   * @return SkillBean
   */
  public function getBean()
  { return new SkillBean($this); }
}
