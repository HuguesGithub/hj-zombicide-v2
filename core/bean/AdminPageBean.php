<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe AdminPageBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.26
 */
class AdminPageBean extends MainPageBean
{
  protected $urlFragmentPagination = 'web/pages/admin/fragments/fragment-pagination.php';

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
    $this->analyzeUri();
    $this->tableName = 'wp_11_zombicide_'.$tag;
    $this->tplAdminerUrl  = 'http://zombicide.jhugues.fr/wp-content/plugins/adminer/inc/adminer/loader.php';
    $this->tplAdminerUrl .= '?username=dbo507551204&db=db507551204&table='.$this->tableName;
    $this->SkillServices  = new SkillServices();
    $this->WpPostServices = new WpPostServices();
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
    if (self::isAdmin()) {
      switch ($this->urlParams['onglet']) {
        case 'mission'    :
          $returned = AdminPageMissionsBean::getStaticContentPage($this->urlParams);
        break;
        case 'parametre'  :
          $returned = AdminPageParametresBean::getStaticContentPage($this->urlParams);
        break;
        case 'skill'  :
          $returned = AdminPageSkillsBean::getStaticContentPage($this->urlParams);
        break;
        case 'survivor'  :
          $returned = AdminPageSurvivorsBean::getStaticContentPage($this->urlParams);
        break;
        case ''       :
          $returned = $this->getHomeContentPage();
        break;
        default       :
          $returned = "Need to add <b>".$this->urlParams['onglet']."</b> to AdminPageBean > getContentPage().";
        break;
      }
    }
    return $returned;
  }
  /**
   * @return string
   */
  public function getHomeContentPage()
  {
    /////////////////////////////////////////////////
    // Gestion des Compétences.
    // On récupère la liste des Compétences qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $this->WpPostSkills = $this->WpPostServices->getWpPostByCategoryId(self::WP_CAT_SKILL_ID);
    $nbWpPostSkills = count($this->WpPostSkills);
    $this->Skills = $this->SkillServices->getSkillsWithFilters();
    $nbSkills = count($this->Skills);
    if ($nbWpPostSkills!=$nbSkills || $_GET['getAction']=='controlSkill') {
      $this->checkSkills();
      $strBilan = '';
      if ($nbWpPostSkills!=$nbSkills) {
        $strBilan .= "Le nombre d'articles ($nbWpPostSkills) ne correspond pas au nombre de compétences en base ($nbSkills).<br>";
      }
      if ($this->nbUpdates == 0 && $this->nbCreates == 0) {
        $strBilan .= "Un contrôle a été effectué, et aucune modification n'a été faite.";
      } else {
        $strBilan .= "Un contrôle a été effectué, ";
        if ($this->nbUpdates == 0) {
          $strBilan .= "aucune mise à jour n'a été faite, ";
        } else {
          $strBilan .= $this->nbUpdates." mises à jour ont été faites, ";
        }
        if ($this->nbCreates == 0) {
          $strBilan .= "aucune création n'a été faite.";
        } else {
          $strBilan .= $this->nbCreates." créations ont été faites.";
        }
      }
    }

    /////////////////////////////////////////////////
    // Gestion des Missions.
    // On récupère la liste des Missions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    // Enfin, pour le moment, on ne gère pas les données en base.
    $this->WpPostMissions = $this->WpPostServices->getWpPostByCategoryId(self::WP_CAT_MISSION_ID);
    $strMissions = 'Nombre de Missions avec un article : '.count($this->WpPostMissions).'.';


    /*
    $reset = $this->initVar('reset', '');
    $doReset = !empty($reset);
    if ($doReset) {
      $ts = time();
      list($N, $d, $m, $y) = explode(' ', date('N d m y', $ts));
      $nd = $d + ($N==1 ? 1 : 9-$N);
      $resetTs = mktime(1, 0, 0, $m, $nd, $y);
    }
    $request = "SELECT option_value FROM wp_11_options WHERE option_name='cron';";
    $row = MySQL::wpdbSelect($request);
    $Obj = array_shift($row);
    $arrOptions = unserialize($Obj->option_value);
    foreach ($arrOptions as $key => $value) {
      if (isset($value[WP_DB_BACKUP_CRON])) {
        $nextTs = $key;
        $arrOptions[$resetTs][WP_DB_BACKUP_CRON] = $value[WP_DB_BACKUP_CRON];
        unset($arrOptions[$key]);
      }
    }
    if ($doReset) {
      $serialized = serialize($arrOptions);
      $request = "UPDATE wp_11_options SET option_value='$serialized' WHERE option_name='cron';";
    }
    **/
    $args = array(
    // Date de la prochaine sauvegarde - 1
      date('d/m/Y h:i:00'/*, $nextTs*/),
      // Bilan des mises à jours des Compétences - 2
      $strBilan,
      // Bilan des Missions - 3
      $strMissions,
   );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/admin/home-admin-board.php');
    return vsprintf($str, $args);
  }

  /**
   * @param unknown $urlParams
   */
  public function returnPostActionPage($urlParams)
  {
    switch ($urlParams[self::CST_POSTACTION]) {
      case 'add'   :
        $returned = $this->getAddPage();
      break;
      case 'edit'  :
        $returned = $this->getEditPage($urlParams['id']);
      break;
      case self::CST_TRASH :
        $returned = $this->getTrashPage($urlParams['id']);
      break;
      case 'clone' :
        $returned = $this->getClonePage($urlParams['id']);
      break;
      case 'Appliquer' :
        // On est dans le cas du bulkAction. On doit donc vérifier l'action.
        if ($urlParams['action']==self::CST_TRASH) {
          $returned = $this->getBulkTrashPage();
        } else {
          $returned = $this->getListingPage();
        }
      break;
      default      :
        $returned = $this->getListingPage();
      break;
    }
    return $returned;
  }

  /**
   * Retourne l'interface commune de confirmation de suppression d'éléments
   * @param string $title Titre de la page
   * @param string $subTitle Libellé spécifique à la suppression
   * @param string $strLis Liste des lis des éléments à supprimer
   * @param string $urlCancel Url de rollback si on annule la suppression.
   * @return string
   */
  public function getConfirmDeletePage($title, $subTitle, $strLis, $urlCancel)
  {
    // Les données de l'interface.
    $args = array(
      // Titre de l'opération - 1
      $title,
      // Url de l'action - 2
      '#',
      // - 3
      $subTitle,
      // Liste des éléments qui vont être supprimés - 4
      $strLis,
      // Url pour Annuler - 5
      $urlCancel,
      // Postaction - 6
      self::CST_TRASH,
      '','','','','','','','','','','','','','',
    );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/admin/delete-common-elements.php');
    return vsprintf($str, $args);
  }









  private function checkSkills()
  {
    $this->nbUpdates = 0;
    $this->nbCreates = 0;
    $nbWpPostSkills  = count($this->WpPostSkills);
    while (!empty($this->WpPostSkills)) {
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostSkills);
      $name         = $this->WpPost->getPostTitle();
      // On recherche un Skill dans la base de données qui correspond.
      $Skills       = $this->SkillServices->getSkillsWithFilters(array(self::FIELD_NAME=>$name));
      if (empty($Skills)) {
        // Si on n'en a pas, on doit créer une entrée correspondante.
        $Skill = new Skill();
        $Skill->setName($name);
        $this->checkSkill($Skill, true);
      } elseif (count($Skills)>1) {
        // Si on en a plus d'une, c'est sans doute que le filtre de recherche était trop large (notamment "+1 Action" avec "+1 Action gratuite...")
        $doUpdate = false;
        while (!empty($Skills) && !$doUpdate) {
          $Skill = array_shift($Skills);
          if ($name==$Skill->getName()) {
            $this->checkSkill($Skill);
            $doUpdate = true;
          }
        }
        if (!$doUpdate) {
          $Skill = new Skill();
          $Skill->setName($name);
          $this->checkSkill($Skill, true);
        }
      } else {
        // Si on en a juste une, c'est tranquille.
        $Skill = array_shift($Skills);
        $this->checkSkill($Skill);
      }
    }
  }

  private function checkSkill($Skill, $doCreate=false)
  {
    // On initialise les données
    $doUpdate = false;
    $code         = $this->WpPost->getPostMeta(self::FIELD_CODE);
    $name         = $this->WpPost->getPostTitle();
    $description  = $this->WpPost->getPostContent();
    $description  = substr($description, 25, -27);
    $official     = $this->WpPost->getPostMeta(self::FIELD_OFFICIAL);
    // On vérifie si la donnée en base correspond à l'article.
    if ($Skill->getCode()!=$code) {
      $Skill->setCode($code);
      $doUpdate = true;
    }
    if ($Skill->getName()!=$name) {
      $Skill->setName($name);
      $doUpdate = true;
    }
    if ($Skill->getDescription()!=$description) {
      $Skill->setDescription($description);
      $doUpdate = true;
    }
    if ($Skill->isOfficial()!=$official) {
      $Skill->setOfficial($official);
      $doUpdate = true;
    }
    if ($doCreate) {
    // Si on veut créer, on le fait.
    $this->SkillServices->insertSkill($Skill);
    $this->nbCreates++;
    } elseif ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->SkillServices->updateSkill($Skill);
      $this->nbUpdates++;
    }
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
