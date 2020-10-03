<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * MissionActions
 * @author Hugues
 * @since 1.02.00
 * @version 1.07.25
 */
class MissionActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->WpPostServices   = new WpPostServices();
    $this->DurationServices = new DurationServices();
    $this->ExpansionServices = new ExpansionServices();
    $this->LevelServices    = new LevelServices();
    $this->MissionServices  = new MissionServices();
    $this->MissionExpansionServices = new MissionExpansionServices();
    $this->MissionTileServices = new MissionTileServices();
    $this->OrigineServices  = new OrigineServices();
    $this->PlayerServices   = new PlayerServices();
    $this->TileServices     = new TileServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new MissionActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETMISSIONS) {
      $returned = $Act->dealWithGetMissions();
    } elseif ($post[self::CST_AJAXACTION]==self::AJAX_MISSIONVERIF) {
      $returned = $Act->dealWithMissionVerif(true);
    } else {
      $returned = '';
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetMissions()
  {
    $Bean = new WpPageMissionsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_MISSION, true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de vérifications des Missions en Home Admin
  /**
   * @param boolean $isVerif
   * @return string
   */
  public function dealWithMissionVerif($isVerif=false)
  {
    // On récupère les articles de missions
    $args = array(
      self::WP_CAT         => self::WP_CAT_MISSION_ID,
      self::WP_TAXQUERY    => array(),
      self::WP_POSTSTATUS  => self::WP_PUBLISH.', future, pending',
    );
    $this->WpPostMissions = $this->WpPostServices->getArticles($args);
    $nbWpPostMissions = count($this->WpPostMissions);
    // Et les Missions en base
    $this->Missions = $this->MissionServices->getMissionsWithFilters();
    $nbMissions = count($this->Missions);

    if ($isVerif) {
      $this->checkMissions();
      $strBilan = $this->jsonString($this->strBilan, self::AJAX_MISSIONVERIF, true);
    } elseif ($nbWpPostMissions!=$nbMissions) {
      $strBilan  = "Le nombre d'articles ($nbWpPostMissions) ne correspond pas au nombre de Missions en base ($nbMissions).";
      $strBilan .= "<br>Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostMissions) correspond au nombre de Missions en base.";
    }
    return $strBilan;
  }

  private function checkMissions()
  {
    $hasErrors = false;
    $strErrors = '';
    $this->strBilan  = "Début de l'analyse des données relatives aux Missions.<br>";
    $this->strBilan .= "Il y a ".count($this->WpPostMissions)." articles de Missions.<br>";
    $this->strBilan .= "Il y a ".count($this->Missions)." entrées en base.<br>";
    /////////////////////////////////////////////////////////////////////
    // On va réorganiser les Missions pour les retrouver facilement
    $arrMissions = array();
    while (!empty($this->Missions)) {
      $Mission = array_shift($this->Missions);
      if (isset($arrMissions[$Mission->getCode()])) {
        $strErrors .= "Le code <em>".$Mission->getCode()."</em> semble être utilisé deux fois dans la base de données.<br>";
        $hasErrors = true;
      }
      $arrMissions[$Mission->getCode()] = $Mission;
    }
    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////
    while (!empty($this->WpPostMissions)) {
      // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostMissions);
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      if (!isset($arrMissions[$code])) {
        // A priori l'article n'a pas de code associé en base. Il faut donc en créé un qui corresponde
        $Mission = new Mission();
        $name = $this->WpPost->getPostTitle();
        $Mission->setTitle($name);
        $Mission->setCode($code);
        // Set la Difficulté
        $Mission->setLevelId($this->getWpPostLevelId());
        // Set le Nb de Joueurs
        $Mission->setPlayerId($this->getWpPostPlayerId());
        // Set la Durée
        $Mission->setDurationId($this->getWpPostDurationId());
        // Set l'Origine
        $Mission->setOrigineId($this->getWpPostOrigineId());
        // Set Width & Height
        list($width, $height) = explode(',', $this->getWpPostDimensions());
        $Mission->setWidth($width);
        $Mission->setHeight($height);
        // On peut insérer
        $this->MissionServices->insertMission($Mission);
        $this->strBilan .= "L'article <em>".$name."</em> a été créé en base.<br>";
        continue;
      }
      $Mission = $arrMissions[$code];
      unset($arrMissions[$code]);
      $this->checkMission($Mission);
    }
    /////////////////////////////////////////////////////////////////////
    // On vérifie que la totalité des Missions en base ont été utilisées. Si ce n'est pas le cas, il faut créer des articles correspondants.
    if (!empty($arrMissions)) {
      $this->strBilan .= "On a des données en base qui n'ont pas d'article correspondant.<br>";
      while (!empty($arrMissions)) {
        $Mission = array_shift($arrMissions);
        $this->strBilan .= '<br>Article à créer pour une Extension  : '.$Mission->getName().' ['.$Mission->toJson().'].<br>';
      }
    }

    /////////////////////////////////////////////////////////////////////
    $this->strBilan .= "Fin de l'analyse des données relatives aux Missions.<br>";
    if ($hasErrors) {
      $this->strBilan .= "Anomalies constatées :<br>".$strErrors;
    } else {
      $this->strBilan .= "Aucune anomalie constatée.";
    }
  }
  private function checkMission($Mission)
  {
    $doUpdate = false;
    // On initialise les données de l'article
    $name         = $this->WpPost->getPostTitle();
    // On vérifie si la donnée en base correspond à l'article.
    $strError = '';
    if ($Mission->getTitle()!=$name) {
      $Mission->setTitle($name);
      $strError .= "Le Titre a été mis à jour.<br>";
      $doUpdate = true;
    }
    $levelId = $this->getWpPostLevelId();
    if ($Mission->getLevelId()!=$levelId) {
      $Mission->setLevelId($levelId);
      $strError .= "La Difficulté a été mise à jour.<br>";
      $doUpdate = true;
    }
    $playerId = $this->getWpPostPlayerId();
    if ($Mission->getPlayerId()!=$playerId) {
      $Mission->setPlayer($playerId);
      $strError .= "Le Nb de joueurs a été mis à jour.<br>";
      $doUpdate = true;
    }
    $durationId = $this->getWpPostDurationId();
    if ($Mission->getDurationId()!=$durationId) {
      $Mission->setDurationId($durationId);
      $strError .= "La Durée a été mise à jour.<br>";
      $doUpdate = true;
    }
    $origineId = $this->getWpPostOrigineId();
    if ($Mission->getOrigineId()!=$origineId) {
      $Mission->setOrigineId($origineId);
      $strError .= "L'Origine a été mise à jour.<br>";
      $doUpdate = true;
    }
    list($width, $height) = explode(',', $this->getWpPostDimensions());
    if ($Mission->getWidth()!=$width || $Mission->getHeight()!=$height) {
      if ($width==0) {
        $this->strBilan .= "Il ne semble pas y avoir de Dalles saisies dans le champ tileIds de l'Article <em>".$name."</em> <strong>".$Mission->getCode()."</strong>.<br>";
      } elseif ($width==2 && $height==2 && $Mission->getWidth()!=$Mission->getHeight()) {
        $this->strBilan .= "Les Dimensions (".$Mission->getWidth().", ".$Mission->getHeight().") => ($width, $height) ne peuvent pas être renseignées automatiquement pour l'Article <em>".$name."</em>.<br>";
      } else {
        $strError .= "Les Dimensions (".$Mission->getWidth().", ".$Mission->getHeight().") => ($width, $height) ont été mises à jour.<br>";
        $Mission->setWidth($width);
        $Mission->setHeight($height);
        $doUpdate = true;
      }
    }

    // On récupère les Extensions rattachées à l'article.
    $arrExpansions = $this->getWpPostExpansions();
    // On récupère les MissionExpansions rattachés à la Mission.
    $MissionExpansions = $this->MissionExpansionServices->getMissionExpansionsWithFilters(array(self::FIELD_MISSIONID=>$Mission->getId()));
    if (count($arrExpansions)==count($MissionExpansions)) {
      //$this->strBilan .= "Le nombre entre les deux univers correspond. Il faudrait vérifier que ce sont bien les mêmes...<br>";
    } else {
      // On a une différence. On s'embête pas, on supprime les MissionExpansions existantes, on insère les nouvelles.
      while (!empty($MissionExpansions)) {
        $MissionExpansion = array_shift($MissionExpansions);
        $this->MissionExpansionServices->deleteMissionExpansion($MissionExpansion);
      }
      $MissionExpansion = new MissionExpansion();
      $MissionExpansion->setMissionId($Mission->getId());
      while (!empty($arrExpansions)) {
        $Expansion = array_shift($arrExpansions);
        $MissionExpansion->setExpansionId($Expansion->getId());
        $this->MissionExpansionServices->insertMissionExpansion($MissionExpansion);
      }
    }

    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->MissionServices->updateMission($Mission);
      $this->strBilan .= "Les données de la Mission <em>".$name."</em> ont été mises à jour.<br>".$strError;
    }
  }

  private function getWpPostExpansions()
  {
    $Expansions = array();
    $expansionNames = unserialize($this->WpPost->getPostMeta('expansionIds'));
    while (!empty($expansionNames)) {
      $expansionName = array_shift($expansionNames);
      $SearchedExpansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_NAME=>$expansionName));
      if (empty($SearchedExpansions)) {
        echo "[[ERROR : $expansionName ne correspond pas à une Extension.]]\r\n";
      } else {
        $Expansion = array_shift($SearchedExpansions);
        array_push($Expansions, $Expansion);
      }
    }
    return $Expansions;
  }

  private function getWpPostLevelId()
  {
    $levelId = $this->WpPost->getPostMeta(self::FIELD_LEVELID);
    $Levels = $this->LevelServices->getLevelsWithFilters(array(self::FIELD_NAME=>$levelId));
    $Level = array_shift($Levels);
    return $Level->getId();
  }

  private function getWpPostPlayerId()
  {
    $playerId = $this->WpPost->getPostMeta(self::FIELD_PLAYERID);
    $Players = $this->PlayerServices->getPlayersWithFilters(array(self::FIELD_NAME=>$playerId));
    $Player = array_shift($Players);
    return $Player->getId();
  }

  private function getWpPostDurationId()
  {
    $durationId = $this->WpPost->getPostMeta(self::FIELD_DURATIONID);
    list($min, $max) = explode('-', $durationId);
    $Durations = $this->DurationServices->getDurationsWithFilters(array(self::FIELD_MINDURATION=>$min, self::FIELD_MAXDURATION=>$max));
    $Duration = array_shift($Durations);
    return $Duration->getId();
  }

  private function getWpPostOrigineId()
  {
    $origineId = $this->WpPost->getPostMeta(self::FIELD_ORIGINEID);
    $Origines = $this->OrigineServices->getOriginesWithFilters(array(self::FIELD_NAME=>$origineId));
    $Origine = array_shift($Origines);
    return $Origine->getId();
  }

  private function getWpPostDimensions()
  {
    $tileCodes = $this->WpPost->getPostMeta('tileIds');
    $arrTileIds = explode(', ', str_replace(' &', ',', $tileCodes));
    switch (count($arrTileIds)) {
      case 2 :
      case 3 :
        $width = count($arrTileIds);
        $height = 1;
      break;
      case 4 :
      case 6 :
        $width = count($arrTileIds)/2;
        $height = 2;
      break;
      case 5 :
        $width = 1;
        $height = count($arrTileIds);
      break;
      case 8 :
      case 10 :
        $width = 2;
        $height = count($arrTileIds)/2;
      break;
      case 9 :
        $width = 3;
        $height = 3;
      break;
      case 12 :
        $width = 4;
        $height = 3;
      break;
      default :
        $width = 0;
        $height = 0;
      break;
    }
    return $width.','.$height;
  }

  private function checkCode()
  {
    // On checke le code
    $postCode = $this->WpPost->getPostMeta(self::FIELD_CODE);
    if ($postCode=='') {
      $this->addStrBilan('Code', $this->Mission->getCode());
      update_post_meta($this->WpPost->getID(), self::FIELD_CODE, $this->Mission->getCode());
      $this->areDataOkay = false;
    } elseif ($postCode!=$this->Mission->getCode()) {
      $this->strBilan .= '<br><strong>Code</strong> à mettre à jour.';
      $this->Mission->setCode($postCode);
      $this->doUpdate = true;
    }
  }
  private function checkLevel()
  {
    // On checke le levelId
    $levelId = $this->WpPost->getPostMeta(self::FIELD_LEVELID);
    $Levels = $this->LevelServices->getLevelsWithFilters(array(self::FIELD_NAME=>$levelId));
    $Level = array_shift($Levels);
    if ($levelId=='') {
      $this->addStrBilan('Difficulté', $this->Mission->getLevel()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_LEVELID, $this->Mission->getLevel()->getName());
      $this->areDataOkay = false;
    } else {
      $levelId = $Level->getId();
      if ($levelId!=$this->Mission->getLevelId()) {
        $this->strBilan .= '<br><strong>Difficulté</strong> à mettre à jour.';
        $this->Mission->setLevelId($levelId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkPlayer()
  {
    // On checke le playerId
    $playerId = $this->WpPost->getPostMeta(self::FIELD_PLAYERID);
    $Players = $this->PlayerServices->getPlayersWithFilters(array(self::FIELD_NAME=>$playerId));
    $Player = array_shift($Players);
    if ($playerId=='') {
      $this->addStrBilan('Nombre', $this->Mission->getPlayer()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_PLAYERID, $this->Mission->getPlayer()->getName());
      $this->areDataOkay = false;
    } else {
      $playerId = $Player->getId();
      if ($playerId!=$this->Mission->getPlayerId()) {
        $this->strBilan .= '<br><strong>Nombre</strong> à mettre à jour. ['.$playerId.';'.$this->Mission->getPlayerId().']';
        $this->Mission->setPlayerId($playerId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkDuration()
  {
    // On checke le durationId
    $durationId = $this->WpPost->getPostMeta(self::FIELD_DURATIONID);
    list($min, $max) = explode('-', $durationId);
    $Durations = $this->DurationServices->getDurationsWithFilters(array(self::FIELD_MINDURATION=>$min, self::FIELD_MAXDURATION=>$max));
    if (!empty($Durations)) {
      $Duration = array_shift($Durations);
    } else {
      $Duration = new Duration();
    }
    if ($durationId=='') {
      $strDuree = $this->Mission->getDuration()->getMinDuration().($this->Mission->getDuration()->getMaxDuration()!=0 ? '-'.$this->Mission->getDuration()->getMaxDuration() : '');
      $this->addStrBilan('Durée', $strDuree);
      update_post_meta($this->WpPost->getID(), self::FIELD_DURATIONID, $strDuree);
      $this->areDataOkay = false;
    } else {
      $durationId = $Duration->getId();
      if ($durationId!=$this->Mission->getDurationId()) {
        $this->strBilan .= '<br><strong>Durée</strong> à mettre à jour.';
        $this->Mission->setDurationId($durationId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkOrigine()
  {
    // On checke le origineId
    $origineId = $this->WpPost->getPostMeta(self::FIELD_ORIGINEID);
    $Origines = $this->OrigineServices->getOriginesWithFilters(array(self::FIELD_NAME=>$origineId));
    if (!empty($Origines)) {
      $Origine = array_shift($Origines);
    } else {
      $Origine = new Origine();
    }
    if ($origineId=='') {
      $this->addStrBilan('Origine', $this->Mission->getOrigine()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_ORIGINEID, $this->Mission->getOrigine()->getName());
      $this->areDataOkay = false;
    } else {
      $origineId = $Origine->getId();
      if ($origineId!=$this->Mission->getOrigineId()) {
        $this->strBilan .= '<br><strong>Origine</strong> à mettre à jour.';
        $this->Mission->setOrigineId($origineId);
        $this->doUpdate = true;
      }
    }
  }
  private function addStrBilan($type, $name)
  { $this->strBilan .=  '<br><strong>'.$type.'</strong> <em>'.$name.'</em>.';
  }

  private function checkTiles()
  {
    $missionId = $this->Mission->getId();
    $MissionTiles = $this->MissionTileServices->getMissionTilesWithFilters(array(self::FIELD_MISSIONID=>$missionId));
    if (!empty($MissionTiles)) {
      return;
    }
    $tileIds = $this->WpPost->getPostMeta('tileIds');
    if ($tileIds=='') {
      return;
    }
    $arrTileIds = explode(', ', str_replace(' &', ',', $tileIds));
    $width = $this->Mission->getWidth();
    $height = $this->Mission->getHeight();
    // On initialise le MissionTile qu'on va devoir insérer en base.
    $MissionTile = new MissionTile();
    $MissionTile->setMissionId($missionId);
    $MissionTile->setOrientation('?');
    // On doit insérer chaque Dalle.
    for ($row=0; $row<$height; $row++) {
      $MissionTile->setCoordY($row+1);
      for ($col=0; $col<$width; $col++) {
        $MissionTile->setCoordX($col+1);
        $Tiles = $this->TileServices->getTilesWithFilters(array(self::FIELD_CODE=>$arrTileIds[$row*$width+$col]));
        if (!empty($Tiles)) {
          $Tile = array_shift($Tiles);
          $MissionTile->setTileId($Tile->getId());
          $this->MissionTileServices->insertMissionTile($MissionTile);
        }
      }
    }
  }
  // Fin du bloc relatif à la vérification des Missions sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
}
