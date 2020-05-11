<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageExpansionsBean
 * @author Hugues
 * @since 1.04.30
 * @version 1.05.11
 */
class AdminPageExpansionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlExpansionListing = 'web/pages/admin/expansion-listing.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_EXPANSION);
    $this->title = 'Extensions';
    $this->ExpansionServices  = new ExpansionServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_NAME);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(), $orderby, $order);
    $nbElements = count($Expansions);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedExpansions = array_slice($Expansions, ($curPage-1)*$nbPerPage, $nbPerPage);
    if (!empty($DisplayedExpansions)) {
      foreach ($DisplayedExpansions as $Expansion) {
        $ExpansionBean = new ExpansionBean($Expansion);
        $strRows .= $ExpansionBean->getRowForAdminPage();
      }
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_EXPANSION,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);

    $args = array(
      // Liste des extensions affichées - 1
      $strRows,
      // Filtres - 2
      '',
      // Url pour créer une nouvelle Extension - 3
      '/wp-admin/post-new.php',
      // Subs - 4
      '',
      // Pagination - 5
      $strPagination,
    );
    return $this->getRender($this->urlExpansionListing, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Extensions.
    // On récupère la liste des Extensions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new ExpansionActions();
    $strBilan  = $Act->dealWithExpansionVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_EXPANSION,
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
