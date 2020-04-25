<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentKeyword
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class EquipmentKeyword extends LocalDomain
{
  /**
   * Id technique de la jointure
   * @var int $id
   */
  protected $id;
  /**
   * Id technique de la carte Equipement
   * @var int $equipmentCardId
   */
  protected $equipmentCardId;
  /**
   * Id technique du profil de l'arme
   * @var int $keywordId
   */
  protected $keywordId;
  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->KeywordServices = new KeywordServices();
  }
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @ return int
   */
  public function getEquipmentCardId()
  { return $this->equipmentCardId; }
  /**
   * @ return int
   */
  public function getKeywordId()
  { return $this->keywordId; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $equipmentCardId
   */
  public function setEquipmentCardId($equipmentCardId)
  { $this->equipmentCardId = $equipmentCardId; }
  /**
   * @param int $keywordId
   */
  public function setKeywordId($keywordId)
  { $this->keywordId = $keywordId; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('EquipmentKeyword'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return EquipmentKeyword
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new EquipmentKeyword(), self::getClassVars(), $row); }
  /**
   * @return Keyword
   */
  public function getKeyword()
  {
    if ($this->Keyword == null) {
      $this->Keyword = $this->KeywordServices->select(__FILE__, __LINE__, $this->keywordId);
    }
    return $this->Keyword;
  }
}
