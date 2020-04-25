<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe EquipmentBean
 * @author Hugues
 * @since 1.0.00
 * @version 1.0.00
 */
class EquipmentBean extends LocalBean
{
  /**
   * Class Constructor
   * @param Equipment $EquipmentCard
   */
  public function __construct($EquipmentCard='')
  {
    parent::__construct();
    $this->EquipmentCard = ($EquipmentCard=='' ? new EquipmentCard() : $EquipmentCard);
  }
  /**
   * @param int $expansionId
   * @param int $equipmentLiveDeckId
   * @return string
   */
  public function displayCard($expansionId='', $equipmentLiveDeckId=-1)
  {
    $EquipmentCard = $this->EquipmentCard;
    if ($expansionId=='') {
      $expansionId = $EquipmentCard->getExpansionId();
    }
    $arrKeyWords = array();
    $strClasse = '';
    if ($EquipmentCard->isRanged()) {
      $strClasse .= ' ranged weapon';
    }
    if ($EquipmentCard->isMelee()) {
      $strClasse .= ' melee weapon';
    }
    if ($EquipmentCard->isPimp()) {
      $strClasse .= ' pimp';
      array_push($arrKeyWords, 'Pimp');
    }
    if ($EquipmentCard->isStarter()) {
      $strClasse .= ' starter';
      array_push($arrKeyWords, 'Starter');
    }
    if ($EquipmentCard->isDual()) {
      $strClasse .= ' dual';
      array_push($arrKeyWords, 'Dual');
    }
    $arrKeyWordsToCheck = array(
        'Embuscade', '9mm', 'Pistolet', 'Précision', 'Vivres', '12mm', 'Recharge', 'Effraction', 'Katana',
        'Munitions', 'Composite'
    );
    foreach ($arrKeyWordsToCheck as $keyWord) {
      if ($EquipmentCard->hasKeyword($keyWord)) {
        array_push($arrKeyWords, $keyWord);
      }
    }
    $args = array(
      // Identifiant de l'extension
      $expansionId,
      // Classe
      $strClasse.(self::isAdmin() ? ' hasTooltip' : ''),
      // Nom de l'équipement
      $EquipmentCard->getName(),
      // Liste des mots-clés pour Debug
      implode(', ', $arrKeyWords),
      // URL de l'image
      $EquipmentCard->getImgUrl($expansionId),
      // On montre le bouton Discard ou non - 6
      ($equipmentLiveDeckId!=-1?'':'hidden'),
      // On a besoin du KeyAccess - 7
      ($equipmentLiveDeckId!=-1?$_SESSION[self::CST_DECKKEY]:''),
      // On a besoin de l'id de l'EquipmentExpansion - 8
      ($equipmentLiveDeckId!=-1?$equipmentLiveDeckId:''),
    );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/public/fragments/card-equipment-article.php');
    return vsprintf($str, $args);
  }
}
