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
    ////////////////////////////////////////////////////////////////////////
    // On a le feu vert, on ouvre le fichier XML
    $this->objXmlDocument = simplexml_load_file($fileName);
    ////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    // On est venu pour ça. Analyser l'action passée en paramètre, et traiter.
    $returned = $this->parseAndResolveAction();
    ////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    // Et si on en profitait pour purger les Tchats un peu vieux... de l'automate uniquement
    $purgeLimit = time()-1*24*60*60;
    $Tchats = $this->objXmlDocument->xPath('//tchat[@timestamp<"'.$purgeLimit.'" and @author="Automat"]');
    // On parcourt la liste des logs à supprimer.
    foreach ($Tchats as $Tchat) {
      // Et on les supprime un à un.
      unset($Tchat[0]);
    }
    ////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    // On sauvegarde les modifications du fichier.
    $this->objXmlDocument->asXML($fileName);
    // Et on retourne le visuel modifié. S'il y en a un.
    if (!empty($returned)) {
      return $returned;
    }
  }

  private function parseAndResolveAction()
  {
    $bln_create = true;
    $this->id   = $this->post['id'];
    if ($this->id!='') {
      $this->node = $this->objXmlDocument->xPath('//*[@id="'.$this->id.'"]')[0];
      $bln_create = false;
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
      case 'init'     :
        return $this->initAction($type);
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
      case 'tchat'  :
        return $this->tchatAction();
      break;
      case 'unactivate' :
        $needABean = $this->unactivateAction();
      break;
      default :
      break;
    }
    if ($needABean) {
      $TokenBean = new TokenBean($this->node);
      $returned = $TokenBean->getJsonModifications($this->id, $bln_create);
      return $this->jsonString($returned, 'lstElements', true);
    }
  }

  private function initAction($type)
  {
    switch ($type) {
      case 'Spawn' :
        // On doit supprimer tous les Spawns.
        $Spawns = $this->objXmlDocument->xPath('//spawns/spawn');
        // On vire le noeud Spawns
        foreach ($Spawns as $Spawn) {
          // Et on les supprime un à un.
          unset($Spawn[0]);
        }

        // On récupère l'intervalle à utiliser dorénavant.
        $newInterval = $this->post['interval'];
        $this->insertTchatMessage('Pioche Invasion redéfinie : '.$newInterval);
        // Et on recrée le nouveau, avec le bon intervalle.
        $Spawns = $this->objXmlDocument->xpath('//spawns')[0];
        $Spawns->attributes()['interval'] = $newInterval;

        // On ajoute les nouvelles cartes
        $intervals = explode(',', $newInterval);
        $rank = 1;
        foreach ($intervals as $interval) {
          list($interval, $multi) = explode('x', $interval);
          list($start, $end) = explode('-', $interval);
          if ($multi=='') {
            $multi = 1;
          }
          if ($end=='') {
            $end = $start;
          }
          for ($i=1; $i<=$multi; $i++) {
            for ($j=$start; $j<=$end; $j++) {
              $spawn = $this->objXmlDocument->spawns->addChild('spawn');
              $spawn->addAttribute('id', 'spawn-'.$rank);
              $spawn->addAttribute('src', 'x'.str_pad($j, 3, 0, STR_PAD_LEFT));
              $spawn->addAttribute('rank', $rank);
              $spawn->addAttribute('status', 'deck');
              $rank++;
            }
          }
        }
        // On pense bien à mélanger.
        $this->shuffleAction($type);

        // Et on retourne l'intervalle mis à jour
        $returned = array(
          array('currentInterval', '<input type="text" class="form-control" id="currentInterval" readonly value="'.$newInterval.'"/>'),
        );
        return $this->jsonString($returned, 'lstElements', true);
      break;
      default :
      break;
    }

  }

  private function shuffleAction($type)
  {
    // Spawn, Equipment
    // TODO : Equipment à faire.
    switch ($type) {
      case 'Spawn' :
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
      break;
      default :
      break;
    }
  }
  private function drawAction($type)
  {
    // Spawn, Equipment
    // TODO : Equipment à faire.
    // On récupère toutes les cartes Invasions encore dans la pioche
    $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    if (empty($Spawns)) {
      $this->shuffleAction($type);
      $Spawns = $this->objXmlDocument->xPath('//spawns/spawn[@status="deck"]');
    }
    // On trie par ordre croissant et on récupère le premier élément.
    usort($Spawns, 'sort_trees_rank');
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

        $type = $this->node->attributes()['src'];
        $oldQte = $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'];
        $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'] = $oldQte - $this->node->attributes()['quantite'];
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

  private function insertAction($type='')
  {
    // Zombie, Survivor, Bruit.
    $createId = true;
    switch ($type) {
      case 'Survivor' :
        /////////////////////////////////////////////////////////
        // On ajoute un nouveau Survivor au fichier XML
        $survivor = $this->objXmlDocument->xPath('//survivors')[0]->addChild('survivor');
        $survivorId = $this->post['survivorId'];
        $this->id = $survivorId;
        $survivor->addAttribute('id', $survivorId);
        // On s'appuie sur l'id pour récupérer les infos en base
        $Survivor = $this->SurvivorServices->selectSurvivor(substr($survivorId, 1));
        // Et on peut sauvegarder l'id du portrait
        $usedName = ($Survivor->getAltImgName()!='' ? $Survivor->getAltImgName() : $Survivor->getName());
        $src = 'p'.$Survivor->getNiceName($usedName);
        $survivor->addAttribute('src', $src);
        // On récupère le Token Zone de départ pour y mettre le Survivant.
        $Token = $this->objXmlDocument->xPath('//chip[@type="Starting"]')[0];
        $survivor->addAttribute('coordX', $Token->attributes()['coordX']);
        $survivor->addAttribute('coordY', $Token->attributes()['coordY']);
        // On initialise ensuite les données de base.
        // TODO : Ces infos pourraient ne pas être fixes, selon ... plein de facteurs éventuels.
        $survivor->addAttribute('type', 'Survivor');
        $survivor->addAttribute('status', 'Survivor');
        $survivor->addAttribute('hitPoints', 2);
        $survivor->addAttribute('actionPoints', 3);
        $survivor->addAttribute('experiencePoints', 0);
        $survivor->addAttribute('level', 'Blue');
        /////////////////////////////////////////////////////////

        /////////////////////////////////////////////////////////
        // On va maintenant s'occuper d'ajouter les Skills du Survivor
        $skills = $survivor->addChild('skills');
        $SurvivorSkills = $Survivor->getSurvivorSkills(self::CST_SURVIVORTYPEID_S);
        while (!empty($SurvivorSkills)) {
          $SurvivorSkill = array_shift($SurvivorSkills);
          $skill = $skills->addChild('skill');
          $skill->addAttribute('id', $survivorId.'-'.$SurvivorSkill->getTagLevelId());
          $skill->addAttribute('skillId', $SurvivorSkill->getSkillId());
          $skill->addAttribute('status', ($SurvivorSkill->getTagLevelId()<20 ? 'Active' : 'Unactive'));
          $skill->addAttribute('type', 'Skill');
        }
        /////////////////////////////////////////////////////////

        /////////////////////////////////////////////////////////
        // Enfin, on gère l'équipement de départ pour ceux ayant une compétence spécifique
        // TODO : Enfin... En suspens pour le moment.
        $this->node = $survivor;
        /////////////////////////////////////////////////////////
        $msg = '1 Survivant ajouté.';
      break;
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

          $oldQte = $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'];
          $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'] = $oldQte + 1;
        } else {
          $createId = false;
          $msg = 'Tentative création Zombie foirée : '.$type.'.';
        }
      break;
    }
    $this->insertTchatMessage($msg);
    return $createId;
  }
  private function addAction($qte='', $type='')
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

        $type = $this->node->attributes()['src'];
        $oldQte = $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'];
        $this->objXmlDocument->xPath('//pool[@type="'.$type.'"]')[0]->attributes()['current'] = $oldQte + $qte;
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

  private function tchatAction()
  {
    // L'idée est d'insérer le message envoyé en paramètre. Ou ne rien faire s'il n'y a pas de message à insérer.
    // Puis de retourner les derniers messages insérés.
    // Pour ça, on a besoin de connaître le timestamp du dernier tchat affiché. Il faut donc qu'il soit stocké dans le tchat...
    $timestamp = $this->post['tsTreshold'];
    $Bean = new WpPageMissionOnlineBean();
    $lstTchats = $Bean->getLstTchats($timestamp);
    $returned = array(
      array('tchat-new', $lstTchats),
    );
    return $this->jsonString($returned, 'lstElements', true);
  }








  private function formatErrorMessage($msgError)
  {
    // TODO
    return "[[$msgError]]";

  }
  private function insertTchatMessage($msg='', $author='Automat')
  {
    $Tchat = $this->objXmlDocument->tchats->addChild('tchat', $msg);
    $Tchat->addAttribute('timestamp', time());
    $Tchat->addAttribute('author', $author);
  }

}

function sort_trees_rank($t1, $t2) {
  return ($t1['rank']*1 > $t2['rank']*1);
}
