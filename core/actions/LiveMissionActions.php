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
    $returned = $this->tokenActionV2();
    /*
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
    */
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

  private function tokenActionV2()
  {
    $this->id   = $this->post['id'];
    if ($this->id!='') {
      $this->node = $this->objXmlDocument->xPath('//*[@id="'.$this->id.'"]')[0];
    }
    $qte  = $this->post['quantite'];
    $type = $this->post['type'];
    $posX = $this->post['left'];
    $posY = $this->post['top'];

    $needABean = false;
    switch ($this->post['act']) {
      case 'activate' :
        $needABean = $this->activateAction();
      break;
      case 'add'      :
        $needABean = $this->addAction($qte, $type);
      break;
      case 'close'    :
        $needABean = $this->closeAction();
      break;
      case 'del'      :
        $needABean = $this->deleteAction($qte, $type);
      break;
      case 'draw'     :
        return $this->drawAction($type);
      break;
      case 'move'     :
        $needABean = $this->moveAction($posX, $posY);
      break;
      case 'open'     :
        $needABean = $this->openAction();
      break;
      case 'pick'     :
        $this->pickAction();
      break;
      case 'reveal'   :
        $needABean = $this->revealAction();
      break;
      case 'shuffle'  :
        $this->shuffleAction($type);
      break;
      case 'unactivate' :
        $needABean = $this->unactivateAction();
      break;
      default :
      break;
    }
    if ($needABean) {
      $TokenBean = new TokenBean($this->node);
      $returned = $TokenBean->getJsonModifications($this->id);
      return $this->jsonString($returned, 'lstElements', true);
    }
  }


  private function shuffleAction($type)
  {
    // Spawn, Equipment
    // TODO : Equipment à faire.
    // On récupère toutes les cartes Invasions, puis on les mélange
    $Spawns = $this->objXmlDocument->xpath('//spawns/spawn');
    shuffle($Spawns);
    // On les renumérote en les remettant dans la pioche
    $rank = 1;
    foreach ($Spawns as $Spawn) {
      $Spawn->attributes()['rank'] = $rank;
      $Spawn->attributes()['status'] = 'deck';
      $rank++;
    }
    // On insère un message et on ne retourne rien.
    $this->insertTchatMessage('Pioche Invasion mélangée');
  }
  private function drawAction($type)
  {
    // Spawn, Equipment
    // TODO : Equipment à faire.
    // On récupère toutes les cartes Invasions encore dans la pioche
    $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    /*
    if (empty($Spawns)) {
      $this->dealWithSpawnShuffle();
      $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    }
    */
    // On trie par ordre croissant et on récupère le premier élément.
    usort($Spawns, 'sort_trees');
    $Spawn = $Spawns[0];
    // On le défausse, on le trace, puis on retourne le visuel
    $Spawn->attributes()['status'] = 'discard';
    $this->insertTchatMessage('1 Carte Invasion piochée');
    //
    $Bean = new LocalBean();
    $returned = array(
      array("modalBody", $Bean->getBalise(self::TAG_IMG, '', array(self::ATTR_SRC=>$this->urlDirSpawns.$Spawn->attributes()['src'].'-thumb.jpg'))),
    );
    return $this->jsonString($returned, 'lstElements', true);
  }
  private function moveAction($posX, $posY)
  {
    // Zombie, Noise, Survivor
    $this->node->attributes()['coordX'] = $posX;
    $this->node->attributes()['coordY'] = $posY;
    $this->insertTchatMessage($this->node->attributes()['type'].' déplacé');
    return true;
  }
  private function pickAction()
  {
    switch (substr($this->id, 0, 1)) {
      case 'c' :
        // Objective, Noise
        $Elements = $this->objXmlDocument->map->chips->chip;
      break;
      case 'z' :
        // Zombie
        $Elements = $this->objXmlDocument->map->zombies->zombie;
      break;
      case 's' :
        // Survivor
        $Elements = $this->objXmlDocument->map->survivors->survivor;
      break;
      default :
        $this->insertTchatMessage('Suppression échouée ['.$this->id.'].');
        $Elements = array();
      break;
    }
    $cpt = 0;
    foreach ($Elements as $element) {
      if ($element['id'][0]==$this->id) {
        $this->insertTchatMessage($element->attributes()['type'].' supprimé');
        unset($Elements[$cpt]);
      }
      $cpt++;
    }
  }

  private function insertAction($type)
  {
    // Zombie, Bruit.
    $createId = true;
    switch ($type) {
      case 'Noise' :
          /////////////////////////////////////////////////////////
          // On récupère l'id du prochain Token à insérer.
          $chips = $this->objXmlDocument->xPath('//chips')[0];
          $maxId = $chips->attributes()['maxid']+1;
          $chips->attributes()['maxid'] = $maxId;
          /////////////////////////////////////////////////////////
          // On ajoute un nouveau Token au fichier XML
          $this->id = 'c'.$maxId;
          $chip = $this->objXmlDocument->xPath('//chips')[0]->addChild('chip');
          $chip->addAttribute('id', $this->id);
          $chip->addAttribute('type', 'Noise');
          $chip->addAttribute('coordX', $this->post['coordx']);
          $chip->addAttribute('coordY', $this->post['coordy']);
          $chip->addAttribute('quantite', 1);
          $this->node = $chip;
          /////////////////////////////////////////////////////////
          $msg = '1 Bruit ajouté.';
      break;
      default      :
        // Dans le cas des Zombies, c'est un peu plus complexe...
        $patternZombie = '/z(Walker|Runner|Fatty|Abomination)(Standard)/';
        if (preg_match($patternZombie, $type, $matches)) {
          /////////////////////////////////////////////////////////
          // On récupère l'id du prochain Zombie à insérer.
          $zombies = $this->objXmlDocument->xPath('//zombies')[0];
          $maxId = $zombies->attributes()['maxid']+1;
          $zombies->attributes()['maxid'] = $maxId;
          /////////////////////////////////////////////////////////
          // On ajoute un nouveau Zombie au fichier XML
          $this->id = 'z'.$maxId;
          $zombie = $this->objXmlDocument->xPath('//zombies')[0]->addChild('zombie');
          $zombie->addAttribute('id', $this->id);
          $zombie->addAttribute('type', 'Zombie');
          $zombie->addAttribute('src', $type);
          $zombie->addAttribute('coordX', $this->post['coordx']);
          $zombie->addAttribute('coordY', $this->post['coordy']);
          $zombie->addAttribute('quantite', 1);
          $this->node = $zombie;
          /////////////////////////////////////////////////////////
          $msg = '1 '.$matches[1].' '.$matches[2].' ajouté.';
        } else {
          $createId = false;
          $msg = 'Tentative création Zombie foirée : '.$type.'.';
        }
      break;
    }
    $this->insertTchatMessage($msg);
    return $createId;
  }
  private function addAction($qte, $type)
  {
    // Si l'id n'est pas défini, c'est probablement une insertion.
    if ($this->id=='') {
      return $this->insertAction($type);
    }
    // Pour les PA, PV & XP, on a passé un $type. Mais le type du node est Survivor.
    $arrTypes = array('pa'=>'actionPoints', 'pv'=>'hitPoints', 'xp'=>'experiencePoints');
    // Zombie, Noise, Survivor (XP, PV & PA).
    $matchId = true;
    switch ($this->node->attributes()['type']) {
      case 'Noise' :
        $oldQte = $this->node->attributes()['quantite'];
        $this->node->attributes()['quantite'] = $oldQte + $qte;
        $msg = $qte.' Bruit(s) ajouté(s).';
      break;
      case 'Zombie' :
        $oldQte = $this->node->attributes()['quantite'];
        $this->node->attributes()['quantite'] = $oldQte + $qte;
        $msg = $qte.' Zombie(s) ajouté(s).';
      break;
      case 'Survivor' :
        $oldQte = $this->node->attributes()[$arrTypes[$type]];
        $this->node->attributes()[$arrTypes[$type]] = $oldQte + $qte;
        $msg = $qte.' '.$type.' ajouté(s).';
      break;
      default       :
        $msg = 'Tentative insertion foirée.';
        $matchId = false;
      break;
    }
    $this->insertTchatMessage($msg);
    return $matchId;
  }

  private function deleteAction($qte, $type)
  {
    // Pour les PA, PV & XP, on a passé un $type. Mais le type du node est Survivor.
    $arrTypes = array('pa'=>'actionPoints', 'pv'=>'hitPoints', 'xp'=>'experiencePoints');
    // Zombie, Noise, Survivor (XP, PV & PA).
    $matchId = true;
    switch ($this->node->attributes()['type']) {
      case 'Noise' :
        $oldQte = $this->node->attributes()['quantite'];
        $this->node->attributes()['quantite'] = $oldQte - $qte;
        $msg = $qte.' Bruit(s) retiré(s).';
      break;
      case 'Zombie' :
        $oldQte = $this->node->attributes()['quantite'];
        $this->node->attributes()['quantite'] = $oldQte - $qte;
        $msg = $qte.' Zombie(s) retiré(s).';
      break;
      case 'Survivor' :
        $oldQte = $this->node->attributes()[$arrTypes[$type]];
        $this->node->attributes()[$arrTypes[$type]] = $oldQte - $qte;
        $msg = $qte.' '.$type.' retiré(s).';
      break;
      default       :
        $msg = 'Tentative suppression foirée.';
        $matchId = false;
      break;
    }
    $this->insertTchatMessage($msg);
    return $matchId;
  }

  private function revealAction()
  {
    // Objectif
    $this->node->attributes()['status'] = 'Unactive';
    $this->insertTchatMessage('Objectif révélé');
    return true;
  }

  private function openCloseMutualAction($newStatus, $label)
  {
    // Door
    $this->node->attributes()['status'] = $newStatus;
    $this->insertTchatMessage('Porte '.$label);
    return true;
  }
  private function openAction()
  { return $this->openCloseMutualAction('Opened', 'ouverte'); }
  private function closeAction()
  { return $this->openCloseMutualAction('Closed', 'fermée'); }

  private function activateUnactivateMutualAction($newStatus, $label)
  {
    // Spawn, Exit ou Skill
    $matchId = true;
    $this->node->attributes()['status'] = $newStatus;
    switch ($this->node->attributes()['type']) {
      case 'Exit'  :
        $msg = 'Zone de Sortie '.$label;
      break;
      case 'Skill' :
        $msg = 'Compétence '.$label;
      break;
      case 'Spawn' :
        $msg = 'Zone de Spawn '.$label;
      break;
      default      :
        $msg = ucfirst($label).' envisagée, mais id ['.$this->id.'] ne trouve pas de cible.';
        $matchId = false;
      break;
    }
    $this->insertTchatMessage($msg);
    return $matchId;
  }
  private function unactivateAction()
  { return $this->activateUnactivateMutualAction('Unactive', 'désactivée'); }
  private function activateAction()
  { return $this->activateUnactivateMutualAction('Active', 'activée'); }










  private function formatErrorMessage($msgError)
  {
    // TODO
    return "[[$msgError]]";

  }
/*
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
      } else {
        // On insère autre chose. Pour le moment, c'est forcément un Bruit

        /////////////////////////////////////////////////////////
        // On récupère l'id du prochain Chip à insérer.
        $chips = $this->objXmlDocument->map->chips->attributes()['maxid'];
        $maxId = $chips[0]+1;
        // On met à jour l'id pour la prochaine insertion.
        $this->objXmlDocument->map->chips->attributes()['maxid'] = $maxId;
        /////////////////////////////////////////////////////////
        $chip = $this->objXmlDocument->map->chips->addChild('chip');
        $chip->addAttribute('id', 'c'.$maxId);
        $chip->addAttribute('type', 'Bruit');
        $chip->addAttribute('coordX', $this->post['coordx']);
        $chip->addAttribute('coordY', $this->post['coordy']);
        $chip->addAttribute('status', 'temp');
        $chip->addAttribute('quantite', 1);

        /////////////////////////////////////////////////////////
        // On restitue le visuel
        $TokenBean = new TokenBean($chip);
        $returned = array(
          array('c'.$maxId, $TokenBean->getTokenBalise()),
          array('mc'.$maxId, $TokenBean->getTokenMenu()),
        );
        return $this->jsonString($returned, 'lstElements', true);
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
      $skill->addAttribute('id', $this->post['survivorId'].'-'.$SurvivorSkill->getTagLevelId());
      $skill->addAttribute('skillId', $SurvivorSkill->getSkillId());
      $skill->addAttribute('status', ($SurvivorSkill->getTagLevelId()<20 ? 'Active' : 'Unactive'));
      $skill->addAttribute('type', 'Skill');
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
            list($act, $qte) = explode('-', $this->act);
            if ($this->act=='pick') {
              unset($this->objXmlDocument->map->chips->chip[$cpt]);
              $this->insertTchatMessage('Jeton supprimé');
              continue;
            } elseif ($this->act=='move') {
              $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['coordX'] = $this->post['left'];
              $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['coordY'] = $this->post['top'];
              continue;
            } elseif ($act=='add') {
              $qte = $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['quantite'] + $qte;
              $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['quantite'] = $qte;
              $this->insertTchatMessage('Bruit ajouté');
            } elseif ($act=='del') {
              $qte = $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['quantite'] - $qte;
              $this->objXmlDocument->map->chips->chip[$cpt]->attributes()['quantite'] = $qte;
              $this->insertTchatMessage('Bruit retiré');
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
*/
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
