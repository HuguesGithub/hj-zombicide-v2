<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe AdminPageBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.11
 */
class AdminPageBean extends MainPageBean
{
  protected $urlFragmentPagination = 'web/pages/admin/fragments/fragment-pagination.php';
  protected $tplHomeAdminBoard = 'web/pages/admin/home-admin-board.php';

  /**
   * Backup Cron Table
   */
  const WP_DB_BACKUP_CRON = 'wp_db_backup_cron';
  /**
   * @param string $tag
   */
  public function __construct($tag='')
  {
    parent::__construct();
    $this->urlParams = array();
    $this->analyzeUri();
    $this->tableName = 'wp_11_zombicide_'.$tag;
    $this->tplAdminerUrl  = 'http://zombicide.jhugues.fr/wp-content/plugins/adminer/inc/adminer/loader.php';
    $this->tplAdminerUrl .= '?username=dbo507551204&db=db507551204&table='.$this->tableName;
  }

  /**
   * @return string
   */
  public function analyzeUri()
  {
    $uri = $_SERVER['REQUEST_URI'];
    $pos = strpos($uri, '?');
    if ($pos!==false) {
      $arrParams = explode('&', substr($uri, $pos+1, strlen($uri)));
      if (!empty($arrParams)) {
        foreach ($arrParams as $param) {
          list($key, $value) = explode('=', $param);
          $this->urlParams[$key] = $value;
        }
      }
      $uri = substr($uri, 0, $pos-1);
    }
    $pos = strpos($uri, '#');
    if ($pos!==false) {
      $this->anchor = substr($uri, $pos+1, strlen($uri));
    }
    if (isset($_POST)) {
      foreach ($_POST as $key => $value) {
        $this->urlParams[$key] = $value;
      }
    }
    return $uri;
  }
  /**
   * @return string
   */
  public function getContentPage()
  {
    $strReturned = 'Need to be an admin';
    if (self::isAdmin()) {
      if (!isset($this->urlParams[self::CST_ONGLET])) {
        $strReturned = $this->getHomeContentPage();
      } elseif ($this->urlParams[self::CST_ONGLET]==self::CST_SKILL) {
        $Bean = new AdminPageSkillsBean($this->urlParams);
        $strReturned = $Bean->getSpecificContentPage();
      } elseif ($this->urlParams[self::CST_ONGLET]==self::CST_SURVIVOR) {
        $Bean = new AdminPageSurvivorsBean($this->urlParams);
        $strReturned = $Bean->getSpecificContentPage();
      } elseif ($this->urlParams[self::CST_ONGLET]==self::CST_EXPANSION) {
        $Bean = new AdminPageExpansionsBean($this->urlParams);
        $strReturned = $Bean->getSpecificContentPage();
      } else {
        $strReturned = "Need to add <b>".$this->urlParams[self::CST_ONGLET]."</b> to AdminPageBean > getContentPage().";
      }
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getHomeContentPage()
  {
    /////////////////////////////////////////////////
    // Gestion des Cartes.
    // On récupère les cartes qu'on souhaite afficher sur la Home
    // La carte relatives aux compétences
    $Bean = new AdminPageSkillsBean();
    $lstCards  = $Bean->getCheckCard();
    // La carte relatives aux extensions
    $Bean = new AdminPageExpansionsBean();
    $lstCards .= $Bean->getCheckCard();
    // La carte relatives aux Survivants
    $Bean = new AdminPageSurvivorsBean();
    $lstCards .= $Bean->getCheckCard();
    // La carte relatives aux Missions
    $Bean = new AdminPageMissionsBean();
    $lstCards .= $Bean->getCheckCard();

    $args = array(
      // La liste des Cartes affichées sur le panneau d'accueil de la Home - 1
      $lstCards,
   );
    return $this->getRender($this->tplHomeAdminBoard, $args);
  }


  /**
   * @param unknown $queryArg
   * @param unknown $post_status
   * @param unknown $curPage
   * @param unknown $nbPages
   * @param unknown $nbElements
   * @return string
   */
  protected function getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements)
  {
    ////////////////////////////////////////////////////////////////////////////
    // Lien vers la première page. Seulement si on n'est ni sur la première, ni sur la deuxième page.
    if ($curPage>=3) {
      $queryArg[self::CST_CURPAGE] = 1;
      $strToFirst = '<a class="first-page button" href="'.$this->getQueryArg($queryArg).'"><span aria-hidden="true">&laquo;</span></a>';
    } else {
      $strToFirst = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // Lien vers la page précédente. Seulement si on n'est pas sur la première.
    if ($curPage>=2) {
      $queryArg[self::CST_CURPAGE] = $curPage-1;
      $strToPrevious = '<a class="prev-page button" href="'.$this->getQueryArg($queryArg).'"><span aria-hidden="true">&lsaquo;</span></a>';
    } else {
      $strToPrevious = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // Lien vers la page suivante. Seulement si on n'est pas sur la dernière.
    if ($curPage<$nbPages) {
      $queryArg[self::CST_CURPAGE] = $curPage+1;
      $strToNext = '<a class="next-page button" href="'.$this->getQueryArg($queryArg).'"><span aria-hidden="true">&rsaquo;</span></a>';
    } else {
      $strToNext = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
    }
    ////////////////////////////////////////////////////////////////////////////
    // Lien vers la dernière page. Seulement si on n'est pas sur la dernière, ni l'avant-dernière.
    if ($curPage<$nbPages-1) {
      $queryArg[self::CST_CURPAGE] = $nbPages;
      $strToLast = '<a class="next-page button" href="'.$this->getQueryArg($queryArg).'"><span aria-hidden="true">&raquo;</span></a>';
    } else {
      $strToLast = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
    }

    $args = array(
      // Nombre d'éléments - 1
      $nbElements,
      // Lien vers la première page - 2
      $strToFirst,
      // Lien vers la page précédente - 3
      $strToPrevious,
      // Page courante - 4
      $curPage,
      // Nombre total de pages - 5
      $nbPages,
      // Lien vers la page suivante - 6
      $strToNext,
      // Lien vers la dernière page - 7
      $strToLast,
    );
    return $this->getRender($this->urlFragmentPagination, $args);
  }


}
