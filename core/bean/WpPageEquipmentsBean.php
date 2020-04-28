<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageEquipmentsBean
 * @author Hugues
 * @since 1.04.15
 * @version 1.04.28
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
        $strCartes .= $EquipmentBean->displayCard($id);
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
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
