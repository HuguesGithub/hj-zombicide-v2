<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostSkillBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.20
 */
class WpPostSkillBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-skill.php';
  protected $arrLvls = array(1=>'S', 2=>'Z', 3=>'U', 4=>'UZ');

  /**
   * Class Constructor
   */
  public function __construct($WpPost)
  {
    parent::__construct();
    $this->SkillServices         = new SkillServices();
    $this->SurvivorServices      = new SurvivorServices();
    $this->SurvivorSkillServices = new SurvivorSkillServices();
    // TODO : Quand toutes les compétences auront leur article, on peut virer ce test et l'exécution secondaire.
    if ($WpPost instanceof WpPost) {
      $this->WpPost = $WpPost;
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      $Skills = $this->SkillServices->getSkillsWithFilters(array(self::FIELD_CODE=>$code));
      $this->Skill = (!empty($Skills) ? array_shift($Skills) : new Skill());
    } else {
      $this->Skill = $this->SkillServices->selectSkill($WpPost);
    }
  }
  /**
   * On retourne la page dédiée à la compétence.
   * @return string
   */
  public function getContentPage()
  {
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

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Nom de la Compétence - 1
      $this->Skill->getName(),
      // Description de la Compétence - 2
      $this->Skill->getDescription(),
      // Liste des Survivants ayant la compétence en Bleu (Zombivant et Ultimate compris) - 3
      $this->buildSkillBadges(1, $arrTags),
      // Liste des Survivants ayant la compétence en Jaune (Zombivant et Ultimate compris) - 4
      $this->buildSkillBadges(2, $arrTags),
      // Liste des Survivants ayant la compétence en Orange (Zombivant et Ultimate compris) - 5
      $this->buildSkillBadges(3, $arrTags),
      // Liste des Survivants ayant la compétence en Rouge (Zombivant et Ultimate compris) - 6
      $this->buildSkillBadges(4, $arrTags),
      // Lien de navigation - 7
      $this->getNavLinks(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getNavLinks()
  {
    //////////////////////////////////////////////////////////////////
    // On construit les liens de navigation
    // On récupère toutes les compétences, classées par ordre alphabétique.
    $Skills = $this->SkillServices->getSkillsWithFilters();
    $firstSkill = null;
    while (!empty($Skills)) {
      $Skill = array_shift($Skills);
      // On les parcourt jusqu'à trouver la courante.
      if ($Skill->getId()==$this->Skill->getId()) {
        break;
      }
      if ($firstSkill==null) {
        $firstSkill = $Skill;
      }
      $prevSkill = $Skill;
    }
    $nextSkill = array_shift($Skills);
    if (empty($prevSkill)) {
      $prevSkill = array_pop($Skills);
    }
    if (empty($nextSkill)) {
      $nextSkill = $firstSkill;
    }

    $nav = '';
    // On exploite la précédente et la suivante.
    if (!empty($prevSkill)) {
      $attributes = array(self::ATTR_HREF=>$prevSkill->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, '&laquo; '.$prevSkill->getWpPost()->getPostTitle(), $attributes);
    }
    if (!empty($nextSkill)) {
      $attributes = array(self::ATTR_HREF=>$nextSkill->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, $nextSkill->getWpPost()->getPostTitle().' &raquo;', $attributes);
    }
    return $nav;
  }
  private function buildSkillBadges($rank, $arrTags)
  {
    $strReturned = '';
    foreach ($arrTags as $key=>$value) {
      $cartoucheAttributes = array(self::ATTR_CLASS=>'cartouche badge badge-'.$key.'-skill');
      $Survivors = $this->skills[$key][$rank];
      if (!empty($Survivors)) {
        ksort($Survivors);
        while (!empty($Survivors)) {
          $Survivor = array_shift($Survivors);
          $strReturned .= $Survivor->getBean()->getCartouche($cartoucheAttributes, true);
        }
        $strReturned .= '<br>';
      }
    }
    return $strReturned;
  }
}
