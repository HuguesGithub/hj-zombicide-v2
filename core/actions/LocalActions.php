<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * LocalActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.00
 */
class LocalActions extends GlobalActions implements ConstantsInterface
{
  /**
   * Class Constructor
   */
  public function __construct()
  {
  }

  /**
   * Retourne une chaine json
   * @param string $msg
   * @param string $id
   * @param boolean $directReturn
   * @return string
   */
  protected function jsonString($msg, $id, $directReturn)
  {
    $content = '"'.$id.'":'.json_encode($msg);
    return ($directReturn ? '{'.$content.'}' : $content);
  }
}
