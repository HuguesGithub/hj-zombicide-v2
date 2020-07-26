<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe OrigineBean
 * @author Hugues
 * @since 1.07.25
 * @version 1.07.25
 */
class OrigineBean extends LocalBean
{

  /**
   * @param Expansion $Origine
   */
  public function __construct($Origine=null)
  {
    parent::__construct();
    $this->Origine = ($Origine==null ? new Origine() : $Origine);
    $this->OrigineServices = new OrigineServices();
  }

  public static function getStaticSelect($name, $selId=0)
  {
    $OrigineBean = new OrigineBean();
    return $OrigineBean->getSelect($name, $selId);
  }
  public function getSelect($name, $selId)
  {
    $Origines = $this->OrigineServices->getOriginesWithFilters();
    $strOptions = $this->getBalise(self::TAG_OPTION, 'Toutes Origines', array(self::ATTR_VALUE=>0));
    while (!empty($Origines)) {
      $Origine = array_shift($Origines);
      $args = array(self::ATTR_VALUE=>$Origine->getId());
      if ($Origine->getId()==$selId) {
        $args[self::ATTR_SELECTED] = self::CST_SELECTED;
      }
      $strOptions .= $this->getBalise(self::TAG_OPTION, $Origine->getName(), $args);
    }
    return $this->getBalise(self::TAG_SELECT, $strOptions, array(self::ATTR_NAME=>$name, self::ATTR_ID=>'filter-by-'.$name));
  }

}
