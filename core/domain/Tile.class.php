<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Tile
 * @author Hugues.
 * @since 1.04.07
 * @version 1.07.22
 */
class Tile extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de l'Expansion
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * Code de la Dalle
   * @var string $code
   */
  protected $code;
  protected $coordPoly;
  protected $zoneType;
  /**
   * Sans doute à virer...
   * @var string $zoneAcces
   */
  protected $zoneAcces;
  /**
   * La Dalle est elle active ?
   * @var int $active
   */
  protected $activeTile;
  protected $oCode;
  protected $side_top;
  protected $side_right;
  protected $side_bottom;
  protected $side_left;

  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @return string
   */
  public function getCode()
  { return $this->code; }
  /**
   * @return int
   */
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @return string
   */
  public function getZoneAcces()
  { return $this->zoneAcces; }

  public function getOCode()
  { return $this->oCode; }
  public function getSideTop()
  { return $this->side_top; }
  public function getSideRight()
  { return $this->side_right; }
  public function getSideBottom()
  { return $this->side_bottom; }
  public function getSideLeft()
  { return $this->side_left; }

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
   * @param int $expansionId
   */
  public function setExpansionId($expansionId)
  { $this->expansionId=$expansionId; }
  /**
   * @param string $zoneAcces
   */
  public function setZoneAcces($zoneAcces)
  { $this->zoneAcces=$zoneAcces; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Tile'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Tile
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Tile(), self::getClassVars(), $row); }
  /**
   * @return string
   */
  public function getImgUrl()
  { return '/wp-content/plugins/hj-zombicide/web/rsc/img/tiles/'.$this->code.'-500px.png'; }
  /**
   * @return string
   */
  public function getDimensions()
  {
    list($width, $height, ,) = getimagesize('http://zombicide.jhugues.fr'.$this->getImgUrl());
    return $width.'px x '.$height.'px';
  }
  /**
   * @param array $row
   * @return Tile
   */
  public static function convertElementFromPost($row)
  {
    $Obj = new Tile();
    $vars = get_class_vars('Tile');
    if (!empty($vars)) {
      foreach ($vars as $key => $value) {
        $Obj->setField($key, str_replace("\\", '', $row[$key]));
      }
      if ($row['active']=='on') {
        $Obj->setField('active', 1);
      }
    }
    return $Obj;
  }
}
