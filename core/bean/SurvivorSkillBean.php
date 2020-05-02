<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorSkillBean
 * @author Hugues.
 * @since 1.05.02
 * @version 1.05.02
 */
class SurvivorSkillBean extends LocalBean
{

  /**
   * @param SurvivorSkill $SurvivorSkill
   */
  public function __construct($SurvivorSkill=null)
  {
    parent::__construct();
    $this->SurvivorSkill = ($SurvivorSkill==null ? new SurvivorSkill() : $SurvivorSkill);
  }

  public function getBadge($linked=false)
  {
    if ($linked) {
      $tag = self::TAG_A;
      $attributes = array(
        self::ATTR_CLASS => 'badge badge-'.$this->getColor().'-skill',
        self::ATTR_HREF  => '/page-competences/?skillId=43',
      );
    } else {
      $tag = self:: TAG_SPAN;
      $attributes = array(
        self::ATTR_CLASS => 'badge badge-'.$this->getColor().'-skill',
      );
    }
    return $this->getBalise($tag, $this->SurvivorSkill->getSkill()->getName(), $attributes);
  }

  public function getColor()
  {
    switch ($this->SurvivorSkill->getTagLevelId()) {
      case 20 :
        $color = 'yellow';
      break;
      case 30 :
      case 31 :
        $color = 'orange';
      break;
      case 40 :
      case 41 :
      case 42 :
        $color = 'red';
      break;
      default :
        $color = 'blue';
      break;
    }
    return $color;
  }
}


