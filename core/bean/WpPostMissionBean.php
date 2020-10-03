<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostMissionBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.07.25
 */
class WpPostMissionBean extends WpPostBean
{
  protected $urlTemplate        = 'web/pages/public/wppage-mission.php';
  protected $urlImgBase         = '/wp-content/plugins/hj-zombicide/web/rsc/img/missions/';
  protected $urlTemplateArticle = 'web/pages/public/fragments/mission-article.php';




  /**
   * Constructeur
   */
  public function __construct($missionId='')
  {
    parent::__construct();
    $this->MissionServices = new MissionServices();
    if ($missionId instanceof WpPost) {
      $this->WpPost = $missionId;
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      $Missions = $this->MissionServices->getMissionsWithFilters(array(self::FIELD_CODE=>$code));
      $this->Mission = array_shift($Missions);
    } else {
      $this->Mission = $this->MissionServices->selectMission($missionId);
    }
  }
  /**
   * @return string
   */
  public function displayWpPost()
  { return $this->Mission->getBean()->getContentForHome(); }
  /**
   * @return string
   */
  public function getContentPage()
  {
    //////////////////////////////////////////////////////////////////
    // On enrichit le template puis on le restitue.
    $args = array(
      // On affiche la Mission demandée - 1
      $this->getArticlePage(true),
      // Liens de navigation - 2
      $this->getNavLinks(),
      // Contenu additionnel en bas de page - 3
      '',
    );
    return $this->getRender($this->urlTemplate, $args);
  }

  public function getArticlePage($isWhole=false)
  {
    if ($isWhole) {
      $classExtra = 'wholeArticle';
      $imgExt = '-Missions.png';
    } else {
      $classExtra = '';
      $imgExt = '-Thumb.png';
    }

    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Titre de la Mission - 1
      '['.$this->getMission()->getCode().'] - '.$this->WpPost->getPostTitle(),
      // Synopsis - 2
      $this->WpPost->getPostContent(),
      // Extensions nécessaires - 3
      $this->getMission()->getBean()->getStrExpansions(),
      // Dalles nécessaires - 4
      $this->getMission()->getBean()->getStrTiles(),
      // Classe supplémentaire - 5
      $classExtra,
      // Url de la Mission... - 6
      $this->WpPost->getPermalink(),
      // Image de la Map éventuelle - 7
      $this->urlImgBase.$this->getMission()->getCode().$imgExt,
      // Lien vers la page des Missions - 8
      '/'.self::PAGE_MISSION,
      // Difficulté - 9
      $this->getMission()->getBean()->getLinkedDifficulty(),
      // Nb Survivants - 10
      $this->getMission()->getBean()->getStrNbJoueurs(),
      // Durée - 11
      $this->getMission()->getBean()->getLinkedDuration(),
      // Liste des Objectifs - 12
      $this->getMission()->getBean()->getMissionContentObjectives(),
      // Liste des Règles - 13
      $this->getMission()->getBean()->getMissionContentRules(),
    );
    return $this->getRender($this->urlTemplateArticle, $args);
  }

  private function getNavLinks()
  {
    //////////////////////////////////////////////////////////////////
    // On construit les liens de navigation
    // On récupère toutes les missions, classées par ordre alphabétique.
    $Missions = $this->MissionServices->getMissionsWithFilters(array(), self::FIELD_CODE);
    $firstMission = null;
    while (!empty($Missions)) {
      $Mission = array_shift($Missions);
      // On les parcourt jusqu'à trouver la courante.
      if ($Mission->getId()==$this->Mission->getId()) {
        break;
      }
      if ($firstMission==null) {
        $firstMission = $Mission;
      }
      $prevMission = $Mission;
    }
    $nextMission = array_shift($Missions);
    if (empty($prevMission)) {
      $prevMission = array_pop($Missions);
    }
    if (empty($nextMission)) {
      $nextMission = $firstMission;
    }

    $nav = '';
    // On exploite la précédente et la suivante.
    if (!empty($prevMission)) {
      $attributes = array(self::ATTR_HREF=>$prevMission->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, '&laquo; '.$prevMission->getWpPost()->getPostTitle(), $attributes);
    }
    if (!empty($nextMission)) {
      $attributes = array(self::ATTR_HREF=>$nextMission->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, $nextMission->getWpPost()->getPostTitle().' &raquo;', $attributes);
    }
    return $nav;
  }

  public function getMission()
  { return $this->Mission; }


}
