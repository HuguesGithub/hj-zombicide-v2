<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * ExpansionActions
 * @author Hugues
 * @since 1.04.30
 * @version 1.04.30
 */
class ExpansionActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->ExpansionServices  = new ExpansionServices();
    $this->SurvivorServices   = new SurvivorServices();
    $this->WpPostServices     = new WpPostServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new ExpansionActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_EXPANSIONVERIF) {
      $returned = $Act->dealWithExpansionVerif(true);
    } else {
      $returned  = 'Erreur dans ExpansionActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
    }
    return $returned;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de vérifications des Extensions en Home Admin
  /**
   * @param boolean $isVerif
   * @return string
   */
  public function dealWithExpansionVerif($isVerif=false)
  {
    // On récupère les articles d'extensions
    $this->WpPostExpansions = $this->WpPostServices->getWpPostByCategoryId(self::WP_CAT_EXPANSION_ID);
    $nbWpPostExpansions = count($this->WpPostExpansions);
    // Et les extensions en base
    $this->Expansions = $this->ExpansionServices->getExpansionsWithFilters();
    $nbExpansions = count($this->Expansions);

    if ($isVerif) {
      $this->checkExpansions();
      $strBilan = $this->jsonString($this->strBilan, self::AJAX_EXPANSIONVERIF, true);
    } elseif ($nbWpPostExpansions!=$nbExpansions) {
      $strBilan  = "Le nombre d'articles ($nbWpPostExpansions) ne correspond pas au nombre d'extensions en base ($nbExpansions).<br>";
      $strBilan .= "Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostExpansions) correspond au nombre d'extensions en base.";
    }
    return $strBilan;
  }
  private function checkExpansions()
  {
    // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
    while (!empty($this->WpPostExpansions)) {
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostExpansions);
      $name = $this->WpPost->getPostTitle();
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      // On recherche un Expansion dans la base de données qui correspond.
      $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$code));
      if (empty($Expansions)) {
        // Si on n'en a pas, on doit créer une entrée correspondante.
        $Expansion = new Expansion();
        $Expansion->setCode($code);
        $Expansion->setName($name);
        $Expansion->setDisplayRank($this->WpPost->getPostMeta(self::FIELD_DISPLAYRANK));
        $Expansion->setOfficial($this->WpPost->getPostMeta(self::FIELD_OFFICIAL));
        $this->ExpansionServices->insertExpansion($Expansion);
        $this->strBilan .= 'Extension créée en base : '.$name.'.<br>';
      } else {
        $Expansion = array_shift($Expansions);
        $this->checkExpansion($Expansion);
      }
    }
    // Puis, on regarde les données en base et on vérifie que des articles ont été créés pour elles.
    while (!empty($this->Expansions)) {
      // On récupère l'extension.
      $Expansion = array_shift($this->Expansions);
      $code = $Expansion->getCode();
      $args = array(
        self::WP_NUMBERPOSTS  => -1,
        self::WP_POSTTYPE     => self::WP_POST,
        self::WP_METAKEY      => self::FIELD_CODE,
        self::WP_METAVALUE    => $code,
        self::WP_TAXQUERY     => array(),
        self::WP_CAT          => self::WP_CAT_EXPANSION_ID,
      );
      $WpPost = $this->WpPostServices->getArticles($args);
      if (empty($WpPost)) {
        $this->strBilan .= 'Article à créer pour une Extension  : '.$Expansion->getName().' ['.$Expansion->toJson().'].<br>';
      }
    }
    if ($this->strBilan=='') {
      $this->strBilan = 'Il semblerait que tout aille à la perfection. Aucune anomalie remontée.';
    }
  }
  private function checkExpansion($Expansion, $doCreate=false)
  {
    // On initialise les données
    $doUpdate = false;
    $code         = $this->WpPost->getPostMeta(self::FIELD_CODE);
    $name         = $this->WpPost->getPostTitle();
    $displayRank  = $this->WpPost->getPostMeta(self::FIELD_DISPLAYRANK);
    $official     = $this->WpPost->getPostMeta(self::FIELD_OFFICIAL);
    // On vérifie si la donnée en base correspond à l'article.
    if ($Expansion->getName()!=$name) {
      $Expansion->setName($name);
      $doUpdate = true;
    }
    if ($Expansion->getDisplayRank()!=$displayRank) {
      $Expansion->setDisplayRank($displayRank);
      $doUpdate = true;
    }
    if ($Expansion->isOfficial()!=$official) {
      $Expansion->setOfficial($official);
      $doUpdate = true;
    }
    // On peut aussi envisager de mettre à jour les chaps nbSurvivants et nbMissions...
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters(array(self::FIELD_EXPANSIONID=>$Expansion->getId()));
    if ($Expansion->getNbSurvivants()!=count($Survivors)) {
      $Expansion->setNbSurvivants(count($Survivors));
      $doUpdate = true;
    }
    $Missions = $Expansion->getMissions();
    if ($Expansion->getNbMissions()!=count($Missions)) {
      // TODO : Données à mettre à jour avant de perdre plein d'infos à la con.
//      $this->strBilan .= $name.' a en base : '.$Expansion->getNbMissions().' et devrait avoir '.count($Missions).' Missions.<br>';
//      $Expansion->setNbMissions(count($Missions));
//      $doUpdate = true;
    }
    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->ExpansionServices->updateExpansion($Expansion);
      $this->strBilan .= 'Extension mise à jour : '.$name.'.<br>';
    }
  }
  // Fin du bloc relatif à la vérification d'extensions sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}
