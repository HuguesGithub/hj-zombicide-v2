<?php
if ( !defined( 'ABSPATH') ) die( 'Forbidden' );
/**
 * Classe WpTagServices
 * @author Hugues.
 * @since 1.04.16
 * @version 1.04.16
 */
class WpTagServices extends GlobalServices {

  public function __construct() { }

  public function getTagByName($name, $dbTag='wp_5')
  {
    $requete = "SELECT t.term_id AS termId FROM ".$dbTag."_terms t INNER JOIN ".$dbTag."_term_taxonomy tt ON t.term_id = tt.term_id WHERE name = '$name' AND taxonomy='post_tag';";
    $rows = MySQL::wpdbSelect($requete);
    if (empty($rows)) {
      return new WpTag();
    } else {
      $row = array_shift($rows);
      $tagId = $row->termId;
      return WpTag::convertElement(get_tag($tagId));
    }
  }

  public function getTagBySlug($slug='')
  {
    $requete = "SELECT term_id FROM wp_11_terms WHERE slug = '$slug';";
    $rows = MySQL::wpdbSelect($requete);
    if (empty($rows)) {
      return new WpTag();
    } else {
      $row = array_shift($rows);
      $tagId = $row->term_id;
      return WpTag::convertElement(get_tag($tagId));
    }
  }

  public function getTags()
  {
    $WpTags = array();
    $tags = get_tags();
    while (!empty($tags)) {
      // Qu'on convertit en WpTag
      array_push($WpTags, WpTag::convertElement(array_shift($tags)));
    }
    return $WpTags;
  }
}
?>
