<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.07.21
 */
class MissionBean extends LocalBean
{
  protected $urlRowAdmin        = 'web/pages/admin/fragments/mission-row.php';
  protected $urlRowPublic       = 'web/pages/public/fragments/mission-row.php';
  protected $urlArticle         = 'web/pages/public/fragments/mission-article.php';
  /**
   * @param Mission $Mission
   */
  public function __construct($Mission=null)
  {
    parent::__construct();
    $this->Mission = ($Mission==null ? new Mission() : $Mission);
    $this->ExpansionServices = new ExpansionServices();
    $this->MissionServices   = new MissionServices();
    $this->WpPostServices    = new WpPostServices();
  }

  //////////////////////////////////////////////////////////////////////////
  // Différentes modes de présentation
  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    ///////////////////////////////////////////////////////////////
    // Les infos WpPost regroupées dans une cellule.
    $infosWpPost  = $this->Mission->getTitle().' - '.$this->Mission->getCode().'<br>';
    $infosWpPost .= $this->Mission->getStrDifPlaDur();

    /////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
      // Identifiant de la Mission - 1
      $this->Mission->getId(),
      // Les infos du WpPost associé - 2
      $infosWpPost,
      // Url d'édition du WpPost - 3
      $this->Mission->getWpPostEditUrl(),
      // Url d'édition de la BDD - 4
      $this->Mission->getEditUrl(self::CST_MISSION),
      // Url pulique de l'article en ligne - 5
      $this->Mission->getWpPostUrl(),
      // Dimensions de la map - 6
      $this->Mission->getHeight().'x'.$this->Mission->getWidth(),
      // Url de la Map - 7
      $this->Mission->getThumbUrl(),
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlRowAdmin, $args);
  }
  /**
   * @return string
   */
  public function getRowForPublicPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $urlWpPost = $this->Mission->getWpPostUrl();
    $args = array(
      // L'identifiant de la Mission - 1
      $this->Mission->getId(),
      // L'url pour accéder au détail de la Mission - 2
      $urlWpPost,
      // Le Titre de la Mission - 3
      '['.$this->Mission->getCode().'] - '.$this->Mission->getTitle(),
      // La Difficulté, le nombre de Survivants et la Durée de la Mission - 4
      $this->Mission->getStrDifPlaDur(),
      // La liste des Extensions nécessaires à la Mission - 5
      $this->Mission->getStrExpansions(),
      // L'origine de la publication originelle - 6
      $this->getStrOrigine(),
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlRowPublic, $args);
  }
  /**
   * @return string
   */
  public function getContentForHome()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichit le template et on le retourne.
    $args = array(
      // Titre de la Mission - 1
      $this->Mission->getWpPost()->getPostTitle(),
      // Synopsis - 2
      $this->Mission->getWpPost()->getPostContent(),
      // Extensions nécessaires - 3
      $this->getStrExpansions(),
      // Dalles nécessaires - 4
      $this->getStrTiles(),
      // Classes additionnellets - 5
      ' col-12 col-md-6 col-xl-4',
      // Url de l'Article de la Mission - 6
      $this->Mission->getWpPostUrl(),
      // Url de l'img source de la map - 7
      $this->Mission->getThumbUrl(),
      // Url vers la page Missions - 7
      '/'.self::PAGE_MISSION,
      // Difficulté - 9
      $this->getLinkedDifficulty(),
      // Nb de Survivants - 10
      $this->getStrNbJoueurs(),
      // Durée - 11
      $this->getLinkedDuration(),
      '','','','','','','','',
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlArticle, $args);
  }

  public function getStrExpansions()
  {
    $expansionIds = unserialize($this->Mission->getWpPost()->getPostMeta('expansionIds'));
    if ($expansionIds=='') {
      if (self::isAdmin()) {
        $strReturned = 'Wip Extensions';
      } else {
        $strReturned = '';
      }
    } else {
      $strReturned = implode(', ', $expansionIds);
    }
    return $strReturned;
  }
  public function getStrTiles()
  {
    $strTileIds = $this->Mission->getWpPost()->getPostMeta('tileIds');
    if ($strTileIds=='') {
      $strTileIds = (self::isAdmin() ? 'Wip Tiles' : '');
    }
    return $strTileIds;
  }
  public function getLinkedDifficulty()
  { return '<a href="/tag/'.strtolower($this->getStrDifficulty()).'">'.$this->getStrDifficulty().'</a>'; }
  private function getStrDifficulty()
  {
    $strLevel = $this->Mission->getWpPost()->getPostMeta(self::FIELD_LEVELID);
    if ($strLevel=='') {
      $strLevel = $this->Mission->getLevel()->getName();
    }
    return $strLevel;
  }
  public function getStrNbJoueurs()
  {
    $strPlayers = $this->Mission->getWpPost()->getPostMeta(self::FIELD_PLAYERID);
    if ($strPlayers=='') {
      $strPlayers = (self::isAdmin() ? 'Wip Nb' : '');
    }
    return $strPlayers.' Survivants';
  }
  public function getLinkedDuration()
  { return '<a href="/tag/'.strtolower(str_replace(' ', '-', $this->getStrDuree())).'">'.$this->getStrDuree().'</a>'; }
  private function getStrDuree()
  {
    $strDuree = $this->Mission->getWpPost()->getPostMeta(self::FIELD_DURATIONID);
    return ($strDuree=='' ? $this->getMission()->getDuration()->getStrDuree() : $strDuree.' minutes');
  }
  private function getStrOrigine()
  {
    $str = $this->Mission->getStrOrigine();
    if (empty($str)) {
      $str = 'TODO';
    }
    return $str;
  }
  // Fin des extras pour l'affichage d'un article de la Home
  ///////////////////////////////////////////////////////////////










  protected $urlTemplateExtract = 'web/pages/public/fragments/mission-article.php';
  protected $urlTemplateHome    = 'web/pages/public/fragments/mission-article-home.php';
  protected $strModelObjRules   = '<li class="objRule hasTooltip"><span class="tooltip"><header>%1$s</header><div>%2$s</div></span></li>';
  protected $h5Ul               = '<h5>%1$s</h5><ul>%2$s</ul>';


  /**
   * Class par défaut du Select
   * @var $classe
   */
  public $classe = 'custom-select custom-select-sm filters';

  private function getMissionContentObjRules($categId, $label)
  {
    $WpPosts = $this->WpPostServices->getWpPostsByCustomField(self::FIELD_MISSIONID, $this->Mission->getWpPost()->getID());
    $strObj = array();
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $WpCategories = $WpPost->getCategories();
      $isObj = false;
      while (!empty($WpCategories)) {
        $WpCategory = array_shift($WpCategories);
        if ($WpCategory->getCatId()==$categId) {
          $isObj = true;
        }
      }
      if ($isObj) {
        $rank = $WpPost->getPostMeta('rang');
        $strObj[$rank] = vsprintf($this->strModelObjRules, array($WpPost->getPostTitle(), $WpPost->getPostContent()));
      }
    }
    if (!empty($strObj)!=0) {
      ksort($strObj);
      return vsprintf($this->h5Ul, array($label, implode('', $strObj)));
    } else {
      return vsprintf($this->h5Ul, array($label, 'Non saisis pour le moment.'));
    }
  }
  public function getMissionContentObjectives()
  { return $this->getMissionContentObjRules(self::WP_CAT_OBJECTIVE_ID, 'Objectifs'); }
  public function getMissionContentRules()
  { return $this->getMissionContentObjRules(self::WP_CAT_RULE_ID, 'Regles speciales'); }
  /**
   * @return Mission
   */
  public function getMission()
  { return $this->Mission; }







}
