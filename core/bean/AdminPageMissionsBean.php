<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageMissionsBean
 * @author Hugues
 * @since 1.05.10
 * @version 1.05.12
 */
class AdminPageMissionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlMissionListing = 'web/pages/admin/mission-listing.php';
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
      //$this->dealWithPost();
    }
    switch ($this->urlParams[self::CST_POSTACTION]) {
      case 'confirmEdit'  :
      case self::CST_EDIT :
        //return $this->getEditContentPage();
      break;
      default :
        return $this->getListContentPage();
      break;
    }
  }

  public function getListContentPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_TITLE);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Missions = $this->MissionServices->getMissionsWithFilters(array(), $orderby, $order);
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
    );
    return $this->getRender($this->urlMissionListing, $args);
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
