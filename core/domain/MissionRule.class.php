
<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionRule
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
 */
class MissionRule extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de la Mission
   * @var int $missionId
   */
  protected $missionId;
  /**
   * Id technique de la Règle
   * @var int $ruleId
   */
  protected $ruleId;
  /**
   * titre de la règle
   * @var string $title
   */
  protected $title;
  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->MissionServices   = new MissionServices();
    $this->RuleServices      = new RuleServices();
  }
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return int
   */
  public function getMissionId()
  { return $this->missionId; }
  /**
   * @return int
   */
  public function getRuleId()
  { return $this->ruleId; }
  /**
   * @return string
   */
  public function getTitle()
  { return $this->title; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id = $id; }
  /**
   * @param int $missionId
   */
  public function setMissionId($missionId)
  { $this->missionId = $missionId; }
  /**
   * @param int $ruleId
   */
  public function setRuleId($ruleId)
  { $this->ruleId = $ruleId; }
  /**
   * @param string $title
   */
  public function setTitle($title)
  { $this->title = $title; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('MissionRule'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return MissionRule
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new MissionRule(), self::getClassVars(), $row); }
  /**
   * @return Rule
   */
  public function getRule()
  {
    if ($this->Rule==null) {
     $this->Rule = $this->getRuleFromGlobal($this->ruleId);
    }
    return $this->Rule;
  }
}
