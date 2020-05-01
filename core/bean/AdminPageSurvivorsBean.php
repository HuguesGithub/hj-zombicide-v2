<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageSurvivorsBean
 * @author Hugues
 * @since 1.05.01
 * @version 1.05.01
 */
class AdminPageSurvivorsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlSurvivorListing = 'web/pages/admin/survivor-listing.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_SURVIVOR);
    $this->title = 'Survivants';
    $this->SurvivorServices  = new SurvivorServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    $strRows = '';
    $nbPerPage = 10;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_NAME);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters(array(), $orderby, $order);
    $nbElements = count($Survivors);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedSurvivors = array_slice($Survivors, ($curPage-1)*$nbPerPage, $nbPerPage);
    while (!empty($DisplayedSurvivors)) {
      $Survivor = array_shift($DisplayedSurvivors);
      $strRows .= $Survivor->getBean()->getRowForAdminPage();
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_SURVIVOR,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);
    /*
    // Sorts
    $queryArg[self::WP_ORDERBY] = self::FIELD_CODE;
    $queryArg[self::WP_ORDER] = ($orderby==self::FIELD_CODE && $order==self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
    $urlSortCode = $this->getQueryArg($queryArg);
    $queryArg[self::WP_ORDERBY] = self::FIELD_NAME;
    $queryArg[self::WP_ORDER] = ($orderby==self::FIELD_NAME && $order==self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
    $urlSortTitle = $this->getQueryArg($queryArg);
    $args = array(
      // Liste des compétences affichées - 1
      $strRows,
      // Filtres - 2
      '',
      // Url pour créer une nouvelle Compétence - 3
      $this->getQueryArg(array(self::CST_ONGLET=>self::CST_SKILL, self::CST_POSTACTION=>'add')),
      // Subs - 4
      '',
      // Pagination - 5
      $strPagination,
      // class pour le tri sur code - 6
      ($orderby==self::FIELD_CODE ? $order : self::ORDER_DESC),
      // url pour le tri sur code - 7
      $urlSortCode,
      // class pour le tri sur title - 8
      ($orderby==self::FIELD_NAME ? $order : self::ORDER_DESC),
      // url pour le tri sur title - 9
      $urlSortTitle,
      '','','','','','','','','','','','','',
    );
    * */
    $args = array(
      // Liste des survivants affichés - 1
      $strRows,
      // 2 à 4
      '','','',
      // Pagination - 5
      $strPagination,
      '','','','','','','','','','','','','','','','','','','','','','',''
    );
    return $this->getRender($this->urlSurvivorListing, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /*
    /////////////////////////////////////////////////
    // Gestion des Survivants.
    // On récupère la liste des Compétences qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new SkillActions();
    $strBilan  = $Act->dealWithSkillVerif();
    */
    $strBilan  = 'Not yet implemented';
    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_SURVIVOR.'-verif',
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
