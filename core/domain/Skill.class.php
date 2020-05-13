<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Skill
 * @author Hugues.
 * @since 1.00.00
 * @version 1.05.12
 */
class Skill extends WpPostRelais
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
   * Extension de la compétence (première apparition ou derni_ère modification)
   * @var int $expansionId
   */
  protected $expansionId;

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
  public function getExpansionId()
  { return $this->expansionId; }
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
  public function setExpansionId($expansionId)
  { $this->expansionId = $expansionId; }

  ///////////////////////////////////////////////////////////////
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
   * @return SkillBean
   */
  public function getBean()
  { return new SkillBean($this); }
  ///////////////////////////////////////////////////////////////

  /**
   * @return string
   */
  public function getWpPost()
  { return $this->getMainWpPost(self::FIELD_CODE, $this->code, self::WP_CAT_SKILL_ID); }










  public function getExpansion()
  {
    if ($this->Expansion==null) {
      $this->Expansion = $this->ExpansionServices->selectExpansion($this->getExpansionId());
    }
    return $this->Expansion;
  }

}
