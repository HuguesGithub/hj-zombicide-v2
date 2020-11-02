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

  private function init()
  {
    $this->baliseContent = '';
    switch ($this->type) {
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
  private function getLiMenuItem($label, $act, $iCode, $disabled='')
  {
    $span   = $this->getBalise(self::TAG_SPAN, $label, array(self::ATTR_CLASS=>'menu-text'));
    $i = $this->getBalise(self::TAG_I, '', array(self::ATTR_CLASS=>'fa fa-'.$iCode));
    $button = $this->getBalise(self::TAG_BUTTON, $i.$span, array(self::ATTR_TYPE=>'button', self::ATTR_CLASS=>'menu-btn'));
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item'.$disabled,
      self::ATTR_ID      => $this->id,
      'data-menu-action' => $act
    );
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
    $args = array(($this->status=='Active' ? '' : ' '.self::CST_DISABLED));
    return $strMenu . $this->getRender($this->urlMenuZombiesTemplate, $args);
  }
  private function getSurvivorMenu()
  {
    $strButton = '<button type="button" class="menu-btn"> <span class="menu-text">%1$s</span> </button>';
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item',
      self::ATTR_ID      => $this->id,
    );
    // On peut ajouter des Zombies
    $subMenu  = '';
    for ($i=1; $i<=5; $i++) {
      $argsLi['data-menu-action'] = 'add-xp-'.$i;
      $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
    }
    $strMenu  ='<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-plus-circle"></i> <span class="menu-text">Ajouter XP</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    $strMenu .= $this->getLiMenuItem('Retirer 1 XP', 'del-xp', 'minus-circle');
    $strMenu .= $this->getLiMenuSeparator();
    $strMenu .= $this->getLiMenuItem('Reinitialiser PA', 'init-pa', 'undo');
    $strMenu .= $this->getLiMenuItem('Retirer 1 PA', 'del-pa', 'minus-circle');
    $strMenu .= $this->getLiMenuSeparator();
    $strMenu .= $this->getLiMenuItem('Ajouter 1 PV', 'add-pv', 'plus-circle');
    $strMenu .= $this->getLiMenuItem('Retirer 1 PV', 'del-pv', 'minus-circle');
    $strMenu .= $this->getLiMenuSeparator();
    return $strMenu . $this->getLiMenuItem('Supprimer', 'pick', 'trash');
  }
  private function getZombieMenu()
  {
    $strButton = '<button type="button" class="menu-btn"> <span class="menu-text">%1$s</span> </button>';
    $argsLi = array(
      self::ATTR_CLASS   => 'menu-item',
      self::ATTR_ID      => $this->id,
    );
    // On peut ajouter des Zombies
    $subMenu  = '';
    for ($i=1; $i<=5; $i++) {
      $argsLi['data-menu-action'] = 'add-'.$i;
      $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
    }
    $strMenu  ='<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-plus-circle"></i> <span class="menu-text">Ajouter</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    // On peut enlever des Zombies
    $argsLi['data-menu-action'] = 'pick';
    $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
    if ($this->quantite>1) {
      $subMenu .= $this->getLiMenuSeparator();
      for ($i=1; $i<min(6, $this->quantite); $i++) {
        $argsLi['data-menu-action'] = 'del-'.$i;
        $subMenu .= $this->getBalise(self::TAG_LI, sprintf($strButton, $i), $argsLi);
      }
    }
    $strMenu .='<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-minus-circle"></i> <span class="menu-text">Retirer</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    // On peut déplacer des Zombies
    $strMenu .= $this->getLiMenuSeparator();
    return $strMenu . $this->getLiMenuItem('Déplacer', 'move', 'arrows-alt', ' '.self::CST_DISABLED);
  }
  public function getTokenMenu()
  {
    switch ($this->type) {
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
    $id = substr($this->id, 1);
    $Survivor = $this->SurvivorServices->selectSurvivor($id);
    $skills = $this->chip['skills']['skill'];
    $strSkills = '';
    while (!empty($skills)) {
      $skill = array_shift($skills);
      $id = $skill['@attributes']['id'];
      list($sId, $skId) = explode('-', $id);
      $skillId = substr($skId, 2);
      $Skill = $this->SkillServices->selectSkill($skillId);
      $level = strtolower($skill['@attributes']['level']);
      $unlocked = ($skill['@attributes']['unlocked']==1);
      $strSkills .= '<li id="'.$id.'" class="'.(!$unlocked ? 'disabled' : '').'"><span class="badge badge-'.$level.'-skill">'.$Skill->getName().'</span></li>';

    }
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
