<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionTile
 * @author Hugues.
 * @since 1.0.00
 * @version 1.04.28
 */
class MissionTile extends LocalDomain
{
  /**
   * Id technique de la jointure
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de la Mission
   * @var int $missionId
   */
  protected $missionId;
  /**
   * Id technique de la Tuile
   * @var int $tileId
   */
  protected $tileId;
  /**
   * Orientation de la Dalle sur la Mission
   * @var string $orientation
   */
  protected $orientation;
  /**
   * Coordonnées en abscisses
   * @var int $coordX
   */
  protected $coordX;
  /**
   * Coordonnée en ordonnées
   * @var int $coordY
   */
  protected $coordY;
  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  { parent::__construct($attributes); }
  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @ return int
   */
  public function getMissionId()
  { return $this->MissionId; }
  /**
   * @ return int
   */
  public function getTileId()
  { return $this->tileId; }
  /**
   * @return string
   */
  public function getOrientation()
  { return $this->orientation; }
  /**
   * @return int
   */
  public function getCoordX()
  { return $this->coordX; }
  /**
   * @return int
   */
  public function getCoordY()
  { return $this->coordY; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $missionId
   */
  public function setMissionId($missionId)
  { $this->missionId = $missionId; }
  /**
   * @param int $tileId
   */
  public function setTileId($tileId)
  { $this->tileId = $tileId; }
  /**
   * @param string $orientation
   */
  public function setOrientation($orientation)
  { $this->orientation=$orientation; }
  /**
   * @param int $coordX
   */
  public function setCoordX($coordX)
  { $this->coordX=$coordX; }
  /**
   * @param int $coordY
   */
  public function setCoordY($coordY)
  { $this->coordY=$coordY; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('MissionTile'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return MissionExpansion
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new MissionTile(), self::getClassVars(), $row); }
  /**
   * @return Tile
   */
  public function getTile()
  {
    if ($this->Tile == null) {
      $this->Tile = $this->TileServices->selectTile($this->tileId);
    }
    return $this->Tile;
  }

}
