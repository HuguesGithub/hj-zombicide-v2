<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * ExpansionActions
 * @author Hugues
 * @since 1.05.09
 * @version 1.05.09
 */
class ToolActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new ToolActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETTHROWDICE) {
      $returned = $Act->dealWithThrowDice(true);
    } else {
      $returned  = 'Erreur dans ToolActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
    }
    return $returned;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion du lancer de dés

  public function dealWithThrowDice()
  {
    $Bean = new UtilitiesBean();
    $tag = self::TAG_SPAN;
    //////////////////////////////////////////////////////////////////////
    // Initialisation des variables
    $params = $this->post['params'];
    $arrParams = explode('&', $params);
    while (!empty($arrParams)) {
      $param = array_shift($arrParams);
      list($key, $value) = explode('=', $param);
      $$key = $value;
    }

//    echo "[$nbDice][$seuil][$modif][$surunsix][$dual][$barbauto]";
    // Si on a un nombre dans Barbare / Mode Automatique, on prend le plus gros score entre le nombre de dés de l'arme et le nombre d'acteurs dans al Zone.
    $nbDice = max($nbDice, $barabauto);

    $arrDice = array();
    for ($i=0; $i<$nbDice; $i++) {
      $dice = rand(1, 6);
      if ($dice==1) {
        $color = self::COLOR_RED;
        $dice = min(6,max(1,$dice+$modif));
      } else {
        $dice = min(6,max(1,$dice+$modif));
        if ($dice>=6) {
          $color = self::COLOR_BLUE;
        } elseif ($dice>=$seuil) {
          $color = self::COLOR_YELLOW;
        } else {
          $color = self::COLOR_ORANGE;
        }
      }
      $attributes = array(
        self::ATTR_CLASS => 'badge badge-'.$color.'-skill',
      );

      array_push($arrDice, $Bean->getBalise($tag, $dice, $attributes));
    }

    $result = '';
    while (!empty($arrDice)) {
      $num = array_shift($arrDice);
      $result .= '['.$num.']';
    }
    $result = '<section id="page-piste-de-des">'.$result.'</section>';
    return $this->jsonString($result, self::PAGE_PISTE_DE_DES, true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}
