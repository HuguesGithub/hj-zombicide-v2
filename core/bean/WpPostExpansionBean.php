<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostExpansionBean
 * @author Hugues
 * @since 1.07.21
 * @version 1.07.21
 */
class WpPostExpansionBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-expansion.php';

  /**
   * Class Constructor
   */
  public function __construct($WpPost)
  {
    parent::__construct();
    $this->ExpansionServices = new ExpansionServices();
    $this->WpPost = $WpPost;
    $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$code));
    $this->Expansion = (!empty($Expansions) ? array_shift($Expansions) : new Expansion());

  }
  /**
   * On retourne la page dédiée à la compétence.
   * @return string
   */
  public function getContentPage()
  {
    /*
    //////////////////////////////////////////////////////////////////
    // On enrichi les tableaux de données nécessaires.
    $arrF = array(self::FIELD_SKILLID => $this->Skill->getId());
    $arrTags = array(
      self::COLOR_BLUE   => array(10, 11),
      self::COLOR_YELLOW => array(20),
      self::COLOR_ORANGE => array(30, 31),
      self::COLOR_RED    => array(40, 41, 42),
    );
    //////////////////////////////////////////////////////////////////
    // On construit le tableau nécessaire au listing des Survivants
    foreach ($arrTags as $key => $value) {
      while (!empty($value)) {
        $val = array_shift($value);
        $arrF[self::FIELD_TAGLEVELID] = $val;
        foreach ($this->arrLvls as $k => $v) {
          $arrF[self::FIELD_SURVIVORTYPEID] = $k;
          $SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters($arrF);
          foreach ($SurvivorSkills as $SurvivorSkill) {
            $Survivor = $SurvivorSkill->getSurvivor();
            $this->skills[$key][$k][$Survivor->getNiceName()] = $Survivor;
          }
          if (!empty($this->skills[$key][$k])) {
            ksort($this->skills[$key][$k]);
          }
        }
      }
    }

    */
    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Nom de l'Extension - 1
      $this->Expansion->getName(),
      // Description de la Compétence - 2
      $this->WpPost->getPostContent(),
      // Lien de navigation - 3
      '',//$this->getNavLinks(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }

}
