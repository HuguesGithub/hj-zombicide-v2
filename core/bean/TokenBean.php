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
  private $name;
  private $orientation;
  private $quantite;
  private $src;
  private $status;
  private $type;
  private $width;

  /**
   * @param Expansion $Expansion
   */
  public function __construct($chip=null)
  {
    parent::__construct();
    if (!is_array($chip)) {
      $this->color       = $chip->attributes()['color'];
      $this->coordX      = $chip->attributes()['coordX'];
      $this->coordY      = $chip->attributes()['coordY'];
      $this->id          = $chip->attributes()['id'];
      $this->orientation = $chip->attributes()['orientation'];
      $this->quantite    = $chip->attributes()['quantite'];
      $this->src         = $chip->attributes()['src'];
      $this->status      = $chip->attributes()['status'];
      $this->type        = $chip->attributes()['type'];
    } else {
      $this->color       = $chip[self::XML_ATTRIBUTES]['color'];
      $this->coordX      = $chip[self::XML_ATTRIBUTES]['coordX'];
      $this->coordY      = $chip[self::XML_ATTRIBUTES]['coordY'];
      $this->id          = $chip[self::XML_ATTRIBUTES]['id'];
      $this->orientation = $chip[self::XML_ATTRIBUTES]['orientation'];
      $this->quantite    = $chip[self::XML_ATTRIBUTES]['quantite'];
      $this->src         = $chip[self::XML_ATTRIBUTES]['src'];
      $this->status      = $chip[self::XML_ATTRIBUTES]['status'];
      $this->type        = $chip[self::XML_ATTRIBUTES]['type'];
    }
    $this->patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
    $this->init($chip);
  }

  private function init($chip)
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
      case '' :
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
//        echo "[[".$this->type."]]";
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
    $strMenu = $this->getLiMenuItem('Ouvrir', 'open', 'folder-open-o', ($this->status=='Closed' ? '' : ' disabled'));
    return $strMenu . $this->getLiMenuItem('Fermer', 'close', 'folder-o', ($this->status=='Opened' ? '' : ' disabled'));
  }
  private function getObjectiveMenu()
  {
    // On peut vouloir révéler ou prendre un Objectif
    $strMenu = $this->getLiMenuItem('Révéler', 'reveal', 'share-square-o', ($this->status=='Unveiled' ? '' : ' disabled'));
    return $strMenu . $this->getLiMenuItem('Prendre', 'pick', 'check-square-o', ($this->status=='Unactive' ? '' : ' disabled'));
  }
  private function getExitMenu()
  {
    $strMenu  = $this->getLiMenuItem('Activer', 'activate', 'thumbs-o-up', ($this->status=='Unactive' ? '' : ' disabled'));
    return $strMenu . $this->getLiMenuItem('Désactiver', 'unactivate', 'thumbs-o-down', ($this->status=='Active' ? '' : ' disabled'));
  }
  private function getSpawnMenu()
  {
    // On peut vouloir activer ou désactiver un Spawn.
    $strMenu  = $this->getLiMenuItem('Activer', 'activate', 'thumbs-o-up', ($this->status=='Unactive' ? '' : ' disabled'));
    $strMenu .= $this->getLiMenuItem('Désactiver', 'unactivate', 'thumbs-o-down', ($this->status=='Active' ? '' : ' disabled'));
    $strMenu .= $this->getLiMenuSeparator();
    // On peut vouloir le retirer du plateau. On peut vouloir le déplacer.
    $strMenu .= $this->getLiMenuItem('Déplacer', 'move', 'arrows-alt', ' disabled');
    $strMenu .= $this->getLiMenuItem('Supprimer', 'pick', 'trash');
    $strMenu .= $this->getLiMenuSeparator();
    // On peut v ouloir ajouter des Zombies.
    $subsubMenu  = '<li class="menu-item" data-menu-action="zWalkerStandard"><button type="button" class="menu-btn"> <span class="menu-text">Walker</span> </button></li>';
    $subsubMenu .= '<li class="menu-item" data-menu-action="zFattyStandard"><button type="button" class="menu-btn"> <span class="menu-text">Fatty</span> </button></li>';
    $subsubMenu .= '<li class="menu-item" data-menu-action="zRunnerStandard"><button type="button" class="menu-btn"> <span class="menu-text">Runner</span> </button></li>';
    $subsubMenu .= '<li class="menu-item" data-menu-action="zAbominationStandard"><button type="button" class="menu-btn"> <span class="menu-text">Abomination</span> </button></li>';
    $subMenu = '<li class="menu-item submenu"><button type="button" class="menu-btn"> <span class="menu-text">Standard</span> </button><menu class="menu">'.$subsubMenu.'</menu></li>';
    $strMenu .='<li class="menu-item submenu'.($this->status=='Active' ? '' : ' disabled').'"><button type="button" class="menu-btn"> <i class="fa fa-user-plus"></i> <span class="menu-text">Spawner</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    return $strMenu;
  }
  private function getZombieMenu()
  {
    // On peut ajouter des Zombies
    $subMenu  = '';
    for ($i=1; $i<=5; $i++) {
      $subMenu .= '<li class="menu-item" id="'.$this->id.'" data-menu-action="add-'.$i.'"><button type="button" class="menu-btn"> <span class="menu-text">'.$i.'</span> </button></li>';
    }
    $strMenu  ='<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-plus-circle"></i> <span class="menu-text">Ajouter</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    // On peut enlever des Zombies
    $subMenu  = '<li class="menu-item" id="'.$this->id.'" data-menu-action="pick"><button type="button" class="menu-btn"> <span class="menu-text">Tous</span> </button></li>';
    if ($this->quantite>1) {
      $subMenu .= $this->getLiMenuSeparator();
      for ($i=1; $i<min(6, $this->quantite); $i++) {
        $subMenu .= '<li class="menu-item" id="'.$this->id.'" data-menu-action="del-'.$i.'"><button type="button" class="menu-btn"> <span class="menu-text">'.$i.'</span> </button></li>';
      }
    }
    $strMenu .='<li class="menu-item submenu"><button type="button" class="menu-btn"> <i class="fa fa-minus-circle"></i> <span class="menu-text">Retirer</span> </button><menu class="menu">'.$subMenu.'</menu></li>';
    // On peut déplacer des Zombies
    $strMenu .= $this->getLiMenuSeparator();
    return $strMenu . $this->getLiMenuItem('Déplacer', 'move', 'arrows-alt', ' disabled');
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
      default :
      break;
    }

    return $this->getBalise('menu', $returned, array(self::ATTR_CLASS=>'menu', self::ATTR_ID=>'m'.$this->id));
  }

}
