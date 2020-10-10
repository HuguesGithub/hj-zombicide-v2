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
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->MissionServices = new MissionServices();
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
      } elseif (is_file(PLUGIN_PATH.$this->urlDirMissions.$_POST['selectMission'].".mission.xml")) {
        // ON doit générer une clef qui va bien et la stocker dans zombieKey.
        // Puis on génère un fichier live à partir du fichier référence de la Mission.
        //AnJwMKqNkXba2suQ
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $strCode = substr(str_shuffle($str), 0, 16);
        $Mission = array_shift($Missions);
        copy(PLUGIN_PATH.$this->urlDirMissions.$Mission->getCode().".mission.xml", PLUGIN_PATH.$this->urlDirLiveMissions.$strCode.".mission.xml");
        $_SESSION['zombieKey'] = $strCode;
        $this->msgError = '<em>Attention</em>, la Mission sélectionnée existe.';
      } else {
        $this->msgError = '<em>Attention</em>, la Mission sélectionnée n\'existe pas.';
      }
    } elseif ($_POST['radioChoice']=='old') {
      if (is_file(PLUGIN_PATH.$this->urlDirLiveMissions.$_POST['saveCode'].".mission.xml")) {
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


    if (isset($_SESSION['zombieKey']) && is_file(PLUGIN_PATH.$this->urlDirLiveMissions.$_SESSION['zombieKey'].".mission.xml")) {
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
    $rank = count($this->arrLstSurvivorDetail)+1;
    // Le Contenu du premier DT
    $contentDT  = $survivor[self::XML_ATTRIBUTES]['src'].' - <span>';
    $contentDT .= $survivor[self::XML_ATTRIBUTES]['experiencePoints'].' <em>XP</em> - ';
    $contentDT .= $survivor[self::XML_ATTRIBUTES]['actionPoints'].' <em>PA</em> - ';
    $contentDT .= $survivor[self::XML_ATTRIBUTES]['hitPoints'].' <em>PV</em>';
    $contentDT .= '</span><i class="fa fa-times-circle float-right"></i>';
    $contentDL  = $this->getBalise(self::TAG_DT, $contentDT);

// Le contenu du deuxième DT
    $contentDT  = 'Compétences<i class="fa fa-window-minimize float-right"></i>';
    $args = array(
      'data-nav' => 'survivor-skill-'.$rank,
    );
    $contentDL .= $this->getBalise(self::TAG_DT, $contentDT, $args);
// Et du DD associé
    $contentDD  = 'Ici, l\'affichage des compétences du Survivant. On verra plus tard pour ce qui est des compétences acquises et celles en devenir de l\'être.';
    $args = array(
      self::ATTR_ID => 'survivor-skill-'.$rank,
    );
    $contentDL .= $this->getBalise(self::TAG_DD, $contentDD, $args);

// Le contenu du troisième DT
    $contentDT  = 'Équipement<i class="fa fa-window-minimize float-right"></i>';
    $args = array(
      'data-nav' => 'survivor-inventory-'.$rank,
    );
    $contentDL .= $this->getBalise(self::TAG_DT, $contentDT, $args);
// Et du DD associé
    $contentDD  = 'Ici, les 5 (voire plus si on veut en ajouter) emplacement d\'équipements.';
    $args = array(
      self::ATTR_ID => 'survivor-inventory-'.$rank,
    );
    $contentDL .= $this->getBalise(self::TAG_DD, $contentDD, $args);

    // Le rendu final.
    $args = array(
      self::ATTR_ID   => 'detail-survivor-'.$rank,
    );
    $this->arrLstSurvivorDetail[] = $this->getBalise(self::TAG_DL, $contentDL, $args);
  }

  private function displayZombie($zombie)
  {
    $id          = $zombie[self::XML_ATTRIBUTES]['id'];
    $quantite    = $zombie[self::XML_ATTRIBUTES]['quantite'];
    $tokenName   = $zombie[self::XML_ATTRIBUTES]['src'];
    if (preg_match($this->patternZombie, $tokenName, $matches)) {
      $strName   = $matches[1].' '.$matches[2];
    } else {
      $strName   = 'Pattern foireux ('.$tokenName.')';
    }

    $addClass    = ' zombie '.$matches[2];

    $args = array(
      self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/zombies/'.$tokenName.'.png',
      self::ATTR_TITLE => $strName.' x'.$quantite,
    );
    $content  = $this->getBalise(self::TAG_IMG, '', $args);
    $content .= $this->getBalise(self::TAG_DIV, $quantite, array(self::ATTR_CLASS=>'badge'));
    $args = array(
      self::ATTR_CLASS   => 'chip'.$addClass,
      self::ATTR_ID      => $id,
      'data-type'        => 'Zombie',
      'data-coordX'      => $zombie[self::XML_ATTRIBUTES]['coordX'],
      'data-coordY'      => $zombie[self::XML_ATTRIBUTES]['coordY'],
      'data-width'       => 50,
      'data-height'      => 50,
      'data-quantity'    => $quantite,
    );
    return $this->getBalise(self::TAG_DIV, $content, $args);
  }
  private function displayZombies()
  {
    // On récupère les Zombies pour les afficher
    $zombies = $this->map['zombies']['zombie'];
    $lstZombies = '';
    $this->patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
    if (!empty($zombies)) {
      if (count($zombies)==1) {
        $lstZombies .= $this->displayZombie($zombies);
      } else {
        foreach ($zombies as $zombie) {
          $lstZombies .= $this->displayZombie($zombie);
        }
      }
    }
    return $lstZombies;
  }
  private function displaySurvivor($survivor)
  {
    $id          = $survivor[self::XML_ATTRIBUTES]['id'];
    $level       = $survivor[self::XML_ATTRIBUTES]['level'];
    $tokenName   = $survivor[self::XML_ATTRIBUTES]['src'];

    $addClass    = ' survivor '.$level;

    $args = array(
      self::ATTR_SRC=>'/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/'.$tokenName.'.jpg'
    );
    $content = $this->getBalise(self::TAG_IMG, '', $args);
    $args = array(
      self::ATTR_CLASS   => 'chip'.$addClass,
      self::ATTR_ID      => $id,
      'data-type'        => 'Survivor',
      'data-coordX'      => $survivor[self::XML_ATTRIBUTES]['coordX'],
      'data-coordY'      => $survivor[self::XML_ATTRIBUTES]['coordY'],
      'data-width'       => 50,
      'data-height'      => 50,
    );
    $this->buildLstPortraits($survivor);
    $this->buildLstSurvivorDetail($survivor);
    return $this->getBalise(self::TAG_DIV, $content, $args);
  }
  private function displaySurvivors()
  {
    // On récupère les Survivants pour les afficher
    $survivors = $this->map['survivors']['survivor'];
    $lstSurvivors = '';
    if (!empty($survivors)) {
      if (count($survivors)==1) {
        $lstSurvivors .= $this->displaySurvivor($survivors);
      } else {
        foreach ($survivors as $survivor) {
          $lstSurvivors .= $this->displaySurvivor($survivor);
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
      $id          = $chip[self::XML_ATTRIBUTES]['id'];
      $type        = $chip[self::XML_ATTRIBUTES]['type'];
      $color       = $chip[self::XML_ATTRIBUTES]['color'];
      $status      = $chip[self::XML_ATTRIBUTES]['status'];
      $orientation = $chip[self::XML_ATTRIBUTES]['orientation'];
      $addClass    = ' token';
      $display     = true;

      switch ($type) {
        case 'Door' :
          $width     = 56;
          $height    = 56;
          $tokenName = 'door_'.strtolower($color).'_'.strtolower($status);
          $addClass .= ' '.$orientation;
        break;
        case 'Objective' :
          if ($status=='Picked') {
            $display = false;
          } else {
            $width     = 50;
            $height    = 50;
            $tokenName = 'objective_'.($status=='Unveiled' ? 'red' : strtolower($color));
          }
        break;
        case 'Spawn' :
          $width     = 100;
          $height    = 50;
          $tokenName = 'spawn_'.strtolower($color);
          $addClass .= ' '.$orientation.' '.strtolower($status);
        break;
        case 'Exit' :
          $width     = 100;
          $height    = 50;
          $tokenName = 'exit';
          $addClass .= ' '.$orientation.($status=='Unactive' ? ' unactive' : '');
        break;
        default :
          $tokenName = '';
        break;
      }
      if ($display) {
        $args = array(
          self::ATTR_CLASS   => 'chip'.$addClass,
          self::ATTR_ID      => $id,
          'data-type'        => $type,
          'data-coordX'      => $chip[self::XML_ATTRIBUTES]['coordX'],
          'data-coordY'      => $chip[self::XML_ATTRIBUTES]['coordY'],
          'data-orientation' => $orientation,
          'data-color'       => $color,
          'data-status'      => $status,
          'data-width'       => $width,
          'data-height'      => $height,
          'style'            => "background:url('/wp-content/plugins/hj-zombicide/web/rsc/img/tokens/".$tokenName.".png');",
        );
        $lstChips .= $this->getBalise(self::TAG_DIV, '', $args);
      }
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
