<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostSurvivorBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.07
 */
class WpPostSurvivorBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-survivor.php';
  protected $arrLvls = array(1=>'S', 2=>'Z', 3=>'U', 4=>'UZ');

  /**
   * Class Constructor
   */
  public function __construct($WpPost)
  {
    parent::__construct();
    $this->SurvivorServices      = new SurvivorServices();
    /*
    $this->SkillServices         = new SkillServices();
    $this->SurvivorSkillServices = new SurvivorSkillServices();
    */
    $this->WpPost = $WpPost;
    $postMetas = $this->WpPost->getPostMetas();
    $survivorId = $postMetas[self::FIELD_SURVIVORID][0];
    $this->Survivor = $this->SurvivorServices->selectSurvivor($survivorId);
  }
  /**
   * @return Survivor
   */
  public function getSurvivor()
  { return $this->Survivor; }
  /**
   * @return string
   */
  public function displayWpPost()
  { return $this->Survivor->getBean()->getContentForHome(); }
  /**
   * On retourne la page dédiée au Survivant.
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
          if (!empty($skills[$key][$k])) {
            ksort($this->skills[$key][$k]);
          }
        }
      }
    }
    */
    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Tous les portraits existants - 1
      $this->Survivor->getBean()->getAllPortraits(),
      // Nom du Survivant - 2
      $this->Survivor->getName(),
      // Background du Survivant - 3
      $this->Survivor->getBackground(),
      // Cases à cocher éventuelles pour afficher les différents profils - 4
      $this->Survivor->getBean()->getCheckBoxType(),
      // Liste des compétences du Survivant - 5
      $this->Survivor->getBean()->getAllSkills(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  /*
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
   *
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
  * */
}
