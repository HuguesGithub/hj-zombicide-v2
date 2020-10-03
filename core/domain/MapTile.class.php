<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MapTile
 * @author Hugues.
 * @since 1.08.30
 */
class MapTile extends LocalDomain
{
  /**
   * Tile
   * @var Tile $Tile
   */
  protected $Tile;
  /**
   * Orientation de la Tuile
   * @var string $orientation
   */
  protected $orientation;
  /**
   * Tuile bloquée ou non.
   * @var boolean $locked
   */
  protected $locked;


  public function __construct($Tile, $orientation, $locked=false)
  {
    $this->Tile = $Tile;
    $this->orientation = $orientation;
    $this->locked = $locked;
  }

  public function getTile()
  { return $this->Tile; }

  public function getOrientation()
  { return $this->orientation; }

  public function isLocked()
  { return ($this->locked==1); }

  public function setTile($Tile)
  { $this->Tile = $Tile; }

  public function setOrientaiton($orientation)
  { $this->orientation = $orientation; }

  public function setLocked($locked)
  { $this->locked = $locked; }

  public function isCompatible($OtherMapTile, $side)
  {
    $isCompatible = true;
    if ($side=='left') {
      $sideLeft  = $this->getLeft();
      $sideRight = $OtherMapTile->getRight();
      //echo "[left : ".$this->Tile->getCode()."-$sideLeft;".$OtherMapTile->getTile()->getCode()."-$sideRight]\r\n";
      $isCompatible = $this->compSides($sideLeft, $sideRight);
    } elseif ($side=='right') {
      $sideRight = $this->getRight();
      $sideLeft  = $OtherMapTile->getLeft();
      //echo "[top : ".$this->Tile->getCode()."-$sideTop;".$OtherMapTile->getTile()->getCode()."-$sideBottom]\r\n";
      $isCompatible = $this->compSides($sideRight, $sideLeft);
    } elseif ($side=='bottom') {
      $sideBottom = $this->getBottom();
      $sideTop    = $OtherMapTile->getTop();
      //echo "[top : ".$this->Tile->getCode()."-$sideTop;".$OtherMapTile->getTile()->getCode()."-$sideBottom]\r\n";
      $isCompatible = $this->compSides($sideBottom, $sideTop);
    } elseif ($side=='top') {
      $sideTop    = $this->getTop();
      $sideBottom = $OtherMapTile->getBottom();
      //echo "[top : ".$this->Tile->getCode()."-$sideTop;".$OtherMapTile->getTile()->getCode()."-$sideBottom]\r\n";
      $isCompatible = $this->compSides($sideTop, $sideBottom);
    }
    return $isCompatible;
  }

  public function isCompatibleV2($MapTiles, $row, $col)
  {
    $isCompatible = true;
    if (isset($MapTiles[$row][$col-1])) {
      $isCompatible = $this->isCompatible($MapTiles[$row][$col-1], 'left');
    }
    if ($isCompatible && isset($MapTiles[$row-1][$col])) {
      $isCompatible = $this->isCompatible($MapTiles[$row-1][$col], 'top');
    }
    if ($isCompatible && isset($MapTiles[$row][$col+1])) {
      $isCompatible = $this->isCompatible($MapTiles[$row][$col+1], 'right');
    }
    if ($isCompatible && isset($MapTiles[$row+1][$col])) {
      $isCompatible = $this->isCompatible($MapTiles[$row+1][$col], 'bottom');
    }
    return $isCompatible;
  }

  private function compSides($sideLeft, $sideRight)
  {
    // B: Building
    // S : Street
    // V : terrain Vague
    // C : Couloir
    // --> P : Prison Outbreak
    // --> M : Toxic City Mall
    // --> H : Rue Morgue
    // --> W : Museum Worricow
    // --> F : Highschool Funeral

    $arrCorridors = array('P', 'M', 'H', 'W', 'F');
    if ($sideLeft[0]==$sideRight[2] && $sideLeft[1]==$sideRight[1] && $sideLeft[2]==$sideRight[0]) {
      return true;
    }
    if (in_array($sideLeft[0], $arrCorridors) && in_array($sideRight[2], $arrCorridors) &&
        in_array($sideLeft[1], $arrCorridors) && in_array($sideRight[1], $arrCorridors) &&
        in_array($sideLeft[2], $arrCorridors) && in_array($sideRight[0], $arrCorridors)) {
      return true;
    }
    return false;
  }

  public function getTop()
  {
    switch ($this->orientation) {
      case 'top' :
        $top = $this->Tile->getSideTop();
      break;
      case 'right' :
        $top = $this->Tile->getSideLeft();
      break;
      case 'bottom' :
        $top = $this->Tile->getSideBottom();
      break;
      default :
        $top = $this->Tile->getSideRight();
      break;
    }
    return $top;
  }
  public function getBottom()
  {
    switch ($this->orientation) {
      case 'top' :
        $bottom = $this->Tile->getSideBottom();
      break;
      case 'right' :
        $bottom = $this->Tile->getSideRight();
      break;
      case 'bottom' :
        $bottom = $this->Tile->getSideTop();
      break;
      default :
        $bottom = $this->Tile->getSideLeft();
      break;
    }
    return $bottom;
  }

  public function getRight()
  {
    switch ($this->orientation) {
      case 'top' :
        $right = $this->Tile->getSideRight();
      break;
      case 'right' :
        $right = $this->Tile->getSideTop();
      break;
      case 'bottom' :
        $right = $this->Tile->getSideLeft();
      break;
      default :
        $right = $this->Tile->getSideBottom();
      break;
    }
    return $right;
  }
  public function getLeft()
  {
    switch ($this->orientation) {
      case 'top' :
        $left = $this->Tile->getSideLeft();
      break;
      case 'right' :
        $left = $this->Tile->getSideBottom();
      break;
      case 'bottom' :
        $left = $this->Tile->getSideRight();
      break;
      default :
        $left = $this->Tile->getSideTop();
      break;
    }
    return $left;
  }
}
