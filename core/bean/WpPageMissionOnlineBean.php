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
  protected $xmlSuffixe         = '.mission.xml';
  protected $urlOnlineDetailSurvivor = 'web/pages/public/fragments/online-detail-survivor.php';
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
      $this->setDimensions(),
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
      implode('', $this->arrLstSurvivorDetail),
    );
    return $this->getRender($this->urlTemplate, $args);
  }

  private function getLstPortraits()
  {
    for ($rank=count($this->arrLstPortraits); $rank<6; $rank++) {
      $args = array(
        self::ATTR_ID    => 'portrait-'.$rank,
        self::ATTR_CLASS => 'unknown',
        'data-rank'      => $rank,
        self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/p.jpg',
        self::ATTR_TITLE => 'Add a Survivor',
      );
      $this->arrLstPortraits[] = $this->getBalise(self::TAG_IMG, '', $args);
    }
    return implode('', $this->arrLstPortraits);
  }
  private function buildLstPortraits($survivor)
  {
    $rank = count($this->arrLstPortraits)+1;
    $args = array(
      self::ATTR_ID    => 'portrait-'.$rank,
      self::ATTR_CLASS => 'known',
      'data-rank'      => $rank,
      self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/p'.$survivor[self::XML_ATTRIBUTES]['src'].'.jpg',
      self::ATTR_TITLE => '',
    );
    $this->arrLstPortraits[] = $this->getBalise(self::TAG_IMG, '', $args);
  }
  private function buildLstSurvivorDetail($survivor)
  {
    $rank = count($this->arrLstPortraits)+1;
    $id = substr($survivor[self::XML_ATTRIBUTES]['id'], 1);
    $Survivor = $this->SurvivorServices->selectSurvivor($id);

    $args = array(
      // Le rang du Survivant dans la partie
      $rank,
      // L'url du portrait
      $Survivor->getPortraitUrl(),
      // Le nom du Survivant
      $Survivor->getName(),
      // Niveau du Survivant
      strtolower($survivor[self::XML_ATTRIBUTES]['level']),
      // Nombre d'XP - 5
      strtolower($survivor[self::XML_ATTRIBUTES]['experiencePoints']),
      // Nombre de PA - 6
      strtolower($survivor[self::XML_ATTRIBUTES]['actionPoints']),
      // Nombre de PV - 7
      strtolower($survivor[self::XML_ATTRIBUTES]['hitPoints']),

    );

    $this->arrLstSurvivorDetail[] = $this->getRender($this->urlOnlineDetailSurvivor, $args);
  }

  private function displayZombies()
  {
    // On récupère les Zombies pour les afficher
    $zombies = $this->map['zombies']['zombie'];
    $lstZombies = '';
    if (!empty($zombies)) {
      if (count($zombies)==1) {
        $TokenBean = new TokenBean($zombies);
        $lstZombies .= $TokenBean->getTokenBalise();
        $lstZombies .= $TokenBean->getTokenMenu();
      } else {
        foreach ($zombies as $zombie) {
          $TokenBean = new TokenBean($zombie);
          $lstZombies .= $TokenBean->getTokenBalise();
          $lstZombies .= $TokenBean->getTokenMenu();
        }
      }
    }
    return $lstZombies;
  }
  private function displaySurvivors()
  {
    // On récupère les Survivants pour les afficher
    $survivors = $this->map['survivors']['survivor'];
    $lstSurvivors = '';
    if (!empty($survivors)) {
      if (count($survivors)==1) {
        $TokenBean = new TokenBean($survivors);
        $lstSurvivors .= $TokenBean->getTokenBalise();
        $lstSurvivors .= $TokenBean->getTokenMenu();
        $this->buildLstPortraits($survivors);
        $this->buildLstSurvivorDetail($survivors);
      } else {
        foreach ($survivors as $survivor) {
          $TokenBean = new TokenBean($survivor);
          $lstSurvivors .= $TokenBean->getTokenBalise();
          $lstSurvivors .= $TokenBean->getTokenMenu();
          $this->buildLstPortraits($survivor);
          $this->buildLstSurvivorDetail($survivor);
        }
      }
    }
    return $lstSurvivors;
  }
  private function displayTokens()
  {
    // On récupère les Tokens pour les afficher
    $chips = $this->map['chips']['chip'];
    $lstChips = '';
    foreach ($chips as $chip) {
      $TokenBean = new TokenBean($chip);
      $lstChips .= $TokenBean->getTokenBalise();
      $lstChips .= $TokenBean->getTokenMenu();
    }
    return $lstChips;
  }
  private function displayTiles()
  {
    // On récupère les Dalles pour les afficher
    $tiles = $this->map['tiles']['tile'];
    $lstTiles = '';
    foreach ($tiles as $tile) {
      $code        = $tile[self::XML_ATTRIBUTES]['code'];
      $orientation = $tile[self::XML_ATTRIBUTES]['orientation'];
      $args = array(
        self::ATTR_CLASS => 'mapTile '.$orientation,
        'style'          => "background:url('/wp-content/plugins/hj-zombicide/web/rsc/img/tiles/".$code."-500px.png');",
      );
      $lstTiles .= $this->getBalise(self::TAG_DIV, '', $args);
    }
    return $lstTiles;
  }
  private function setDimensions()
  {
    // On détermine les dimensions de la map pour pouvoir appliquer les styles css
    $this->width  = $this->map[self::XML_ATTRIBUTES]['width'];
    $this->height = $this->map[self::XML_ATTRIBUTES]['height'];
    return 'map'.$this->height.'x'.$this->width;
  }
  private function openFile()
  {
    $objXmlDocument = simplexml_load_file(PLUGIN_PATH.$this->urlDirLiveMissions.$_SESSION['zombieKey'].".mission.xml");
    $objJsonDocument = json_encode($objXmlDocument);
    $arrOutput = json_decode($objJsonDocument, TRUE);
    $this->map = $arrOutput['map'];
  }
}
