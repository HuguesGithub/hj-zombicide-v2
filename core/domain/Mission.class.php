<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Mission
 * @author Hugues.
 * @since 1.04.00
 * @version 1.05.14
 */
class Mission extends WpPostRelais
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Titre de la Mission
   * @var string $title
   */
  protected $title;
  /**
   * Code de la donnée
   * @var string $code
   */
  protected $code;
  /**
   * Id de la difficulté de la Mission
   * @var int $levelId
   */
  protected $levelId;
  /**
   * Id du nb de joueurs de la Mission
   * @var int $playerId
   */
  protected $playerId;
  /**
   * Id de la durée de la Mission
   * @var int $durationId
   */
  protected $durationId;
  /**
   * Id de l'origine de la Mission
   * @var int $origineId
   */
  protected $origineId;
  /**
   * Nombre de dalles en largeur
   * @var int $width
   */
  protected $width;
  /**
   * Nombre de dalles en hauteur
   * @var int $height
   */
  protected $height;
  /**
   * La mission a-t-elle était publiée ?
   * @var int $published
   */
  protected $published;
  /**
   * La mission peut-elle être jouée en ligne ?
   * @var int $liveAble
   */
  protected $liveAble;

  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @return string
   */
  public function getTitle()
  { return $this->title; }
  /**
   * @return string
   */
  public function getCode()
  { return $this->code; }
  /**
   * @return int
   */
  public function getLevelId()
  { return $this->levelId; }
  /**
   * @return int
   */
  public function getPlayerId()
  { return $this->playerId; }
  /**
   * @return int
   */
  public function getDurationId()
  { return $this->durationId; }
  /**
   * @return int
   */
  public function getOrigineId()
  { return $this->origineId; }
  /**
   * @return int
   */
  public function getWidth()
  { return $this->width; }
  /**
   * @return int
   */
  public function getHeight()
  { return $this->height; }
  /**
   * @return boolean
   */
  public function isPublished()
  { return ($this->published==1); }
  /**
   * @return boolean
   */
  public function isLiveAble()
  { return ($this->liveAble==1); }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $title
   */
  public function setTitle($title)
  { $this->title=$title; }
  /**
   * @param string $code
   */
  public function setCode($code)
  { $this->code=$code; }
  /**
   * @param int $levelId
   */
  public function setLevelId($levelId)
  { $this->levelId=$levelId; }
  /**
   * @param int $playerId
   */
  public function setPlayerId($playerId)
  { $this->playerId=$playerId; }
  /**
   * @param int $durationId
   */
  public function setDurationId($durationId)
  { $this->durationId=$durationId; }
  /**
   * @param int $origineId
   */
  public function setOrigineId($origineId)
  { $this->origineId=$origineId; }
  /**
   * @param int $width
   */
  public function setWidth($width)
  { $this->width=$width; }
  /**
   * @param int $height
   */
  public function setHeight($height)
  { $this->height=$height; }
  /**
   * @param boolean $published
   */
  public function setPublished($published)
  { $this->published=$published; }
  /**
   * @param boolean $liveAble
   */
  public function setLiveAble($liveAble)
  { $this->liveAble=$liveAble; }

  ///////////////////////////////////////////////////////////////
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Mission'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Mission
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Mission(), self::getClassVars(), $row); }
  /**
   * @return MissionBean
   */
  public function getBean()
  { return new MissionBean($this); }
  ///////////////////////////////////////////////////////////////

  /**
   * @return string
   */
  public function getWpPost()
  { return $this->getMainWpPost(self::FIELD_CODE, $this->code, self::WP_CAT_MISSION_ID); }







  /**
   * @param string $orderBy
   * @param string $order
   * @return array MissionTile
   */
  public function getMissionTiles($orderBy='id', $order='asc')
  {
    if ($this->MissionTiles == null) {
      $arrFilters = array(self::FIELD_MISSIONID=>$this->id);
      $this->MissionTiles = $this->MissionTileServices->getMissionTilesWithFilters($arrFilters, $orderBy, $order);
    }
    return $this->MissionTiles;
  }
  /**
   * @return array MissionZone
   */
  public function getMissionZones()
  {
    if ($this->MissionZones == null) {
      $arrFilters = array(self::CST_MISSIONID=>$this->id);
      $this->MissionZones = $this->MissionZoneServices->getMissionZonesWithFilters(__FILE__, __LINE__, $arrFilters);
    }
    return $this->MissionZones;
  }
  /**
   * @return array MissionRule
   */
  public function getMissionRules($orderBy='id')
  {
    if ($this->MissionRules == null) {
      $arrFilters = array(self::FIELD_MISSIONID=>$this->id);
      $this->MissionRules = $this->MissionRuleServices->getMissionRulesWithFilters($arrFilters, $orderBy);
    }
    return $this->MissionRules;
  }
  /**
   * @return array MissionObjective
   */
  public function getMissionObjectives($oBy='id')
  {
    if ($this->MissionObjectives == null) {
      $arrF = array(self::FIELD_MISSIONID=>$this->id);
      $this->MissionObjectives = $this->MissionObjectiveServices->getMissionObjectivesWithFilters($arrF, $oBy);
    }
    return $this->MissionObjectives;
  }
  /**
   * @return array MissionExpansion
   */
  public function getMissionExpansions()
  {
    if ($this->MissionExpansions == null) {
      $arrFilters = array(self::FIELD_MISSIONID=>$this->id);
      $this->MissionExpansions = $this->MissionExpansionServices->getMissionExpansionsWithFilters($arrFilters);
    }
    return $this->MissionExpansions;
  }
  /**
   * @return Duration
   */
  public function getDuration()
  {
    if ($this->Duration == null) {
      $this->Duration = $this->DurationServices->selectDuration($this->durationId);
    }
    return $this->Duration;
  }
  /**
   * @return Level
   */
  public function getLevel()
  {
    if ($this->Level == null) {
      $this->Level = $this->LevelServices->selectLevel($this->levelId);
    }
    return $this->Level;
  }
  /**
   * @return Origine
   */
  public function getOrigine()
  {
    if ($this->Origine==null) {
      $this->Origine = $this->OrigineServices->selectOrigine($this->origineId);
    }
    return $this->Origine;
  }
  /**
   * @return Player
   */
  public function getPlayer()
  {
    if ($this->Player == null) {
      $this->Player = $this->PlayerServices->selectPlayer($this->playerId);
    }
    return $this->Player;
  }
  public function getImgUrl()
  { return '/wp-content/plugins/hj-zombicide/web/rsc/img/missions/'.$this->getCode().'-Mission.png'; }
  public function getThumbUrl()
  { return '/wp-content/plugins/hj-zombicide/web/rsc/img/missions/'.$this->getCode().'-Thumb.png'; }
  /**
   * @return string
   */
  public function getStrRules()
  {
    $MissionRules = $this->getMissionRules(self::FIELD_TITLE);
    $strList = '';
    if (!empty($MissionRules)) {
      foreach ($MissionRules as $MissionRule) {
        $strList .= ($strList!='' ? '<br>' : '');
        $strList .= '<span class="objRule">'.$MissionRule->getTitle().' <span class="tooltip">';
        $strList .= '<header>'.$MissionRule->getRuleCode().'</header>';
        $strList .= '<content>'.$MissionRule->getRuleDescription().'</content></span></span> ';
      }
    }
    return $strList;
  }
  /**
   * @return string
   */
  public function getStrObjectives()
  {
    $MissionObjectives = $this->getMissionObjectives(self::FIELD_TITLE);
    $strList = '';
    if (!empty($MissionObjectives)) {
      foreach ($MissionObjectives as $MissionObjective) {
        $strList .= ($strList!='' ? '<br>' : '');
        $strList .= '<span class="objRule">'.$MissionObjective->getTitle().' <span class="tooltip">';
        $strList .= '<header>'.$MissionObjective->getObjectiveCode().'</header>';
        $strList .= '<content>'.$MissionObjective->getObjectiveDescription().'</content></span></span> ';
      }
    }
    return $strList;
  }
  /**
   * @return string
   */
  public function getStrTiles()
  {
    $MissionTiles = $this->getMissionTiles();
    $strName = '';
    while (!empty($MissionTiles)) {
      $MissionTile = array_shift($MissionTiles);
      if ($strName!='') {
        $strName .= ', ';
      }
      $strName .= $MissionTile->getTile()->getCode();
    }
    return $strName;
  }
  /**
   * @return string
   */
  public function getStrExpansions()
  {
    $MissionExpansions = $this->getMissionExpansions();
    $strName = '';
    if (!empty($MissionExpansions)) {
      foreach ($MissionExpansions as $MissionExpansion) {
        if ($strName!='') {
          $strName .= ', ';
        }
        $strName .= $MissionExpansion->getExpansion()->getName();
      }
    }
    return $strName;
  }
  /**
   * @return string
   */
  public function getStrDifPlaDur()
  { return $this->getStrDifficulty().' / '.$this->getStrNbJoueurs().' / '.$this->getStrDuree(); }
  /**
   * @return string
   */
  public function getStrDuree()
  { return $this->getDuration()->getStrDuree(); }
  /**
   * @return string
   */
  public function getStrDifficulty()
  { return $this->getLevel()->getName(); }
  /**
   * @return string
   */
  public function getStrNbJoueurs()
  { return $this->getPlayer()->getNbJoueurs(); }
  /**
   * @return string
   */
  public function getStrOrigine()
  { return $this->getOrigine()->getName(); }
  /**
   * @param bool $isHome
   * @return string
   */
  public function getStrClassFilters($isHome)
  {
    $strClassFilters ='';
    $strClassFilters  = 'player-'.$this->playerId.' ';
    $strClassFilters .= 'duration-'.$this->durationId.' ';
    $strClassFilters .= 'level-'.$this->levelId.' ';
    return $strClassFilters.' col-12 col-sm-6 col-md-4';
  }
  /**
   * @param int $x
   * @param int $y
   * @return MissionTile
   */
  public function getMissionTile($x, $y)
  {
    $MissionTiles = $this->getMissionTiles();
    if (!empty($MissionTiles)) {
      foreach ($MissionTiles as $MissionTile) {
        if ($MissionTile->getCoordX()==$x && $MissionTile->getCoordY()==$y) {
          return $MissionTile;
        }
      }
    }
    return new MissionTile();
  }
  /**
   * @param int $x
   * @param int $y
   * @return int
   */
  public function getTileId($x, $y)
  { return $this->getMissionTile($x, $y)->getTileId(); }
  /**
   * @param int $x
   * @param int $y
   * @return string
   */
  public function getTileCode($x, $y)
  { return $this->getMissionTile($x, $y)->getTileCode(); }
  /**
   * @param int $x
   * @param int $y
   * @return string
   */
  public function getTileCodeAndOrientation($x, $y)
  { return $this->getTileCode($x, $y).'-'.$this->getTileOrientation($x, $y); }
  /**
   * @param int $x
   * @param int $y
   * @return string
   */
  public function getTileOrientation($x, $y)
  { return $this->getMissionTile($x, $y)->getOrientation(); }
  /**
   * @param array $MissionExpansions
   */
  public function setMissionExpansions($MissionExpansions)
  { $this->MissionExpansions = $MissionExpansions; }
  /**
   * @param array $post
   * @return bool
   */
  public function updateWithPost($post)
  {
    $doUpdate = false;
    $arr = array(self::FIELD_TITLE, self::FIELD_CODE, self::CST_LEVELID, self::CST_DURATIONID, self::CST_PLAYERID, self::CST_ORIGINEID);
    while (!empty($arr)) {
      $key = array_shift($arr);
      $value = stripslashes($post[$key]);
      if ($this->{$key} != $value) {
        $doUpdate = true;
        $this->{$key} = $value;
      }
    }
    return $doUpdate;
  }
  /**
   * @param array $post
   * @return bool
   */
  public function initWithPost($post)
  {
    $doInsert = true;
    $arr = array(self::FIELD_TITLE, self::FIELD_CODE, self::CST_LEVELID, self::CST_DURATIONID, self::CST_PLAYERID, self::CST_ORIGINEID);
    while (!empty($arr)) {
      $key = array_shift($arr);
      if ($post[$key] == '') {
        $doInsert = false;
      } else {
        $this->{$key} = stripslashes($post[$key]);
      }
    }
    return $doInsert;
  }
  /**
   * @param int $ruleId
   * @return boolean
   */
  public function hasRule($ruleId)
  {
    $hasRule = false;
    $MissionRules = $this->getMissionRules();
    while (!empty($MissionRules)) {
      $MissionRule = array_shift($MissionRules);
      if ($MissionRule->getRuleId()==$ruleId) {
        $hasRule = true;
      }
    }
    return $hasRule;
  }
  /**
   * @return int
   */
  public function getStartingMissionZoneId()
  {
    return 14;
  }
  /**
   * @param Live $Live
   * @param array $LiveSurvivors
   */
  public function addStandardStartingEquipment($Live, $LiveSurvivors)
  {
    shuffle($LiveSurvivors);
    // On va checker les éventuelles règles qui perturbent cette distribution.
    if ($this->hasRule(2)) {
      // On ne distribue que jusqu'à 3 Poêles aux Survivants. id de la Poêle : 27
      $cpt=0;
      while (!empty($LiveSurvivors) && $cpt<3) {
        $LiveSurvivor = array_shift($LiveSurvivors);
        $args = array(self::CST_LIVESURVIVORID=>$LiveSurvivor->getId());
        $EquipmentLiveDecks = $this->EquipmentLiveDeckServices->getEquipmentLiveDecksWithFilters(__FILE__, __LINE__, $args);
        $rk = count($EquipmentLiveDecks);
        $args = array(
          self::CST_LIVEID=>$Live->getId(),
          self::CST_EQUIPMENTCARDID=>27,
          'rank'=>$rk,
          self::CST_STATUS=>'E',
          self::CST_LIVESURVIVORID=>$LiveSurvivor->getId()
        );
        $EquipmentLiveDeck = new EquipmentLiveDeck($args);
        $this->EquipmentLiveDeckServices->insert(__FILE__, __LINE__, $EquipmentLiveDeck);
        $cpt++;
      }
    } else {
      // On vérifie l'extension rattachée à la Mission et en fonction on donnera du matériel.
    }
  }

}
