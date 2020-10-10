<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * LiveMissionActions
 * @author Hugues
 * @since 1.10.06
 * @version 1.10.06
 */
class LiveMissionActions extends LocalActions
{
  protected $urlDirLiveMissions = 'web/rsc/missions/live/';
  protected $strTokenStyle      = 'background:url("/wp-content/plugins/hj-zombicide/web/rsc/img/tokens/%1$s.png");';
  protected $chipToken          = 'chip token';
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->SurvivorServices = new SurvivorServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new LiveMissionActions($post);
    if ($post[self::CST_AJAXACTION]=='updateLiveMission') {
      $returned = $Act->dealWithUpdateLiveMission();
    } else {
      $returned = '';
    }
    return $returned;
  }

  private function buildChipToken($classe, $chip, $width, $height, $style)
  {
    $args = array(
      self::ATTR_CLASS  => $classe,
      self::ATTR_ID     => $this->id,
      'data-width'      => $width,
      'data-height'     => $height,
      'style'           => $style,
    );
    if (isset($chip['type'][0])) {
      $args['data-type'] = $chip['type'][0];
    }
    if (isset($chip['coordX'][0])) {
      $args['data-coordx'] = $chip['coordX'][0];
    }
    if (isset($chip['coordY'][0])) {
      $args['data-coordy'] = $chip['coordY'][0];
    }
    if (isset($chip['orientation'][0])) {
      $args['data-orientation'] = $chip['orientation'][0];
    }
    if (isset($chip['color'][0])) {
      $args['data-color'] = $chip['color'][0];
    }
    if (isset($chip['status'][0])) {
      $args['data-status'] = $chip['status'][0];
    }
    $Bean = new LocalBean();
    $returned = array($this->id, $Bean->getPublicBalise(self::TAG_DIV, '', $args));
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function updateDoor($chip)
  {
    $chip->attributes()['status'] = $this->status;
    $tokenName = 'door_'.strtolower($chip['color'][0]).'_'.strtolower($this->status);
    $classe = $this->chipToken.' '.$chip['orientation'][0];
    $style = sprintf($this->strTokenStyle, $tokenName);
    return $this->buildChipToken($classe, $chip, 56, 56, $style);
  }
  private function updateExit($chip)
  {
    $chip->attributes()['status'] = $this->status;
    $tokenName = 'exit';
    $classe = $this->chipToken.' '.$chip['orientation'][0].' '.strtolower($this->status);
    $style = sprintf($this->strTokenStyle, $tokenName);
    return $this->buildChipToken($classe, $chip, 100, 50, $style);
  }
  private function updateObjective($chip)
  {
    $chip->attributes()['status'] = $this->status;
    $tokenName = 'objective_'.strtolower($chip['color'][0]);
    $classe = $this->chipToken;
    $style = sprintf($this->strTokenStyle, $tokenName);
    return $this->buildChipToken($classe, $chip, 50, 50, $style);
  }
  private function updateSpawn($chip)
  {
    $chip->attributes()['status'] = $this->status;
    $tokenName = 'spawn_'.strtolower($chip['color'][0]);
    $classe = $this->chipToken.' '.$chip['orientation'][0].' '.strtolower($this->status);
    $style = sprintf($this->strTokenStyle, $tokenName);
    return $this->buildChipToken($classe, $chip, 100, 50, $style);
  }
  private function updateSurvivor()
  {
    $cpt = 0;
    foreach ($this->objXmlDocument->map->survivors->survivor as $survivor) {
      if ($survivor['id'][0]==$this->id) {
        if (isset($this->post['top'])) {
          // Là, on vient de juste déplacer le Survivant.
          $survivor->attributes()['coordX'] = $this->post['left'];
          $survivor->attributes()['coordY'] = $this->post['top'];
        }
      }
      $cpt++;
    }
  }
  private function updateZombie()
  {
    $cpt = 0;
    foreach ($this->objXmlDocument->map->zombies->zombie as $zombie) {
      if ($zombie['id'][0]==$this->id) {
        if (isset($this->post['top'])) {
          // Là, on vient de juste déplacer le Zombie.
          $zombie->attributes()['coordX'] = $this->post['left'];
          $zombie->attributes()['coordY'] = $this->post['top'];
        } elseif (isset($this->post['quantity'])) {
          $qty = $this->post['quantity'];
          // Là, on vient de modifier le nombre de Zombies
          if ($qty==0) {
            // La pile est vide, on la supprime du fichier XML
            unset($this->objXmlDocument->map->zombies->zombie[$cpt]);
          } else {
            $zombie->attributes()['quantite'] = $qty;
          }
        }
      }
      $cpt++;
    }
  }
  private function dealWithUpdateChip()
  {
    switch (substr($this->id, 0, 1)) {
      case 'c' :
        $cpt = 0;
        foreach ($this->objXmlDocument->map->chips->chip as $chip) {
          if ($chip['id'][0]==$this->id) {
            $this->status = $this->post['status'];
            if ($this->status=='Picked') {
              unset($this->objXmlDocument->map->chips->chip[$cpt]);
              continue;
            }
            // On vient de trouver la chip concernée.
            switch($chip['type'][0]) {
              case 'Door' :
                return $this->updateDoor($chip);
              break;
              case 'Exit' :
                return $this->updateExit($chip);
              break;
              case 'Objective' :
                return $this->updateObjective($chip);
              break;
              case 'Spawn' :
                return $this->updateSpawn($chip);
              break;
              default :
              break;
            }
          }
          $cpt++;
        }
      break;
      case 's' :
        // On est dans le cas d'un Survivant
        $this->updateSurvivor();
      break;
      case 'z' :
        // On est dans le cas d'un zombie
        $this->updateZombie();
      break;
      default :
      break;
    }
  }
  private function insertZombie($matches)
  {
    /////////////////////////////////////////////////////////
    // On récupère l'id du prochain Zombie à insérer.
    $zombies = $this->objXmlDocument->map->zombies->attributes()['maxid'];
    $maxId = $zombies[0]+1;
    // On met à jour l'id pour la prochaine insertion.
    $this->objXmlDocument->map->zombies->attributes()['maxid'] = $maxId;
    // On ajoute un nouveau Zombie au fichier XML
    $zombie = $this->objXmlDocument->map->zombies->addChild('zombie');
    $zombie->addAttribute('id', 'z'.$maxId);
    $zombie->addAttribute('src', $this->post['type']);
    $zombie->addAttribute('coordX', $this->post['coordx']);
    $zombie->addAttribute('coordY', $this->post['coordy']);
    $zombie->addAttribute('quantite', 1);
    /////////////////////////////////////////////////////////

    $Bean = new LocalBean();
    /////////////////////////////////////////////////////////
    // On prépare le Template pour retourner le visuel à afficher.
    $args = array(
      self::ATTR_SRC    => '/wp-content/plugins/hj-zombicide/web/rsc/img/zombies/'.$this->post['type'].'.png',
      // TODO : Construction du Title à améliorer plus tard quand on aura des trucs un peu plus spécifique.
      // Notamment pour les Crawlers, Skinners... Dont les noms ne sont pas composés.
      // Faudra aussi reprendre le pattern.
      self::ATTR_TITLE  => $matches[1].' '.$matches[2].' x1',
    );
    $content  = $Bean->getPublicBalise(self::TAG_IMG, '', $args);
    $content .= $Bean->getPublicBalise(self::TAG_DIV, 1, array(self::ATTR_CLASS=>'badge'));
    $args = array(
      self::ATTR_CLASS  => 'chip zombie '.$matches[2],
      self::ATTR_ID     => 'z'.$maxId,
      'data-type'       => 'Zombie',
      'data-coordx'     => $this->post['coordx'],
      'data-coordy'     => $this->post['coordy'],
      'data-width'      => 50,
      'data-height'     => 50,
      'data-quantity'   => 1,
    );
    $returned = array('z'.$maxId, $Bean->getPublicBalise(self::TAG_DIV, $content, $args));
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function insertSurvivor()
  {
    ///////////////////////////////////////////////////////
    // On ajoute un nouveau Survivant au fichier XML
    $survivor = $this->objXmlDocument->map->survivors->addChild('survivor');
    $survivor->addAttribute('id', $this->post['survivorId']);
    $survivorId = substr($this->post['survivorId'], 1);
    $Survivor = $this->SurvivorServices->selectSurvivor($survivorId);
    $usedName = ($Survivor->getAltImgName()!='' ? $Survivor->getAltImgName() : $Survivor->getName());
    $src = 'p'.$Survivor->getNiceName($usedName);
    $survivor->addAttribute('src', $src);
    $survivor->addAttribute('coordX', 975);
    $survivor->addAttribute('coordY', 475);
    $survivor->addAttribute('hitPoints', 2);
    $survivor->addAttribute('status', 'Survivor');
    $survivor->addAttribute('actionPoints', 3);
    $survivor->addAttribute('experiencePoints', 0);
    $survivor->addAttribute('level', 'Blue');
    ///////////////////////////////////////////////////////

    $Bean = new LocalBean();
    /////////////////////////////////////////////////////////
    // On prépare le Template pour retourner le visuel à afficher.
    $args = array(
      self::ATTR_SRC    => '/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/'.$src.'.jpg',
    );
    $content  = $Bean->getPublicBalise(self::TAG_IMG, '', $args);
    $args = array(
      self::ATTR_CLASS  => 'chip survivor Blue',
      self::ATTR_ID     => $this->post['survivorId'],
      'data-type'       => 'Survivor',
      'data-coordx'     => 975,
      'data-coordy'     => 475,
      'data-width'      => 50,
      'data-height'     => 50
    );
    $returned = array($this->post['survivorId'], $Bean->getPublicBalise(self::TAG_DIV, $content, $args));
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function dealWithInsertChip()
  {
    $this->patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
    if (isset($this->post['type'])) {
      if (preg_match($this->patternZombie, $this->post['type'], $matches)) {
        // On va insérer un Zombie.
        return $this->insertZombie($matches);
      } elseif ($this->post['type']=='survivor') {
        // On va insérer un Survivant.
        return $this->insertSurvivor();
      }
    }
  }
  private function formatErrorMessage($msgError)
  {
    // TODO
    return "[[$msgError]]";

  }
  /**
   * @return string
   */
  public function dealWithUpdateLiveMission()
  {
    $returned = '';
    ////////////////////////////////////////////////////////////////////////
    // On récupère et vérifie les données
    if (!isset($this->post['uniqid'])) {
      return $this->formatErrorMessage('Identifiant fichier non défini.');
    }
    $this->fileId = $this->post['uniqid'];
    $fileName = PLUGIN_PATH.$this->urlDirLiveMissions.$this->fileId.".mission.xml";
    if (!is_file($fileName)) {
      return $this->formatErrorMessage('Le fichier de sauvegarde n\'existe pas.');
    }
    $this->objXmlDocument = simplexml_load_file($fileName);
    if (!isset($this->post['id'])) {
      // Ici, on gère un ajout
      $returned = $this->dealWithInsertChip();
    } elseif (!empty($this->post['id'])) {
      // Ici, on gère un update
      $this->id = $this->post['id'];
      $returned = $this->dealWithUpdateChip();
    }
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    $this->objXmlDocument->asXML($fileName);
    if (!empty($returned)) {
      return $returned;
    }
  }

}
