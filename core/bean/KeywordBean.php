<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe KeywordBean
 * @author Hugues
 * @since 1.0.00
 * @version 1.0.00
 */
class KeywordBean extends LocalBean
{
  /**
   * Class Constructor
   * @param Keyword $Keyword
   */
  public function __construct($Keyword='')
  {
    parent::__construct();
    $this->Keyword = ($Keyword=='' ? new Keyword() : $Keyword);
    $this->EquipmentKeywordServices = new EquipmentKeywordServices();
  }
  /**
   * @param string $tBodyButtons Template des Boutons de fin de ligne
   * @return string
   */
  public function getRowForAdminPage($tBodyButtons)
  {
    $Keyword = $this->Keyword;
    $arrF = array('keywordId'=>$Keyword->getId());
    $Equipments = $this->EquipmentKeywordServices->getEquipmentKeywordsWithFilters(__FILE__, __LINE__, $arrF);
    $nb = count($Equipments);
    $queryArg = array(
      self::CST_ONGLET=>'parametre',
      self::CST_POSTACTION=>'edit',
      'table'=>'keyword',
      'id'=>$Keyword->getId()
    );
    $urlEdit = $this->getQueryArg($queryArg);
    $queryArg[self::CST_POSTACTION] = 'trash';
    $urlTrash = $this->getQueryArg($queryArg);
    $args = array(
      $nb.' Carte'.($nb>1?'s':'').' Equipement',
      $urlEdit,
      $urlTrash,
    );
    $tBody = '<tr><td>'.$Keyword->getId().'</td><td>'.$Keyword->getName().'</td><td>'.$Keyword->getDescription().'</td>';
    return $tBody.vsprintf($tBodyButtons, $args).'</tr>';
  }
}
