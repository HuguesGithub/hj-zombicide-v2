<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageMissionsBean
 * @author Hugues
 * @since 1.05.10
 * @version 1.07.25
 */
class AdminPageMissionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlMissionListing = 'web/pages/admin/mission-listing.php';
  protected $urlAdminEdit      = 'web/pages/admin/mission-edit.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_MISSION);
    $this->title = 'Missions';
    $this->MissionServices    = new MissionServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    if (isset($this->urlParams[self::FIELD_ID])) {
      $this->Mission = $this->MissionServices->selectMission($this->urlParams[self::FIELD_ID]);
    }
    if (isset($_POST)&&!empty($_POST)) {
      $this->dealWithPost();
    }
    switch ($this->urlParams[self::CST_POSTACTION]) {
      case 'confirmEdit'  :
      case self::CST_EDIT :
        return $this->getEditContentPage();
      break;
      default :
        return $this->getListContentPage();
      break;
    }
  }
  private function dealWithPost()
  {
    if ($this->urlParams[self::CST_POSTACTION]=='confirmEdit') {
      $this->Mission->setWidth($this->urlParams[self::FIELD_WIDTH]);
      $this->Mission->setHeight($this->urlParams[self::FIELD_HEIGHT]);
      $this->MissionServices->updateMission($this->Mission);
    }
  }
  public function getListContentPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_TITLE);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $filters = array();
    if (isset($this->urlParams[self::FIELD_ORIGINEID])) {
      $filters[self::FIELD_ORIGINEID] = $this->urlParams[self::FIELD_ORIGINEID];
    }
    $Missions = $this->MissionServices->getMissionsWithFilters($filters, $orderby, $order);
    $nbElements = count($Missions);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedMissions = array_slice($Missions, ($curPage-1)*$nbPerPage, $nbPerPage);
    if (!empty($DisplayedMissions)) {
      foreach ($DisplayedMissions as $Mission) {
        $MissionBean = new MissionBean($Mission);
        $strRows .= $MissionBean->getRowForAdminPage();
      }
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_MISSION,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);

    $args = array(
      // Liste des missions affichées - 1
      $strRows,
      // Filtres - 2
      '',
      // Url pour créer une nouvelle Mission - 3
      '/wp-admin/post-new.php',
      // Subs - 4
      '',
      // Pagination - 5
      $strPagination,
      // Filtre Extensions - 6
      OrigineBean::getStaticSelect(self::FIELD_ORIGINEID, $this->urlParams[self::FIELD_ORIGINEID]),
    );
    return $this->getRender($this->urlMissionListing, $args);
  }
  public function getEditContentPage()
  {
    //////////////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
    // L'identifiant de la mission - 1
    $this->Mission->getId(),
    // Le code de la Mission - 2
    $this->Mission->getCode(),
    // Le titre de la Mission - 3
    $this->Mission->getTitle(),
    // Le synopsis de la Mission - 4
    $this->Mission->getWpPost()->getPostContent(),
    // La difficulté de la Mission - 5
    $this->Mission->getStrDifficulty(),
    // Le nombre de Survivants de la Mission - 6
    $this->Mission->getStrNbJoueurs(),
    // La durée de la Mission - 7
    $this->Mission->getStrDuree(),
    // L'origine de la Mission - 8
    $this->Mission->getStrOrigine(),
    // La liste des extensions utilisées - 9
    implode(', ', unserialize($this->Mission->getWpPost()->getPostMeta('expansionIds'))),
    // La liste des dalles utilisées - 10
    $this->Mission->getWpPost()->getPostMeta('tileIds'),
    // Url de l'image de la map - 11
    $this->Mission->getThumbUrl(),
    // Largeur de la Map - 12
    $this->Mission->getWidth(),
    // Hauteur de la Map - 13
    $this->Mission->getHeight(),
      '', '', '', '', '', '', '', '', '', '', '', '', '',
    );
    // Puis on le restitue.
    return $this->getRender($this->urlAdminEdit, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Missions.
    // On récupère la liste des Missions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new MissionActions();
    $strBilan  = $Act->dealWithMissionVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_MISSION,
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
