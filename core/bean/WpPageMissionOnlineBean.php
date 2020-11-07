<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageMissionOnlineBean
 * @author Hugues
 * @since 1.10.04
 * @version 1.10.04
 */
class WpPageMissionOnlineBean extends WpPageBean
{
  protected $urlDirMissions     = '/web/rsc/missions/';
  protected $urlDirLiveMissions = '/web/rsc/missions/live/';
  protected $urlLoginTemplate   = 'web/pages/public/wppage-mission-online-login.php';
  protected $urlTemplate        = 'web/pages/public/wppage-mission-online.php';
  protected $urlTchatMsgTpl     = 'web/pages/public/fragments/tchat-message.php';
  protected $urlSectionSetup    = 'web/pages/public/fragments/online-section-setup.php';
  protected $xmlSuffixe         = '.mission.xml';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->MissionServices = new MissionServices();
    $this->SurvivorServices = new SurvivorServices();
  }
  /**
   * @return string
   */
  public function getContentPage()
  {
    $this->msgError = '';
    if ($_POST['radioChoice']=='new') {
      $Missions = $this->MissionServices->getMissionsWithFilters(array(self::FIELD_CODE=>$_POST['selectMission']));
      if (empty($Missions)) {
        $this->msgError = '<em>Attention</em>, le code sélectionné n\'existe pas.';
      } elseif (is_file(PLUGIN_PATH.$this->urlDirMissions.$_POST['selectMission'].$this->xmlSuffixe)) {
        // ON doit générer une clef qui va bien et la stocker dans zombieKey.
        // Puis on génère un fichier live à partir du fichier référence de la Mission.
        //AnJwMKqNkXba2suQ
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $strCode = substr(str_shuffle($str), 0, 16);
        $Mission = array_shift($Missions);
        copy(PLUGIN_PATH.$this->urlDirMissions.$Mission->getCode().$this->xmlSuffixe, PLUGIN_PATH.$this->urlDirLiveMissions.$strCode.$this->xmlSuffixe);
        $_SESSION['zombieKey'] = $strCode;
        $MissionOnline = new MissionOnline($Mission);
        $MissionOnline->setUp();
        $this->msgError = '<em>Attention</em>, la Mission sélectionnée existe.';
      } else {
        $this->msgError = '<em>Attention</em>, la Mission sélectionnée n\'existe pas.';
      }
    } elseif ($_POST['radioChoice']=='old') {
      if (is_file(PLUGIN_PATH.$this->urlDirLiveMissions.$_POST['saveCode'].$this->xmlSuffixe)) {
        // On récupère la clef fournie et on la stocke dans zombieKey
        $_SESSION['zombieKey'] = $_POST['saveCode'];
        $this->msgError = '<em>Attention</em>, le code saisi correspond à une partie sauvegardée.';
      } else {
        // Prévoir une gestion d'erreur pour fichier inexistant.
        $this->msgError = '<em>Attention</em>, le code saisi ne correspond pas à une partie sauvegardée.';
      }
    } elseif (isset($_GET['logout'])) {
      unset($_SESSION['zombieKey']);
    }


    if (isset($_SESSION['zombieKey']) && is_file(PLUGIN_PATH.$this->urlDirLiveMissions.$_SESSION['zombieKey'].$this->xmlSuffixe)) {
      return $this->getBoard();
    } else {
      return $this->getLogin();
    }
  }
  public function getLogin()
  {
    if ($this->msgError!='') {
      $strMsgError = $this->getBalise(self::TAG_DIV, $this->msgError, array(self::ATTR_CLASS=>'alert alert-danger'));
    } else {
      $strMsgError = '';
    }
    // Gérer les cas éventuels d'erreur.
    $args = array(
      // Le message d'erreur éventuel - 1
      $strMsgError,
      // Si old est checked - 2
      (isset($_POST['radioChoice']) && $_POST['radioChoice']=='old') ? 'checked' : '',
      // Si old n'est pas checked - 3
      (!isset($_POST['radioChoice']) || $_POST['radioChoice']=='new') ? 'checked' : '',
      // Si old est checked - 2
      (isset($_POST['radioChoice']) && $_POST['radioChoice']=='old') ? 'fa-dot-circle-o' : 'fa-circle-o',
      // Si old n'est pas checked - 3
      (!isset($_POST['radioChoice']) || $_POST['radioChoice']=='new') ? 'fa-dot-circle-o' : 'fa-circle-o',

    );
    return $this->getRender($this->urlLoginTemplate, $args);
  }
  public function getBoard()
  {
    //////////////////////////////////////////////////////////////////
    // On va afficher une Mission, à partir de son XML... Donc, déjà, il faut l'ouvrir !
    $this->openFile();
    // On initialise quelques variables :
    $this->arrLstPortraits = array();
    $this->arrLstSurvivorDetail = array();

    $Missions = $this->MissionServices->getMissionsWithFilters(array(self::FIELD_CODE=>'AJ01'));
    $Mission = array_shift($Missions);
    $MissionBean = $Mission->getBean();


    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // class pour afficher correctement la Map - 1
      $this->getDimensions(),
      // La liste des Dalles - 2
      $this->displayTiles(),
      // La liste des Zones - 3
      '',
      // La liste des Tokens - 4
      $this->displayTokens().$this->displaySurvivors().$this->displayZombies(),
      // Identifiant de la Partie - 5
      // TODO : en dur pour le moment.
      $_SESSION['zombieKey'],
      // Liste des Objectifs - 6
      $MissionBean->getMissionContentObjectives(),
      // Liste des Règles Spéciales - 7
      $MissionBean->getMissionContentRules(),
      // Portraits des Survivants dans la Sidebar - 8
      $this->getLstPortraits(),
      // Fiche d'identité des Survivants dans la Sidebar - 9
      $this->getLstDetails(),
      // Le Tchat - 10
      $this->getLstTchats(),
      // Le contenu de l'onglet SetUp - 11
      $this->getMissionSetup(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }

  private function getMissionSetup()
  {
    $Spawns = $this->objXmlDocument->xPath('//spawns')[0];
    $Pools  = $this->objXmlDocument->xPath('//pools/pool');

    $lstPools = '';
    foreach ($Pools as $Pool) {
      $innerDiv = $this->getBalise(self::TAG_DIV, $Pool->attributes()['current'].' / '.$Pool->attributes()['max'], array(self::ATTR_CLASS=>'badge'));
      $img = $this->getBalise(self::TAG_IMG, '', array(self::ATTR_SRC=>'/wp-content/plugins/hj-zombicide/web/rsc/img/zombies/'.$Pool->attributes()['type'].'.png'));
      $outerDiv = $this->getBalise(self::TAG_DIV, $img.$innerDiv, array(self::ATTR_CLASS=>'chip token zombie Standard non-draggable'));
      $lstPools .= $this->getBalise(self::TAG_LI, $outerDiv);
    }

    $args = array(
      // Spawn actuel - 1
      $Spawns->attributes()['interval'],
      // Etat de la réserve de Zombies - 2
      $lstPools,
    );
    return $this->getRender($this->urlSectionSetup, $args);
  }

  public function getLstTchats($tsTreshold='')
  {
    if ($tsTreshold=='') {
      $Tchats = $this->objXmlDocument->xPath('//tchat');
    } else {
      $this->openFile();
      $Tchats = $this->objXmlDocument->xPath('//tchat[@timestamp>"'.$tsTreshold.'"]');
    }
    usort($Tchats, 'sort_trees');
    $lstMsgs = '';
    $prevTs = '';
    while (!empty($Tchats)) {
      $Tchat = array_shift($Tchats);
      $author = $Tchat->attributes()['author'];
      $ts = $Tchat->attributes()['timestamp']*1;
      // On insère un Tag pour séparer les messages des différentes journées.
      if ($prevTs!='' && date('d', $ts)!=date('d', $prevTs)) {
        $liClass = 'clearfix';
        $msgDataClass = ' message changeDate';
        $msgDataContent  = date('d m Y', $ts);
        $msgClass = ' hidden';
        $args = array(
          $liClass,
          $msgDataClass,
          $msgDataContent,
          $msgClass,
          '',
          $ts,
        );
        $lstMsgs .= $this->getRender($this->urlTchatMsgTpl, $args);
      }
      // Selon que l'auteur est Automat, le user courant ou un autre, le visuel change
      if ($author=='Automat') {
        $liClass = 'clearfix';
        $msgDataClass = '';
        $msgDataContent  = date('H:i', $ts);
        $msgClass = ' tech-message';
      } elseif ($author=='me') {
        $liClass = 'clearfix';
        $msgDataClass = ' align-right';
        $msgDataContent  = $this->getBalise(self::TAG_SPAN, date('H:i', $ts), array(self::ATTR_CLASS=>'message-data-time')).'&nbsp;&nbsp;';
        $msgDataContent .= $this->getBalise(self::TAG_SPAN, $author, array(self::ATTR_CLASS=>'message-data-name'));
        $msgClass = ' other-message float-right';
      } else {
        $liClass = '';
        $msgDataClass = '';
        $msgDataContent  = $this->getBalise(self::TAG_SPAN, $author, array(self::ATTR_CLASS=>'message-data-name')).'&nbsp;&nbsp;';
        $msgDataContent .= $this->getBalise(self::TAG_SPAN, date('H:i', $ts), array(self::ATTR_CLASS=>'message-data-time'));
        $msgClass = ' my-message';
      }
      $args = array(
        $liClass,
        $msgDataClass,
        $msgDataContent,
        $msgClass,
        $Tchat[0],
        $ts,
      );
      $lstMsgs .= $this->getRender($this->urlTchatMsgTpl, $args);
      $prevTs = $ts;
    }
    return $lstMsgs;
  }

  private function getLstDetails()
  {
    $lstDetails       = array();
    $survivors        = $this->objXmlDocument->xPath('//survivor');
    while (!empty($survivors)) {
      $survivor       = array_shift($survivors);
      $TokenBean      = new TokenBean($survivor);
      $lstDetails[]   = $TokenBean->getTokenDetail();
    }
    return implode('', $lstDetails);
  }
  private function getLstPortraits()
  {
    $lstPortraits     = array();
    $survivors        = $this->objXmlDocument->xPath('//survivor');
    while (!empty($survivors)) {
      $survivor       = array_shift($survivors);
      $TokenBean      = new TokenBean($survivor);
      $lstPortraits[] = $TokenBean->getTokenPortrait();
    }
    // On rajoute un Unkonwn, pour pouvoir ajouter un Survivant.
    $args = array(
      self::ATTR_ID    => 'portrait-new',
      self::ATTR_CLASS => 'unknown',
      self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/p.jpg',
      self::ATTR_TITLE => 'Add a Survivor',
    );
    $lstPortraits[] = $this->getBalise(self::TAG_IMG, '', $args);
    return implode('', $lstPortraits);
  }

  private function displayZombies()
  {
    $lstZombies    = '';
    // On récupère les Zombies pour les afficher
    $zombies       = $this->objXmlDocument->xPath('//zombie');
    while (!empty($zombies)) {
      $zombie      = array_shift($zombies);
      $TokenBean   = new TokenBean($zombie);
      $lstZombies .= $TokenBean->getTokenBalise();
      $lstZombies .= $TokenBean->getTokenMenu();
    }
    return $lstZombies;
  }
  private function displaySurvivors()
  {
    $lstSurvivors    = '';
    // On récupère les Survivants pour les afficher
    $survivors       = $this->objXmlDocument->xPath('//survivor');
    while (!empty($survivors)) {
      $survivor      = array_shift($survivors);
      $TokenBean     = new TokenBean($survivor);
      $lstSurvivors .= $TokenBean->getTokenBalise();
      $lstSurvivors .= $TokenBean->getTokenMenu();
    }
    return $lstSurvivors;
  }
  private function displayTokens()
  {
    $lstChips    = '';
    // On récupère les Tokens pour les afficher
    $chips       = $this->objXmlDocument->xPath('//chip');
    while (!empty($chips)) {
      $chip      = array_shift($chips);
      $TokenBean = new TokenBean($chip);
      $lstChips .= $TokenBean->getTokenBalise();
      $lstChips .= $TokenBean->getTokenMenu();
    }
    return $lstChips;
  }
  private function displayTiles()
  {
    $lstTiles      = '';
    // On récupère les Dalles pour les afficher
    $tiles         = $this->objXmlDocument->xPath('//tile');
    while (!empty($tiles)) {
      $tile        = array_shift($tiles);
      $code        = $tile->attributes()[self::FIELD_CODE];
      $orientation = $tile->attributes()['orientation'];
      $args = array(
        self::ATTR_CLASS => 'mapTile '.$orientation,
        'style'          => "background:url('/wp-content/plugins/hj-zombicide/web/rsc/img/tiles/".$code."-500px.png');",
      );
      $lstTiles .= $this->getBalise(self::TAG_DIV, '', $args);
    }
    return $lstTiles;
  }
  private function getDimensions()
  {
    $maps = $this->objXmlDocument->xPath('//map');
    $map  = array_shift($maps);
    // On détermine les dimensions de la map pour pouvoir appliquer les styles css
    $this->width  = $map->attributes()['width'];
    $this->height = $map->attributes()['height'];
    return 'map'.$this->height.'x'.$this->width;
  }
  private function openFile()
  {
    $fileName = PLUGIN_PATH.$this->urlDirLiveMissions.$_SESSION['zombieKey'].".mission.xml";
    $this->objXmlDocument = simplexml_load_file($fileName);
    $objXmlDocument = simplexml_load_file($fileName);
    $objJsonDocument = json_encode($objXmlDocument);
    $arrOutput = json_decode($objJsonDocument, TRUE);
    $this->map = $arrOutput['map'];
  }
}

function sort_trees($t1, $t2) {
  return ($t1['timestamp']*1 > $t2['timestamp']*1);
}
