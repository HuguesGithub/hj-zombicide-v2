<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorSkill
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class SurvivorSkill extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Id technique du Survivant
   * @var int $survivorId
   */
  protected $survivorId;
  /**
   * Id technique du Skill
   * @var int $skillId
   */
  protected $skillId;
  /**
   * Id technique du type
   * @var int $survivorTypeId
   */
  protected $survivorTypeId;
  /**
   * Rang de la compétence sur le profil
   * @var int $tagLevelId
   */
  protected $tagLevelId;

  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->SkillServices    = new SkillServices();
    $this->SurvivorServices = new SurvivorServices();
  }

  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return int
   */
  public function getSurvivorId()
  { return $this->survivorId; }
  /**
   * @return int
   */
  public function getSkillId()
  { return $this->skillId; }
  /**
   * @return int
   */
  public function getSurvivorTypeId()
  { return $this->survivorTypeId; }
  /**
   * @return int
   */
  public function getTagLevelId()
  { return $this->tagLevelId; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $survivorId
   */
  public function setSurvivorId($survivorId)
  { $this->survivorId=$survivorId; }
  /**
   * @param int $skillId
   */
  public function setSkillId($skillId)
  { $this->skillId=$skillId; }
  /**
   * @param int $survivorTypeId
   */
  public function setSurvivorTypeId($survivorTypeId)
  { $this->survivorTypeId=$survivorTypeId; }
  /**
   * @param int $tagLevelId
   */
  public function setTagLevelId($tagLevelId)
  { $this->tagLevelId=$tagLevelId; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('SurvivorSkill'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return SurvivorSkill
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new SurvivorSkill(), self::getClassVars(), $row); }
  /**
   * @return Skill
   */
  public function getSkill()
  {
    if ($this->Skill == null) {
      $this->Skill = $this->SkillServices->selectSkill($this->skillId);
    }
    return $this->Skill;
  }
  /**
   * @return string
   */
  public function getSkillName()
  { return $this->getSkill()->getName(); }
  public function getSurvivor()
  {
    if ($this->Survivor == null) {
      $this->Survivor = $this->SurvivorServices->selectSurvivor($this->survivorId);
    }
    return $this->Survivor;
  }
}
