<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageEquipmentsBean
 * @author Hugues
 * @since 1.04.15
 * @version 1.04.26
 */
class WpPageEquipmentsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-equipments.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->EquipmentServices          = new EquipmentServices();
    $this->EquipmentExpansionServices = new EquipmentExpansionServices();
    $this->ExpansionServices          = new ExpansionServices();
  }
  /**
   * @return string
   */
  public function getContentPage()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On construit la liste déroulante des Extensions ayant des cartes Equipements
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(), self::FIELD_DISPLAYRANK);
    $strExpansions = '';
    foreach ($Expansions as $Expansion) {
      $id = $Expansion->getId();
      $arrFilters = array(self::FIELD_EXPANSIONID=>$id);
      $EquipmentExpansions = $this->EquipmentExpansionServices->getEquipmentExpansionsWithFilters($arrFilters);
      // Si on n'a pas de carte Equipement rattachée, on n'a pas besoin d'afficher cette extension.
      if (empty($EquipmentExpansions)) {
        continue;
      }
      // On peut ajouter l'Extension au menu pour filtrer.
      $strExpansions .= $this->getBalise(self::TAG_OPTION, $Expansion->getName(), array(self::ATTR_VALUE=>'set-'.$id));

      /////////////////////////////////////////////////////////////////////////////
      // On récupère l'ensemble des cartes de l'extension.
      foreach ($EquipmentExpansions as $EquipmentExpansion) {
        $EquipmentCard = $this->EquipmentServices->selectEquipment($EquipmentExpansion->getEquipmentCardId());
        $niceName = $EquipmentCard->getNiceName().'-'.$EquipmentCard->getId().'-'.$id;
        $EquipmentCard->setExpansionId($id);
        // Par contre, vu qu'on s'appuye sur la tablede jointure, on ne peut pas directement trier les cartes.
        // On les stocke donc temporairement.
        $EquipmentCardsToDisplay[$niceName] = $EquipmentCard;
      }
    }

    /////////////////////////////////////////////////////////////////////////////
    // On construit la liste des cartes à afficher.
    $strCartes = '';
    if (!empty($EquipmentCardsToDisplay)) {
      // On trie les cartes selon leur nom.
      ksort($EquipmentCardsToDisplay);
      foreach ($EquipmentCardsToDisplay as $name => $EquipmentCard) {
        list(, , $id) = explode('-', $name);
        $EquipmentBean = new EquipmentBean($EquipmentCard);
        $strCartes .= $EquipmentBean->displayCard();
      }
    }

    /////////////////////////////////////////////////////////////////////////////
    // On construit la liste déroulante pour les filtres "mots-clés" (mais pas que).
    $arr = array(
      'weapon'=>'Armes',
      'melee'=>'Armes de Mêlée',
      'ranged'=>'Armes A distance',
      'pimp'=>'Armes Pimp',
      'dual'=>'Armes Dual',
      'starter'=>'Equipement de départ'
    );
    $strCategories  = '';
    foreach ($arr as $key => $value) {
      $strCategories .= $this->getBalise(self::TAG_OPTION, $value, array(self::ATTR_VALUE=>$key));
    }


    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste des Survivants puis les éléments nécessaires à la pagination.
    /*
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $nbElements = count($Survivors);
    $nbPages = ceil($nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedSurvivors = array_slice($Survivors, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSurvivors)) {
      foreach ($displayedSurvivors as $Survivor) {
        $strBody .= $Survivor->getBean()->getRowForSurvivorsPage();
      }
    }

    // On construit les liens de la pagination.
    $strPagination = $this->getPaginateLis($this->paged, $nbPages);

    // Affiche-t-on le filtre ?
    $showFilters = isset($this->arrFilters[self::FIELD_NAME])&&$this->arrFilters[self::FIELD_NAME]!='' || isset($this->arrFilters[self::FIELD_EXPANSIONID])&&$this->arrFilters[self::FIELD_EXPANSIONID]!='';
    */
    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Liste des cartes - 1
      $strCartes,
      // Options de sélection des extensions - 2
      $strExpansions,
      // Options de sélection de catégories. - 3
      $strCategories,
      '', '', '', '', '',
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  /**
   * @return string
   */
  public function getExpansionFilters($expansionId='')
  {
    $selExpansionsId = explode(',', $expansionId);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters();
    $strReturned = '';
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      if ($Expansion->getNbSurvivants()==0)
      { continue; }
      $strReturned .= '<option value="'.$Expansion->getId().'"';
      if (in_array($Expansion->getId(), $selExpansionsId)) {
        $strReturned .= ' selected';
      }
      $strReturned .= '>'.$Expansion->getName().'</option>';
    }
    return $strReturned;
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  {
    $this->arrFilters = array();
    if (isset($post[self::CST_FILTERS])) {
      $arrParams = explode('&', $post[self::CST_FILTERS]);
      while (!empty($arrParams)) {
        $arrParam = array_shift($arrParams);
        list($key, $value) = explode('=', $arrParam);
        if ($value!='') {
          $this->arrFilters[$key]= $value;
        }
      }
    }
    $this->paged     = (isset($post[self::AJAX_PAGED]) ? $post[self::AJAX_PAGED] : 1);
    $this->colSort   = (isset($post[self::CST_COLSORT]) ? $post[self::CST_COLSORT] : self::FIELD_NAME);
    $this->colOrder  = (isset($post[self::CST_COLORDER]) ? $post[self::CST_COLORDER] : self::ORDER_ASC);
    $this->nbperpage = (isset($post[self::CST_NBPERPAGE]) ? $post[self::CST_NBPERPAGE] : 10);
  }
}
