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
  protected $urlDirSpawns       = '/wp-content/plugins/hj-zombicide/web/rsc/img/spawns/';
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
    // On a potentiellement d'autres actions disponibles...
    if (isset($this->post['act']) && $this->post['act']=='shuffleSpawn') {
      $this->dealWithSpawnShuffle();
    } elseif (isset($this->post['act']) && $this->post['act']=='drawSpawn') {
      $returned = $this->dealWithSpawnDraw();
    } elseif (!isset($this->post['id'])) {
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
    $zombie->addAttribute('type', 'Zombie');
    $zombie->addAttribute('src', $this->post['act']);
    $zombie->addAttribute('coordX', $this->post['coordx']);
    $zombie->addAttribute('coordY', $this->post['coordy']);
    $zombie->addAttribute('quantite', 1);
    /////////////////////////////////////////////////////////
    $this->insertTchatMessage('Zombie créé');

    /////////////////////////////////////////////////////////
    // On restitue le visuel
    $TokenBean = new TokenBean($zombie);
    $returned = array(
      array('z'.$maxId, $TokenBean->getTokenBalise()),
      array('m'.'z'.$maxId, $TokenBean->getTokenMenu()),
    );
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
    $survivor->addAttribute('type', 'Survivor');
    $survivor->addAttribute('status', 'Survivor');
    $survivor->addAttribute('actionPoints', 3);
    $survivor->addAttribute('experiencePoints', 0);
    $survivor->addAttribute('level', 'Blue');
    // On va ajouter à survivor des skills.
    $skills = $survivor->addChild('skills');
    $SurvivorSkills = $Survivor->getSurvivorSkills(self::CST_SURVIVORTYPEID_S);
    while (!empty($SurvivorSkills)) {
      $SurvivorSkill = array_shift($SurvivorSkills);
      $skill = $skills->addChild('skill');
      $skill->addAttribute('id', $this->post['survivorId'].'-sk'.$SurvivorSkill->getSkillId());
      $skill->addAttribute('level', $SurvivorSkill->getBean()->getColor());
      $skill->addAttribute('unlocked', ($SurvivorSkill->getTagLevelId()<20 ? 1 : 0));
    }
    $survivor->addChild('items');
    ///////////////////////////////////////////////////////
    $this->insertTchatMessage('Survivant créé');

    /////////////////////////////////////////////////////////
    // On restitue le visuel
    $TokenBean = new TokenBean($survivor);
    $returned = array(
      array($this->post['survivorId'], $TokenBean->getTokenBalise()),
      array('m'.$this->post['survivorId'], $TokenBean->getTokenMenu()),
      array('portrait-new', $TokenBean->getTokenPortrait()),
      array('detail-new', $TokenBean->getTokenDetail()),
    );
    return $this->jsonString($returned, 'lstElements', true);
  }










  private function getColorLevel($qte)
  {
    if ($qte>=43) {
      $level = 'Red';
    } elseif ($qte>=19) {
      $level = 'Orange';
    } elseif ($qte>=7) {
      $level = 'Yellow';
    } else {
      $level = 'Blue';
    }
    return $level;
  }
  private function updateSurvivor($cpt)
  {
    list($act, $type, $qte) = explode('-', $this->act);
    switch ($act) {
      case 'add' :
        if ($type=='xp') {
          $qte = $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['experiencePoints'] + $qte;
          $level = $this->getColorLevel($qte);
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['experiencePoints'] = $qte;
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['level'] = $level;
          $this->insertTchatMessage('XP modifiés');
        } elseif ($type=='pv') {
          $qte = $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['hitPoints'] + 1;
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['hitPoints'] = $qte;
          $this->insertTchatMessage('PV modifiés');
        }
      break;
      case 'del' :
        if ($type=='xp') {
          $qte = $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['experiencePoints'] - 1;
          $level = $this->getColorLevel($qte);
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['experiencePoints'] = $qte;
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['level'] = $level;
          $this->insertTchatMessage('XP modifiés');
        } elseif ($type=='pa') {
          $qte = $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['actionPoints'] - 1;
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['actionPoints'] = $qte;
          $this->insertTchatMessage('PA modifiés');
        } elseif ($type=='pv') {
          $qte = $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['hitPoints'] - 1;
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['hitPoints'] = $qte;
          $this->insertTchatMessage('PV modifiés');
        }
      break;
      case 'init' :
        if ($type=='pa') {
          $base = ($this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['experiencePoints']>=7 ? 4 : 3);
          $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['actionPoints'] = $base;
          $this->insertTchatMessage('PA modifiés');
        }
      break;
      case 'move' :
        $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['coordX'] = $this->post['left'];
        $this->objXmlDocument->map->survivors->survivor[$cpt]->attributes()['coordY'] = $this->post['top'];
        $this->insertTchatMessage('Survivant déplacé');
      break;
      default :
      break;
    }
  }
  private function getChipReturnedJSon($chip)
  {
    $TokenBean = new TokenBean($chip);
    $returned = array(
      array($this->id, $TokenBean->getTokenBalise()),
      array('m'.$this->id, $TokenBean->getTokenMenu())
    );
    if ($chip->attributes()['type']=='Survivor') {
      $returned[] = array('portrait-'.$this->id, $TokenBean->getTokenPortrait());
      $returned[] = array('detail-survivor-'.$this->id, $TokenBean->getTokenDetail());
    }
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function getNewStatus($cpt)
  {
    $newStatus = $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'][0];
    $type = $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['type'][0];
    switch($type) {
      case 'Door' :
        if ($this->act=='open') {
          $newStatus = 'Opened';
        } elseif ($this->act=='close') {
          $newStatus = 'Closed';
        }
      break;
      case 'Exit' :
      case 'Spawn' :
        if ($this->act=='activate') {
          $newStatus = 'Active';
        } elseif ($this->act=='unactivate') {
          $newStatus = 'Unactive';
        }
      break;
      case 'Objective' :
        if ($this->act=='reveal') {
          $newStatus = 'Unactive';
        }
      break;
      default :
      break;
    }
    return $newStatus;
  }
  private function updateZombie($cpt) {
    list($act, $qte) = explode('-', $this->act);
    switch ($act) {
      case 'add' :
        $qte = $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] + $qte;
        $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] = $qte;
        $this->insertTchatMessage('Zombie ajouté');
      break;
      case 'del' :
        $qte = $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] - $qte;
        $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['quantite'] = $qte;
        $this->insertTchatMessage('Zombie retiré');
      break;
      case 'move' :
        $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['coordX'] = $this->post['left'];
        $this->objXmlDocument->map->zombies->zombie[$cpt]->attributes()['coordY'] = $this->post['top'];
        $this->insertTchatMessage('Zombie déplacé');
      break;
      default :
      break;
    }
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
              $this->insertTchatMessage('Jeton supprimé');
              continue;
            }
            $newStatus = $this->getNewStatus($cpt);
            $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['status'] = $newStatus;
            $this->insertTchatMessage('Jeton changement de statut');
            $returned = $this->getChipReturnedJSon($chip);
          }
          $cpt++;
        }
      break;
      case 's' :
        if (strpos($this->id, 'sk')!==false) {
          $obj = $this->objXmlDocument->xpath('//skill[@id="'.$this->id.'"]')[0];
          $obj->attributes()['unlocked'] = $this->post['unlocked'];
        } else {
          $cpt = 0;
          foreach ($this->objXmlDocument->map->survivors->survivor as $survivor) {
            if ($survivor['id'][0]==$this->id) {
              $this->act = $this->post['act'];
              if ($this->act=='pick') {
                unset($this->objXmlDocument->map->survivors->survivor[$cpt]);
                $this->insertTchatMessage('Survivant supprimé');
                continue;
              }
              $this->updateSurvivor($cpt);
              $returned = $this->getChipReturnedJSon($survivor);
            }
            $cpt++;
          }
        }
      break;
      case 'z' :
        // On est dans le cas d'un zombie
        $cpt = 0;
        foreach ($this->objXmlDocument->map->zombies->zombie as $zombie) {
          if ($zombie['id'][0]==$this->id) {
            $this->act = $this->post['act'];
            if ($this->act=='pick') {
              unset($this->objXmlDocument->map->zombies->zombie[$cpt]);
              $this->insertTchatMessage('Zombie supprimé');
              continue;
            }
            $this->updateZombie($cpt);
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


  private function dealWithSpawnShuffle()
  {
    $Spawns = $this->objXmlDocument->xpath('//spawns/spawn');
    shuffle($Spawns);
    $rank = 1;
    foreach ($Spawns as $Spawn) {
      $Spawn->attributes()['rank'] = $rank;
      $Spawn->attributes()['status'] = 'deck';
      $rank++;
    }
    $this->insertTchatMessage('Pioche Invasion mélangée');
  }

  private function dealWithSpawnDraw()
  {
    $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    if (empty($Spawns)) {
      $this->dealWithSpawnShuffle();
      $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    }
    usort($Spawns, 'sort_trees');
    $Spawn = $Spawns[0];
    $Spawn->attributes()['status'] = 'discard';
    $this->insertTchatMessage('1 Carte Invasion piochée');

    $Bean = new LocalBean();
    $returned = array(
      array("modalBody", $Bean->getBalise(self::TAG_IMG, '', array(self::ATTR_SRC=>$this->urlDirSpawns.$Spawn->attributes()['src'].'-thumb.jpg'))),
    );
    return $this->jsonString($returned, 'lstElements', true);
  }

  private function insertTchatMessage($msg='', $author='Automat')
  {
    $Tchat = $this->objXmlDocument->tchats->addChild('tchat', $msg);
    $Tchat->addAttribute('timestamp', time());
    $Tchat->addAttribute('author', $author);
  }



}

function sort_trees($t1, $t2) {
  return ($t1['rank']*1 > $t2['rank']*1);
}
