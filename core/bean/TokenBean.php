<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TokenBean
 * @author Hugues
 * @since 1.11.01
 * @version 1.11.01
 */
class TokenBean extends LocalBean
{
  protected $urlDirLiveMissions = '/web/rsc/missions/live/';

  private $addClass = ' token';
  private $color;
  private $coordX;
  private $coordY;
  private $height;
  private $id;
  private $level;
  private $name;
  private $orientation;
  private $quantite;
  private $src;
  private $status;
  private $type;
  private $width;

  private $urlMenuZombiesTemplate = 'web/pages/public/fragments/menu-zombies-creation.php';
  private $urlOnlineDetailSurvivor = 'web/pages/public/fragments/online-detail-survivor.php';

  private $arrTagColors = array(1=>'blue', 2=>'yellow', 3=>'orange', 4=>'red');

  /**
   * @param Expansion $Expansion
   */
  public function __construct($chip=null)
  {
    parent::__construct();
    $this->SurvivorServices = new SurvivorServices();
    $this->SkillServices = new SkillServices();

    if (!is_array($chip)) {
      $this->color       = $chip->attributes()['color'];
      $this->coordX      = $chip->attributes()['coordX'];
      $this->coordY      = $chip->attributes()['coordY'];
      $this->id          = $chip->attributes()['id'];
      $this->level       = $chip->attributes()['level'];
      $this->orientation = $chip->attributes()['orientation'];
      $this->quantite    = $chip->attributes()['quantite'];
      $this->src         = $chip->attributes()['src'];
      $this->status      = $chip->attributes()['status'];
      $this->type        = $chip->attributes()['type'];
      $this->experiencePoints = $chip->attributes()['experiencePoints'];
      $this->actionPoints = $chip->attributes()['actionPoints'];
      $this->hitPoints   = $chip->attributes()['hitPoints'];
    } else {
      $this->color       = $chip[self::XML_ATTRIBUTES]['color'];
      $this->coordX      = $chip[self::XML_ATTRIBUTES]['coordX'];
      $this->coordY      = $chip[self::XML_ATTRIBUTES]['coordY'];
      $this->id          = $chip[self::XML_ATTRIBUTES]['id'];
      $this->level       = $chip[self::XML_ATTRIBUTES]['level'];
      $this->orientation = $chip[self::XML_ATTRIBUTES]['orientation'];
      $this->quantite    = $chip[self::XML_ATTRIBUTES]['quantite'];
      $this->src         = $chip[self::XML_ATTRIBUTES]['src'];
      $this->status      = $chip[self::XML_ATTRIBUTES]['status'];
      $this->type        = $chip[self::XML_ATTRIBUTES]['type'];
      $this->experiencePoints = $chip[self::XML_ATTRIBUTES]['experiencePoints'];
      $this->actionPoints = $chip[self::XML_ATTRIBUTES]['actionPoints'];
      $this->hitPoints   = $chip[self::XML_ATTRIBUTES]['hitPoints'];
    }
    $this->chip = $chip;
    $this->patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
    $this->init();
  }



  public function getJsonModifications($id, $bln_create)
  {
    // On retourne Le Token et son menu
    $args = array(
      // On veut retourner le Tag mis à jour.
      array($id, $this->getTokenBalise()),
      // On veut retourner le Menu associé au Tag mis à jour.
      array('m'.$id, $this->getTokenMenu()),
    );
    // Eventuellement, on ajoute pour certains trucs spéciaux.
    if ($this->type=='Survivor') {
      $args[] = array('portrait-'.($bln_create ? 'new' : $id), $this->getTokenPortrait());
      $args[] = array('detail-survivor-'.($bln_create ? 'new' : $id), $this->getTokenDetail());
    }
    // Puis on retourne le tout
    return $args;
  }






  private function init()
  {
    $this->baliseContent = '';
    switch ($this->type) {
      case 'Noise' :
        $this->width     = 55;
        $this->height    = 50;
        $this->addClass .= ' noise';
        $this->name      = 'noise';
        $this->baliseContent = $this->getBalise(self::TAG_DIV, $this->quantite, array(self::ATTR_CLASS=>'badge'));
      break;
      case 'Door' :
        $this->width     = 56;
        $this->height    = 56;
        $this->name      = 'door_'.strtolower($this->color).'_'.strtolower($this->status);
        $this->addClass .= ' '.$this->orientation;
      break;
      case 'Objective' :
        $this->width     = 50;
        $this->height    = 50;
        $this->name      = 'objective_'.($this->status=='Unveiled' ? 'red' : strtolower($this->color));
      break;
      case 'Spawn' :
        $this->width     = 100;
        $this->height    = 50;
        $this->name      = 'spawn_'.strtolower($this->color);
        $this->addClass .= ' '.$this->orientation.' '.strtolower($this->status);
      break;
      case 'Exit' :
        $this->width     = 100;
        $this->height    = 50;
        $this->name      = 'exit';
        $this->addClass .= ' '.$this->orientation.($this->status=='Unactive' ? ' unactive' : '');
      break;
      case 'Survivor' :
        $this->width     = 50;
        $this->height    = 50;
        $this->addClass .= ' survivor '.$this->level;
        $args = array(
          self::ATTR_SRC=>'/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/'.$this->src.'.jpg'
        );
        $this->baliseContent  = $this->getBalise(self::TAG_IMG, '', $args);
      break;
      case 'Zombie' :
        if (preg_match($this->patternZombie, $this->src, $matches)) {
          $strName       = $matches[1].' '.$matches[2];
        } else {
          $strName       = 'Pattern foireux ('.$this->src.')';
        }
        $this->width     = 50;
        $this->height    = 50;
        $this->type      = 'Zombie';
        $this->addClass .= ' zombie '.$matches[2];
        $args = array(
          self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/zombies/'.$this->src.'.png',
          self::ATTR_TITLE => $strName.' x'.$this->quantite,
        );
        $this->baliseContent  = $this->getBalise(self::TAG_IMG, '', $args);
        $this->baliseContent .= $this->getBalise(self::TAG_DIV, $this->quantite, array(self::ATTR_CLASS=>'badge'));
      break;
      default :
        $this->name      = '';
      break;
    }
  }

  public function getTokenBalise()
  {
    if ($this->status=='Picked') {
      return '';
    }
    $args = array(
      self::ATTR_CLASS   => 'chip'.$this->addClass,
      'data-color'       => $this->color,
      'data-coordX'      => $this->coordX,
      'data-coordY'      => $this->coordY,
      'data-height'      => $this->height,
      self::ATTR_ID      => $this->id,
      'data-orientation' => $this->orientation,
      'data-status'      => $this->status,
      'data-type'        => $this->type,
      'data-width'       => $this->width,
    );
    if (!empty($this->name)) {
      $args['style'] = "background:url('/wp-content/plugins/hj-zombicide/web/rsc/img/tokens/".$this->name.".png');";
    }

    return $this->getBalise(self::TAG_DIV, $this->baliseContent, $args);
  }

  private function getLiMenuSeparator()
  { return $this->getBalise(self::TAG_LI, '', array(self::ATTR_CLASS=>'menu-separator')); }
  private function getLiMenuItem($label, $act, $iCode, $disabled='', $type='')
  {
    $span   = $this->getBalise(self::TAG_SPAN, $label, array(self::ATTR_CLASS=>'menu-text'));
    $i = $this->getBalise(self::TAG_I, '', array(self::ATTR_CLASS=>'fa fa-'.$iCode));
    $button = $this->getBalise(self::TAG_BUTTON, $i.$span, array(self::ATTR_TYPE=>'button', self::ATTR_CLASS=>'menu-btn'));
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item'.$disabled,
      self::ATTR_ID      => $this->id,
      'data-menu-action' => $act
    );
    if ($type!='') {
      $argsLi['data-quantite'] = 1;
      $argsLi['data-type'] = $type;
    }
    return $this->getBalise(self::TAG_LI, $button, $argsLi);
  }
  private function getDoorMenu()
  {
    // On peut vouloir ouvrir ou fermer une porte.
    $strMenu = $this->getLiMenuItem('Ouvrir', 'open', 'folder-open-o', ($this->status=='Closed' ? '' : ' '.self::CST_DISABLED));
    return $strMenu . $this->getLiMenuItem('Fermer', 'close', 'folder-o', ($this->status=='Opened' ? '' : ' '.self::CST_DISABLED));
  }
  private function getObjectiveMenu()
  {
    // On peut vouloir révéler ou prendre un Objectif
    $strMenu = $this->getLiMenuItem('Révéler', 'reveal', 'share-square-o', ($this->status=='Unveiled' ? '' : ' '.self::CST_DISABLED));
    return $strMenu . $this->getLiMenuItem('Prendre', 'pick', 'check-square-o', ($this->status=='Unactive' ? '' : ' '.self::CST_DISABLED));
  }
  private function getExitMenu()
  {
    $strMenu  = $this->getLiMenuItem('Activer', 'activate', 'thumbs-o-up', ($this->status=='Unactive' ? '' : ' '.self::CST_DISABLED));
    return $strMenu . $this->getLiMenuItem('Désactiver', 'unactivate', 'thumbs-o-down', ($this->status=='Active' ? '' : ' '.self::CST_DISABLED));
  }
  private function getSpawnMenu()
  {
    // On peut vouloir activer ou désactiver un Spawn.
    $strMenu  = $this->getLiMenuItem('Activer', 'activate', 'thumbs-o-up', ($this->status=='Unactive' ? '' : ' '.self::CST_DISABLED));
    $strMenu .= $this->getLiMenuItem('Désactiver', 'unactivate', 'thumbs-o-down', ($this->status=='Active' ? '' : ' '.self::CST_DISABLED));
    $strMenu .= $this->getLiMenuSeparator();
    // On peut vouloir le retirer du plateau. On peut vouloir le déplacer.
    $strMenu .= $this->getLiMenuItem('Déplacer', 'move', 'arrows-alt', ' '.self::CST_DISABLED);
    $strMenu .= $this->getLiMenuItem('Supprimer', 'pick', 'trash');
    $strMenu .= $this->getLiMenuSeparator();
    // On peut vouloir ajouter des Zombies.
    $strMenu .= $this->getLiMenuItem('Piocher', 'draw', 'stack-overflow', ($this->status=='Active' ? '' : ' '.self::CST_DISABLED), 'Spawn');
    $strMenu .= $this->getLiMenuItem('Mélanger', 'shuffle', 'recycle', ($this->status=='Active' ? '' : ' '.self::CST_DISABLED), 'Spawn');
    $args = array(($this->status=='Active' ? '' : ' '.self::CST_DISABLED));
    return $strMenu . $this->getRender($this->urlMenuZombiesTemplate, $args);
  }
  private function getLiSubMenu($faClass, $label, $content)
  {
    return '<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-'.$faClass.'"></i> <span class="menu-text">'.$label.'</span> </button><menu class="menu">'.$content.'</menu></li>';
  }
  private function getSurvivorMenu()
  {
    $strButton = '<button type="button" class="menu-btn"> <span class="menu-text">%1$s</span> </button>';
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item',
      self::ATTR_ID      => $this->id,
      'data-menu-action' => 'add',
    );
    // On peut ajouter des Zombies
    $subMenu  = '';
    for ($i=1; $i<=5; $i++) {
      $argsLi['data-quantite'] = $i;
      $argsLi['data-type'] = 'xp';
      $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
    }
    $strMenu  = $this->getLiSubMenu('plus-circle', 'Ajouter XP', $subMenu);
    $strMenu .= $this->getLiMenuItem('Retirer 1 XP', 'del', 'minus-circle', ($this->experiencePoints!=0 ? '' : ' '.self::CST_DISABLED), 'xp');
    $strMenu .= $this->getLiMenuSeparator();
    $strMenu .= $this->getLiMenuItem('Ajouter 1 PA', 'add', 'plus-circle', '', 'pa');
    $strMenu .= $this->getLiMenuItem('Retirer 1 PA', 'del', 'minus-circle', ($this->actionPoints!=0 ? '' : ' '.self::CST_DISABLED), 'pa');
    $strMenu .= $this->getLiMenuSeparator();
    $strMenu .= $this->getLiMenuItem('Ajouter 1 PV', 'add', 'plus-circle', '', 'pv');
    $strMenu .= $this->getLiMenuItem('Retirer 1 PV', 'del', 'minus-circle', ($this->hitPoints!=0 ? '' : ' '.self::CST_DISABLED), 'pv');
    $strMenu .= $this->getLiMenuSeparator();
    return $strMenu . $this->getLiMenuItem('Supprimer', 'pick', 'trash');
  }
  private function getBruitMenu()
  { return $this->getZombieMenu(); }
  private function getZombieMenu()
  {
    $strButton = '<button type="button" class="menu-btn"> <span class="menu-text">%1$s</span> </button>';
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item',
      self::ATTR_ID      => $this->id,
      'data-menu-action' => 'add',
    );
    // On peut ajouter des Zombies
    $subMenu  = '';
    // De 1 à 5
    for ($i=1; $i<=5; $i++) {
      $argsLi['data-quantite'] = $i;
      $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
    }
    $strMenu  = $this->getLiSubMenu('plus-circle', 'Ajouter', $subMenu);
    // On peut enlever des Zombies
    // Tous d'un coup
    $argsLi['data-menu-action'] = 'pick';
    unset($argsLi['quantite']);
    $subMenu  = $this->getBalise(self::TAG_LI, sprintf($strButton, 'Tous'), $argsLi);
    // Ou de 1 à 5 ou 1 de moins que le nombre disponible.
    $argsLi['data-menu-action'] = 'del';
    if ($this->quantite>1) {
      $subMenu .= $this->getLiMenuSeparator();
      for ($i=1; $i<min(6, $this->quantite); $i++) {
        $argsLi['data-quantite'] = $i;
        $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
      }
    }
    $strMenu .= $this->getLiSubMenu('minus-circle', 'Retirer', $subMenu);
    // On peut déplacer des Zombies
    $strMenu .= $this->getLiMenuSeparator();
    return $strMenu . $this->getLiMenuItem('Déplacer', 'move', 'arrows-alt', ' '.self::CST_DISABLED);
  }
  public function getTokenMenu()
  {
    switch ($this->type) {
      case 'Noise' :
        $returned = $this->getBruitMenu();
      break;
      case 'Door' :
        $returned = $this->getDoorMenu();
      break;
      case 'Objective' :
        $returned = $this->getObjectiveMenu();
      break;
      case 'Spawn' :
        $returned = $this->getSpawnMenu();
      break;
      case 'Exit' :
        $returned = $this->getExitMenu();
      break;
      case 'Zombie' :
        $returned = $this->getZombieMenu();
      break;
      case 'Survivor' :
        $returned = $this->getSurvivorMenu();
      break;
      default :
        $returned = 'Bad Type in getTokenMenu : ['.$this->type.'].';
      break;
    }

    return $this->getBalise('menu', $returned, array(self::ATTR_CLASS=>'menu', self::ATTR_ID=>'m'.$this->id));
  }

  public function getTokenPortrait()
  {
    // Retourne le portrait que l'on veut afficher en haut à droite de la sidebar.
    $args = array(
      self::ATTR_ID    => 'portrait-'.$this->id,
      self::ATTR_CLASS => 'known',
      self::ATTR_SRC   => '/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/p'.$this->src.'.jpg',
      self::ATTR_TITLE => '',
    );
    return $this->getBalise(self::TAG_IMG, '', $args);
  }
  public function getTokenDetail()
  {
    $fileName = PLUGIN_PATH.$this->urlDirLiveMissions.$_SESSION['zombieKey'].".mission.xml";
    $this->objXmlDocument = simplexml_load_file($fileName);

    // On récupère l'id et le Survivor associé
    $survivorId = substr($this->id, 1);
    $Survivor = $this->SurvivorServices->selectSurvivor($survivorId);
    // On récupère les Skills stockés dans le fichier
    $skills = $this->objXmlDocument->xPath('//survivor[@id="'.$this->id.'"]/skills/skill');
    $strSkills = '';
    while (!empty($skills)) {
      $skill = array_shift($skills);
      // On récupère l'id du Skill pour aller chercher le nom en base.
      $skillId = $skill->attributes()[self::FIELD_SKILLID];
      $Skill = $this->SkillServices->selectSkill($skillId);
      // On récupère son Id
      $nodeId = $skill->attributes()[self::FIELD_ID];
      list(, $tagLevel) = explode('-', $nodeId);
      $skillColor = $this->arrTagColors[$tagLevel/10];
      // On récupère le Status
      $skillStatus = $skill->attributes()['status'];
      // On construit les tags HTML
      $spanBadge = $this->getBalise(self::TAG_SPAN, $Skill->getName(), array(self::ATTR_CLASS=>'badge badge-'.$skillColor.'-skill'));
      // Et on stack la liste de Skills
      $argsLi = array(
        self::ATTR_ID=>$nodeId,
        self::ATTR_CLASS=>($skillStatus=='Unactive' ? 'disabled' : '')
      );
      $strSkills .= $this->getBalise(self::TAG_LI, $spanBadge, $argsLi);
    }
    // On enrichit le Template et on retourne l'ensemble.
    $args = array(
      // Le rang du Survivant dans la partie
      $this->id,
      // L'url du portrait
      $Survivor->getPortraitUrl(),
      // Le nom du Survivant
      $Survivor->getName(),
      // Niveau du Survivant
      strtolower($this->level),
      // Nombre d'XP - 5
      strtolower($this->experiencePoints),
      // Nombre de PA - 6
      strtolower($this->actionPoints),
      // Nombre de PV - 7
      strtolower($this->hitPoints),
      // Les Compétences du Survivant - 8
      $strSkills
    );
    return $this->getRender($this->urlOnlineDetailSurvivor, $args);
  }
}
