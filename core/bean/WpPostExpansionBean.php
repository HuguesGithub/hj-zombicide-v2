<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostExpansionBean
 * @author Hugues
 * @since 1.07.21
 * @version 1.08.01
 */
class WpPostExpansionBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-expansion.php';

  /**
   * Class Constructor
   */
  public function __construct($WpPost)
  {
    parent::__construct();
    $this->ExpansionServices = new ExpansionServices();
    $this->WpPost = $WpPost;
    $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$code));
    $this->Expansion = (!empty($Expansions) ? array_shift($Expansions) : new Expansion());

  }
  /**
   * On retourne la page dédiée à la compétence.
   * @return string
   */
  public function getContentPage()
  {
    if ($this->Expansion->isOfficial()) {
      $label = 'Officielle';
      $color = 'success';
    } else {
      $label = 'Custom';
      $color = 'danger';
    }
    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Nom de l'Extension - 1
      $this->Expansion->getName(),
      // Description de la Compétence - 2
      $this->WpPost->getPostContent(),
      // Lien de navigation - 3
      '',//$this->getNavLinks(),
      // Infos sur les Survivants / Dalles / Missions... - 4
      $this->Expansion->getBean()->getExpansionDetails(),
      // Mais aussi les cartes Equipements et Invasion... - 5
      $this->Expansion->getBean()->getCardsDetails(),
      // Badge officiel ou Custom - 6
      $this->getBalise(self::TAG_SPAN, $label, array(self::ATTR_CLASS=>'badge badge-'.$color)),
    );
    return $this->getRender($this->urlTemplate, $args);
  }

}
