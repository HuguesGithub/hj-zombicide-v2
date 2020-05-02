<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe UtilitiesBean
 * @author Hugues
 * @since 1.02.00
 * @version 1.05.02
 */
class UtilitiesBean implements ConstantsInterface
{
  /**
   * @param string $balise
   * @param string $label
   * @param array $attributes
   * @return string
   */
  protected function getBalise($balise, $label='', $attributes=array())
  { return '<'.$balise.$this->getExtraAttributesString($attributes).'>'.$label.'</'.$balise.'>'; }
  /**
   * @param array $attributes
   * @return array
   */
  private function getExtraAttributesString($attributes)
  {
    $extraAttributes = '';
    if (!empty($attributes)) {
      foreach ($attributes as $key => $value) {
        $extraAttributes .= ' '.$key.'="'.$value.'"';
      }
    }
    return $extraAttributes;
  }
  /**
   * @param array $attributes
   * @return string
   */
  protected function getIcon($attributes=array())
  { return $this->getBalise(self::TAG_I, '', $attributes); }
  /**
   * @return string
   */
  protected function getIconFarCheckSquare()
  { return $this->getIcon(array(self::ATTR_CLASS=>'far fa-check-square')); }
  /**
   * @return string
   */
  protected function getIconFarSquare()
  { return $this->getIcon(array(self::ATTR_CLASS=>'far fa-square')); }
  /**
   * @return string
   */
  protected function getIconFarSquarePointer()
  { return $this->getIcon(array(self::ATTR_CLASS=>'far fa-square pointer')); }
  /**
   * @return string
   */
  protected function getIconFarWindowClose()
  { return $this->getIcon(array(self::ATTR_CLASS=>'far fa-window-close')); }
  /**
   * @param string $urlTemplate
   * @param array $args
   * @return string
   */
  public function getRender($urlTemplate, $args)
  {
    $pattern = "/web\/pages\/(admin|public)\/[fragments\/]?/";
    return (preg_match($pattern, $urlTemplate) ? vsprintf(file_get_contents(PLUGIN_PATH.$urlTemplate), $args) : '');
  }
}
