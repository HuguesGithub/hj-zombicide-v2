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
  protected $urlImgBase         = '/wp-content/plugins/hj-zombicide/web/rsc/img/missions/';



  protected $urlTemplateExtract = 'web/pages/public/fragments/mission-article.php';

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
      '',//$this->getArticlePage(),
      // Liens de navigation - 2
      '',
      // Contenu additionnel en bas de page - 3
      '',
    );
    return $this->getRender($this->urlTemplate, $args);
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
      $this->Mission->getCode(),
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

  public function getMission()
  { return $this->Mission; }

  private function getLinkedDifPlaDur()
  { return $this->getLinkedDifficulty().' / '.$this->getStrNbJoueurs().' / '.$this->getLinkedDuration(); }












































}
