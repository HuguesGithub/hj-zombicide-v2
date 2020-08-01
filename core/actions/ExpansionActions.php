<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * ExpansionActions
 * @author Hugues
 * @since 1.04.30
 * @version 1.08.01
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
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_EXPANSIONVERIF :
        $returned = $Act->dealWithExpansionVerif(true);
      break;
      case self::AJAX_GETEXPANSIONS  :
        $returned = $Act->dealWithGetExpansions();
      break;
      default :
        $returned  = 'Erreur dans ExpansionActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
      break;
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetExpansions()
  {
    $Bean = new WpPageExpansionsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_EXTENSION, true);
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
    $args = array(
      self::WP_CAT         => self::WP_CAT_EXPANSION_ID,
      self::WP_TAXQUERY    => array(),
      self::WP_POSTSTATUS  => self::WP_PUBLISH.', future, pending',
    );
    $this->WpPostExpansions = $this->WpPostServices->getArticles($args);
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
    $hasErrors = false;
    $strErrors = '';
    $this->strBilan  = "Début de l'analyse des données relatives aux Extensions.<br>";
    $this->strBilan .= "Il y a ".count($this->WpPostExpansions)." articles d'Extensions.<br>";
    $this->strBilan .= "Il y a ".count($this->Expansions)." entrées en base.<br>";
    /////////////////////////////////////////////////////////////////////
    // On va réorganiser les Expansions pour les retrouver facilement
    $arrExpansions = array();
    while (!empty($this->Expansions)) {
      $Expansion = array_shift($this->Expansions);
      if (isset($arrExpansions[$Expansion->getCode()])) {
        $strErrors .= "Le code <em>".$Expansion->getCode()."</em> semble être utilisé deux fois dans la base de données.<br>";
        $hasErrors = true;
      }
      $arrExpansions[$Expansion->getCode()] = $Expansion;
    }
    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////
    while (!empty($this->WpPostExpansions)) {
      // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostExpansions);
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      if (!isset($arrExpansions[$code])) {
        // A priori l'article n'a pas de code associé en base. Il faut donc en créé un qui corresponde
        $Expansion = new Expansion();
        $Expansion->setCode($code);
        $name = $this->WpPost->getPostTitle();
        $Expansion->setName($name);
        $Expansion->setDisplayRank($this->WpPost->getPostMeta(self::FIELD_DISPLAYRANK));
        $Expansion->setOfficial($this->WpPost->getPostMeta(self::FIELD_OFFICIAL));
        // On insère la donnée et on log dans le bilan
        $this->ExpansionServices->insertExpansion($Expansion);
        $this->strBilan .= "L'article <em>".$name."</em> a été créé en base.<br>";
        continue;
      }
      $Expansion = $arrExpansions[$code];
      unset($arrExpansions[$code]);
      $this->checkExpansion($Expansion);
    }
    /////////////////////////////////////////////////////////////////////
    // On vérifie que la totalité des Compétences en base ont été utilisées. Si ce n'est pas le cas, il faut créer des articles correspondants.
    if (!empty($arrExpansions)) {
      $this->strBilan .= "On a des données en base qui n'ont pas d'article correspondant.<br>";
      while (!empty($arrExpansions)) {
        $Expansion = array_shift($arrExpansions);
        $this->strBilan .= '<br>Article à créer pour une Extension  : '.$Expansion->getName().' ['.$Expansion->toJson().'].<br>';
      }
    }
    /////////////////////////////////////////////////////////////////////
    $this->strBilan .= "Fin de l'analyse des données relatives aux Extensions.<br>";
    if ($hasErrors) {
      $this->strBilan .= "Anomalies constatées :<br>".$strErrors;
    } else {
      $this->strBilan .= "Aucune anomalie constatée.";
    }
  }
  private function checkExpansion($Expansion)
  {
    $doUpdate = false;
    // On initialise les données de l'article
    $name         = $this->WpPost->getPostTitle();
    $displayRank  = $this->WpPost->getPostMeta(self::FIELD_DISPLAYRANK);
    $official     = $this->WpPost->getPostMeta(self::FIELD_OFFICIAL);
    // On vérifie si la donnée en base correspond à l'article.
    $strError = '';
    if ($Expansion->getName()!=$name) {
      $Expansion->setName($name);
      $strError .= "Le Nom a été mis à jour.<br>";
      $doUpdate = true;
    }
    if ($Expansion->getDisplayRank()!=$displayRank) {
      $Expansion->setDisplayRank($displayRank);
      $strError .= "Le Rang d'affichage a été mis à jour.<br>";
      $doUpdate = true;
    }
    if ($Expansion->isOfficial()!=$official) {
      $Expansion->setOfficial($official);
      $strError .= "Le statut Officiel a été mis à jour.<br>";
      $doUpdate = true;
    }
    // On peut aussi envisager de mettre à jour les chaps nbSurvivants et nbMissions...
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters(array(self::FIELD_EXPANSIONID=>$Expansion->getId()));
    if ($Expansion->getNbSurvivants()!=count($Survivors)) {
      $Expansion->setNbSurvivants(count($Survivors));
      $strError .= "Le Nombre de Survivants a été mis à jour.<br>";
      $doUpdate = true;
    }
    /*
    // On vérifie si la donnée en base correspond à l'article.
    // On vérifie si la donnée en base correspond à l'article.
    $Missions = $Expansion->getMissions();
    if ($Expansion->getNbMissions()!=count($Missions)) {
      // TODO : Données à mettre à jour avant de perdre plein d'infos à la con.
    }
    */
    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->ExpansionServices->updateExpansion($Expansion);
      $this->strBilan .= "Les données de l'Extension <em>".$name."</em> ont été mises à jour.<br>".$strError;
    }
  }
  // Fin du bloc relatif à la vérification d'extensions sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}
