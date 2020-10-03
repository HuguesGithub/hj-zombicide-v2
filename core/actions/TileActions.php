<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * TileActions
 * @author Hugues
 * @since 1.08.30
 */
class TileActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->TileServices  = new TileServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new TileActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETTILES) {
      $returned = $Act->dealWithGetTiles(true);
    } else {
      $returned  = 'Erreur dans TileActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
    }
    return $returned;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de la récupération des dalles selon une extension

  public function dealWithGetTiles()
  {
    //////////////////////////////////////////////////////////////////////
    // Initialisation des variables
    $expansionId = $this->post['idSet'];
    $Tiles = $this->TileServices->getTilesWithFilters(array(self::FIELD_EXPANSIONID=>$expansionId), self::FIELD_CODE, self::ORDER_ASC);

    //////////////////////////////////////////////////////////////////////
    // On parcourt la liste des Tiles pour les afficher.
    $result = '';
    while (!empty($Tiles)) {
      $Tile = array_shift($Tiles);
      $result .= '<div class="card"><img class="card-img-top" src="'.$Tile->getImgUrl().'"/></div>';
    }

    $result = '<div id="tile-container"><div class="card-columns">'.$result.'</div></div>';
    return $this->jsonString($result, 'tile-container', true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}

