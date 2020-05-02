<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostSkillBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.01
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
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  public function buildSkillUls($argsRank, $color)
  {
    $tplUl = '<ul class="col-3"><li>%1$s :</li>%2$s</ul>';
    $strReturned = '';
    foreach ($argsRank as $rank) {
      $strReturned .= vsprintf($tplUl, array($this->arrLvls[$rank], $this->buildSkillLis($this->skills[$color][$rank], $color)));
    }
    return $strReturned;
  }
  /**
   * Retourne la liste des Survivants ayant une compétence à ce niveau, dans des cartouches de couleur.
   * @param array $Survivors Une liste des Survivants ayant cette compétence
   * @param string $color Permet de colorer le cartouche
   * @return string
   */
  public function buildSkillLis($Survivors, $color)
  {
    $strLis = '';
    if (!empty($Survivors)) {
      ksort($Survivors);
      while (!empty($Survivors)) {
        $Survivor = array_shift($Survivors);
        $strLis .= $Survivor->getBean()->getSkillBadge($color);
      }
    }
    return $strLis;
  }
  public function buildSkillBadges($rank, $arrTags)
  {
    $strReturned = '';
    foreach ($arrTags as $key=>$value) {
      $Survivors = $this->skills[$key][$rank];
      if (!empty($Survivors)) {
        ksort($Survivors);
        while (!empty($Survivors)) {
          $Survivor = array_shift($Survivors);
          $strReturned .= $Survivor->getBean()->getSkillBadge($key);
        }
      }
    }
    return $strReturned;
  }
}
