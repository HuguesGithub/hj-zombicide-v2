<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TileDaoImpl
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.07
 */
class TileDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Tile'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Tile::convertElement($row);
      }
    }
    return $Items;
  }

  /**
   * @param array $arrParams
   * @param array $filters
   * @return array
   */
  public function selectEntriesWithFiltersIn($arrParams, $filters)
  {
    // On s'appuie sur la requête de base.
    $requete  = $this->selectRequest.$this->fromRequest;
    // On ajoute la restriction sur les Extensions
    $requete .= 'WHERE expansionId IN ('.$filters[self::FIELD_EXPANSIONID].') ';
    // On peut aussi trier
    $requete .= $this->orderBy;
    // Et retourner le tableau de résultats.
    return $this->convertToArray($this->selectEntriesAndLogQuery(__FILE__, __LINE__, $requete, $arrParams));
  }

  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Tile
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Tile()); }
}
