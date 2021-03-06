<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Expansion
 * @author Hugues.
 * @since 1.04.00
 * @version 1.07.22
 */
class Expansion extends WpPostRelais
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Code de la donnée
   * @var string $code
   */
  protected $code;
  /**
   * Nom de la donnée
   * @var string $name
   */
  protected $name;
  /**
   * Rang d'affichage
   * @var int $displayRank
   */
  protected $displayRank;
  /**
   * Nombre de Survivants
   * @var int $nbSurvivants
   */
  protected $nbSurvivants;
  /**
   * Nombre de Missions
   * @var int $nbMissions
   */
  protected $nbMissions;
  /**
   * Nombre de Dalles
   * @var int $nbDalles
   */
  protected $nbDalles;
  /**
   * Est officielle ?
   * @var boolean $official;
   */
  protected $official;

  /**
   * Getter Id
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * Getter Code
   * @return string
   */
  public function getCode()
  { return $this->code; }
  /**
   * Getter Name
   * @return string
   */
  public function getName()
  { return $this->name; }
  /**
   * Getter displayRank
   * @return int
   */
  public function getDisplayRank()
  { return $this->displayRank; }
  /**
   * Getter nbSurvivants
   * @return int
   */
  public function getNbSurvivants()
  { return $this->nbSurvivants; }
  /**
   * Getter nbMissions
   * @return int
   */
  public function getNbMissions()
  { return $this->nbMissions; }
  /**
   * Getter nbDalles
   * @return int
   */
  public function getNbDalles()
  { return $this->nbDalles; }
  /**
   * Getter official
   * @return boolean
   */
  public function isOfficial()
  { return ($this->official==1); }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $code
   */
  public function setCode($code)
  { $this->code=$code; }
  /**
   * @param string $name
   */
  public function setName($name)
  { $this->name=$name; }
  /**
   * @param int $displayRank
   */
  public function setDisplayRank($displayRank)
  { $this->displayRank=$displayRank; }
  /**
   * @param int $nbSurvivants
   */
  public function setNbSurvivants($nbSurvivants)
  { $this->nbSurvivants = $nbSurvivants; }
  /**
   * @param int $nbMissions
   */
  public function setNbMissions($nbMissions)
  { $this->nbMissions = $nbMissions; }
  /**
   * @param int $nbDalles
   */
  public function setNbDalles($nbDalles)
  { $this->nbDalles = $nbDalles; }
  /**
   * @param boolean $official
   */
  public function setOfficial($official)
  { $this->official = $official; }

  ///////////////////////////////////////////////////////////////
  /**
   * Retourne les attributs de la classe
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Expansion'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Expansion(), self::getClassVars(), $row); }
  /**
   * @return ExpansionBean
   */
  public function getBean()
  { return new ExpansionBean($this); }
  ///////////////////////////////////////////////////////////////

  /**
   * @return string
   */
  public function getWpPost()
  { return $this->getMainWpPost(self::FIELD_CODE, $this->code, self::WP_CAT_EXPANSION_ID); }








  /**
   * @param array $row
   */
  public static function convertElementFromPost($row)
  {
    $Obj = new Expansion();
    $vars = get_class_vars('Expansion');
    if (!empty($vars)) {
      foreach ($vars as $key => $value) {
        $Obj->setField($key, $row[$key]);
      }
      if ($row['officielle']=='on') {
        $Obj->setField('officielle', 1);
      }
      if ($row['active']=='on') {
        $Obj->setField('active', 1);
      }
    }
    return $Obj;
  }

  public function getTiles()
  {
    if ($this->Tiles==null) {
      $this->Tiles = $this->TileServices->getTilesWithFilters(array(self::FIELD_EXPANSIONID=>$this->getId()));
    }
    return $this->Tiles;
  }
  public function getMissions()
  {
    if ($this->Missions==null) {
      $this->Missions = $this->MissionServices->getMissionsByExpansionId($this->getId());
    }
    return $this->Missions;
  }
}
