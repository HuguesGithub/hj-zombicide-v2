<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostMissionBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.27
 */
class WpPostMissionBean extends WpPostBean
{
  protected $urlTemplate        = 'web/pages/public/wppage-mission.php';
  protected $urlTemplateExtract = 'web/pages/public/fragments/mission-article.php';
  protected $urlTemplateHome    = 'web/pages/public/fragments/mission-article-home.php';
  protected $urlImgBase         = '/wp-content/plugins/hj-zombicide/web/rsc/img/missions/';

  /**
   * Constructeur
   */
  public function __construct($missionId='')
  {
    parent::__construct();
    $this->MissionServices = new MissionServices();
    if ($missionId instanceof WpPost) {
      $this->WpPost = $missionId;
      $missionId = $this->WpPost->getPostMeta(self::FIELD_MISSIONID);
    }
    $this->Mission = $this->MissionServices->selectMission($missionId);
  }
  /**
   * @return string
   */
  public function getArticleHome()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Titre de la Mission - 1
      $this->WpPost->getPostTitle(),
      // Synopsis - 2
      $this->WpPost->getPostContent(),
      // Extensions nécessaires - 3
      $this->getStrExpansions(),
      // Dalles nécessaires - 4
      $this->getStrTiles(),
      // Classes additionnellets - 5
      ' col-12 col-md-6 col-xl-4',
      // Url de l'Article de la Mission - 6
      $this->WpPost->getPermalink(),
      // Url de l'img source de la map - 7
      $this->getThumbUrl(),
      // Url vers la page Missions - 7
      '/'.self::PAGE_MISSION,
      // Difficulté - 9
      $this->getLinkedDifficulty(),
      // Nb de Survivants - 10
      $this->getStrNbJoueurs(),
      // Durée - 11
      $this->getLinkedDuration(),
    );
    return $this->getRender($this->urlTemplateHome, $args);
  }

  public function getArticlePage()
  {
    ///////////////////////////////////////////////////////////////
    // Construction des listes d'obectifs et de règles.
    $contentRules  = $this->getMission()->getBean()->getMissionContentObjectives();
    $contentRules .= $this->getMission()->getBean()->getMissionContentRules();
    // On prépare l'affichage de la Map.
    $argsBalise = array(
      self::ATTR_SRC=>$this->urlImgBase.$this->getMission()->getCode().'-Missions.png',
      self::ATTR_ALT=>$this->WpPost->getPostTitle(),
    );
    $baliseImage = $this->getBalise(self::TAG_IMG, '', $argsBalise);

    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Code de la Mission - 1
      $this->getMission()->getCode(),
      // Titre de la Mission - 2
      $this->WpPost->getPostTitle(),
      // Retourne la chaîne de Difficulté, Nb de joueurs et Durée - 3
      $this->getLinkedDifPlaDur(),
      // Synopsis - 4
      $this->WpPost->getPostContent(),
      // Extensions nécessaires - 5
      $this->getStrExpansions(),
      // Dalles nécessaires - 6
      $this->getStrTiles(),
      // Listes des Objectifs et des Règles - 7
      $contentRules,
      // Image de la Map éventuelle - 8
      $baliseImage,
    );
    return $this->getRender($this->urlTemplateExtract, $args);
  }
  /**
   * @return string
   */
  public function getContentPage()
  {
    $args = array(
      // On affiche la Mission demandée - 1
      $this->getArticlePage(),
      // Liens de navigation - 2
      '',
      // Contenu additionnel en bas de page - 3
      '',
    );
    return $this->getRender($this->urlTemplate, $args);
  }

  public function getMission()
  { return $this->Mission; }

  private function getLinkedDifficulty()
  { return '<a href="/tag/'.strtolower($this->getStrDifficulty()).'">'.$this->getStrDifficulty().'</a>'; }
  private function getStrDifficulty()
  {
    $strLevel = $this->WpPost->getPostMeta(self::FIELD_LEVELID);
    if ($strLevel=='') {
      $strLevel = $this->Mission->getLevel()->getName();
    }
    return $strLevel;
  }
  private function getStrNbJoueurs()
  {
    $strPlayers = $this->WpPost->getPostMeta(self::FIELD_PLAYERID);
    if ($strPlayers=='') {
      $strPlayers = (self::isAdmin() ? 'Wip Nb' : '');
    }
    return $strPlayers.' Survivants';
  }
  private function getLinkedDuration()
  { return '<a href="/tag/'.strtolower(str_replace(' ', '-', $this->getStrDuree())).'">'.$this->getStrDuree().'</a>'; }
  private function getStrDuree()
  {
    $strDuree = $this->WpPost->getPostMeta(self::FIELD_DURATIONID);
    if ($strDuree=='') {
      $strDuree = $this->getMission()->getDuration()->getStrDuree();
    } else {
      $strDuree .= ' minutes';
    }
    return $strDuree;
  }
  private function getStrTiles()
  {
    $strTileIds = $this->WpPost->getPostMeta('tileIds');
    if ($strTileIds=='') {
      $strTileIds = (self::isAdmin() ? 'Wip Tiles' : '');
    }
    return $strTileIds;
  }
  private function getStrExpansions()
  {
    $expansionIds = unserialize($this->WpPost->getPostMeta('expansionIds'));
    if ($expansionIds=='') {
      if (self::isAdmin()) {
        $strReturned = 'Wip Dalles';
      } else {
        $strReturned = '';
      }
    } else {
      $strReturned = implode(', ', $expansionIds);
    }
    return $strReturned;
  }
  private function getThumbUrl()
  {
    $thumbId = $this->WpPost->getPostMeta('map');
    $WpPost = get_post($thumbId);
    return $WpPost->guid;
  }
  private function getLinkedDifPlaDur()
  { return $this->getLinkedDifficulty().' / '.$this->getStrNbJoueurs().' / '.$this->getLinkedDuration(); }























  /**
   * @param string $isHome
   * @return string
   */
  public function displayThumbWpPost($isHome=false)
  {
    $Mission = $this->getMission();
    $args = array(
      // Url de la Mission - 1
      $Mission->getWpPostUrl(),
      // Code et Nom de la Mission - 2
      $Mission->getCode().' - '.$Mission->getTitle(),
      // Difficulté de la Mission - 3
      $Mission->getStrDifficulty(),
      // Nombre de joueurs de la Mission - 4
      $Mission->getStrNbJoueurs(),
      // Durée de la Mission - 5
      $Mission->getStrDuree(),
      // Extensions de la Mission - 6
      $Mission->getStrExpansions(),
   );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/public/fragments/article-mission-thumb.php');
    return vsprintf($str, $args);
  }























  /**
   * @param string $isHome
   * @return string
   */
  public function displayWpPost($isHome=false)
  {
    if ($isHome) {
      return $this->getArticleHome();
    } else {
      return 'WIP WpPostMissionBean displayWpPost';
    }
  }
}
