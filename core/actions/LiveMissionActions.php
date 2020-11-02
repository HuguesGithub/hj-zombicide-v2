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
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    // On traite ensuite soit d'une insertion, soit d'une édition
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

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    // On sauvegarde les modifications du fichier.
    $this->objXmlDocument->asXML($fileName);
    if (!empty($returned)) {
      return $returned;
    }
  }

  private function formatErrorMessage($msgError)
  {
    // TODO
    return "[[$msgError]]";

  }

  private function dealWithInsertChip()
  {
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    $this->patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
    if (isset($this->post['act'])) {
      if (preg_match($this->patternZombie, $this->post['act'], $matches)) {
        // On va insérer un Zombie.
        return $this->insertZombie();
      } elseif ($this->post['act']=='survivor') {
        // On va insérer un Survivant.
        return $this->insertSurvivor();
      }
    }
  }

  private function insertZombie()
  {
    /////////////////////////////////////////////////////////
    // On récupère l'id du prochain Zombie à insérer.
    $zombies = $this->objXmlDocument->map->zombies->attributes()['maxid'];
    $maxId = $zombies[0]+1;
    // On met à jour l'id pour la prochaine insertion.
    $this->objXmlDocument->map->zombies->attributes()['maxid'] = $maxId;
    /////////////////////////////////////////////////////////
    // On ajoute un nouveau Zombie au fichier XML
    $zombie = $this->objXmlDocument->map->zombies->addChild('zombie');
    $zombie->addAttribute('id', 'z'.$maxId);
    $zombie->addAttribute('src', $this->post['act']);
    $zombie->addAttribute('coordX', $this->post['coordx']);
    $zombie->addAttribute('coordY', $this->post['coordy']);
    $zombie->addAttribute('quantite', 1);
    /////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////
    // On restitue le visuel
    $TokenBean = new TokenBean($zombie);
    $returned = array(
      array('z'.$maxId, $TokenBean->getTokenBalise()),
      array('m'.'z'.$maxId, $TokenBean->getTokenMenu()),
    );
    return $this->jsonString($returned, 'lstElements', true);
  }











  private function updateSurvivor()
  {
    $cpt = 0;
    foreach ($this->objXmlDocument->map->survivors->survivor as $survivor) {
      if ($survivor['id'][0]==$this->id && isset($this->post['top'])) {
        // Là, on vient de juste déplacer le Survivant.
        $survivor->attributes()['coordX'] = $this->post['left'];
        $survivor->attributes()['coordY'] = $this->post['top'];
      }
      $cpt++;
    }
  }
  private function getChipReturnedJSon($chip)
  {
    $TokenBean = new TokenBean($chip);
    $returned = array(
      array($this->id, $TokenBean->getTokenBalise()),
      array('m'.$this->id, $TokenBean->getTokenMenu())
    );
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function dealWithUpdateChip()
  {
    $returned = '';
    switch (substr($this->id, 0, 1)) {
      case 'c' :
        $cpt = 0;
        foreach ($this->objXmlDocument->map->chips->chip as $chip) {
          if ($chip['id'][0]==$this->id) {
            $this->act = $this->post['act'];
            if ($this->act=='pick') {
              unset($this->objXmlDocument->map->chips->chip[$cpt]);
              continue;
            }
            // On vient de trouver la chip concernée.
            switch($chip['type'][0]) {
              case 'Door' :
                if ($this->act=='open') {
                  $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = 'Opened';
                } elseif ($this->act=='close') {
                  $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = 'Closed';
                }
                $returned = $this->getChipReturnedJSon($chip);
              break;
              case 'Exit' :
              case 'Spawn' :
                if ($this->act=='activate') {
                  $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = 'Active';
                } elseif ($this->act=='unactivate') {
                  $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = 'Unactive';
                }
                $returned = $this->getChipReturnedJSon($chip);
              break;
              case 'Objective' :
                if ($this->act=='reveal') {
                  $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = 'Unactive';
                }
                $returned = $this->getChipReturnedJSon($chip);
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
        $cpt = 0;
        foreach ($this->objXmlDocument->map->zombies->zombie as $zombie) {
          if ($zombie['id'][0]==$this->id) {
            $this->act = $this->post['act'];
            list($act, $qte) = explode('-', $this->act);
            if ($act=='pick') {
              unset($this->objXmlDocument->map->zombies->zombie[$cpt]);
              continue;
            }
            switch ($act) {
              case 'add' :
                $qte = $zombie->attributes()['quantite'] + $qte;
                $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] = $qte;
              break;
              case 'del' :
                $qte = $zombie->attributes()['quantite'] - $qte;
                $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] = $qte;
              break;
              case 'move' :
                $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['coordX'] = $this->post['left'];
                $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['coordY'] = $this->post['top'];
              break;
              default :
              break;
            }
            $returned = $this->getChipReturnedJSon($zombie);
          }
          $cpt++;
        }
      break;
      default :
      break;
    }
    return $returned;
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

}
