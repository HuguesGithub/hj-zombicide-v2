<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe LocalServices
 * @author Hugues.
 * @since 1.00.00
 * @version 1.05.20
 */
class LocalServices extends GlobalServices implements ConstantsInterface
{
  protected $arrParams = array();
  /**
   * Texte par défaut du Select
   * @var string $labelDefault
   */
  protected $labelDefault = '';
  /**
   * Valeur par défaut de la classe du Select
   * @var string $classe
   */
  protected $classe = 'form-control';
  /**
   * Le Select est-il multiple ?
   * @var boolean $multiple
   */
  protected $multiple = false;

  /**
   * Class Constructor
   */
  public function __construct()
  {
  }

  /**
   * @param array $arrFilters
   * @param string $field
   * @return string
   */
  protected function addFilter($arrFilters, $field)
  {
    return (isset($arrFilters[$field]) && !empty($arrFilters[$field]) ? $arrFilters[$field] : '%');
  }
  /**
   * @param array $arrFilters
   * @param string $field
   * @return string
   */
  protected function addNonArrayFilter($arrFilters, $field, $defaultSearch='%')
  {
    return (isset($arrFilters[$field]) && !empty($arrFilters[$field]) && !is_array($arrFilters[$field]) ? $arrFilters[$field] : $defaultSearch);
  }
  /**
   * @param array $arrFilters
   * @param string $field
   * @return string
   */
  protected function addNonArrayWideFilter($arrFilters, $field, $defaultSearch='%')
  {
    return (isset($arrFilters[$field]) && !empty($arrFilters[$field]) && !is_array($arrFilters[$field]) ? '%'.$arrFilters[$field].'%' : $defaultSearch);
  }


  public function prepObject($Obj, $isUpdate=false) {
    $arr = array();
    $vars = $Obj->getClassVars();
    if ( !empty($vars) ) {
      foreach ( $vars as $key=>$value ) {
        if ( $key=='id' ) { continue; }
        $arr[] = $Obj->getField($key);
      }
      if ( $isUpdate ) { $arr[] = $Obj->getField('id'); }
    }
    return $arr;
  }








  /**
   * @param array $arrSetLabels
   * @param string $name
   * @param string $value
   * @return string
   */
  protected function getSetSelect($arrSetLabels, $name, $value)
  {
    $strSelect = '';
    $selName = $name;
    if ($this->labelDefault!='') {
      $strSelect .= '<label class="screen-reader-text" for="'.$name.'">'.$this->labelDefault.'</label>';
    }
    // On créé la base du select
    $strSelect .= '<select id="'.$name.'" name="'.$selName.'" class="'.$this->classe.'"'.($this->multiple?' multiple':'').'>';
    // S'il n'est pas multiple et qu'il a une valeur par défaut, on la met.
    if (!$this->multiple && $this->labelDefault!='') {
      $strSelect .= '<option value="">'.$this->labelDefault.'</option>';
    }
    // On parcourt l'ensemble des couples $key/$value de la liste
    if (!empty($arrSetLabels)) {
      foreach ($arrSetLabels as $key => $labelValue) {
        // Visiblement, la $key peut parfois être nulle et c'est mal.
        if ($key=='') {
          continue;
        }
        // On construit l'option.
        $strSelect .= '<option value="'.$key.'"';
        $strSelect .= ($this->isKeySelected($key, $value) ? ' selected="selected"' : '');
        $strSelect .= '>'.$labelValue.'</option>';
      }
    }
    return $strSelect.'</select>';
  }
  /**
   * @param string $key
   * @param mixed $values
   * @return boolean
   */
  protected function isKeySelected($key, $values)
  {
    // Si on ne cherche pas dans un tableau, on teste juste l'égalité.
    if (!is_array($values)) {
      return trim($key)==trim($values);
    }
    $isSelected = false;
    // Sinon, on parcourt la liste pour essayer de trouver la valeur cherchée.
    while (!empty($values)) {
      $value = array_shift($values);
      if ($key==$value) {
        $isSelected = true;
      }
    }
    return $isSelected;
  }
  /**
   * Vérifie qu'un élément du tableau n'est ni vide ni un tableau.
   * @param array $arrFilters
   * @param string $tag
   * @return boolean
   */
  protected function isNonEmptyAndNoArray($arrFilters, $tag)
  { return !empty($arrFilters[$tag]) && !is_array($arrFilters[$tag]); }

  /**
   * @return int
   */
  public static function getWpUserId()
  { return get_current_user_id(); }




}
