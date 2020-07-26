<?php
/**
 * @author Hugues
 * @since 1.00.00
 * @version 1.07.25
 */
interface ConstantsInterface
{
  /////////////////////////////////////////////////
  // Icons : https://pngtree.com/free-icon/backpack-management_577946
  // Action Ajax
  const AJAX_ADDMORENEWS    = 'addMoreNews';
  const AJAX_CARDRIVE       = 'carDrive';
  const AJAX_CARIN          = 'carIn';
  const AJAX_CAROUT         = 'carOut';
  const AJAX_CARSWAP        = 'carSwap';
  const AJAX_ENDOFTURN      = 'endOfTurn';
  const AJAX_GETEXPANSIONS  = 'getExpansions';
  const AJAX_EXPANSIONVERIF = 'expansion-verif';
  const AJAX_GETMISSIONS    = 'getMissions';
  const AJAX_GETRANDOMTEAM  = 'getRandomTeam';
  const AJAX_GETSKILLS      = 'getSkills';
  const AJAX_GETSURVIVORS   = 'getSurvivants';
  const AJAX_MAKENOISE      = 'makeNoise';
  const AJAX_MELEEATTACK    = 'meleeAttack';
  const AJAX_MISSIONVERIF   = 'mission-verif';
  const AJAX_MOVE           = 'move';
  const AJAX_OPENDOOR       = 'openDoor';
  const AJAX_ORGANIZE       = 'organize';
  const AJAX_PAGED          = 'paged';
  const AJAX_POSTCHAT       = 'postChat';
  const AJAX_RANGEDATTACK   = 'rangedAttack';
  const AJAX_REFRESHCHAT    = 'refreshChat';
  const AJAX_SEARCH         = 'search';
  const AJAX_SKILLVERIF     = 'skill-verif';
  const AJAX_STARTTURN      = 'startTurn';
  const AJAX_SURVIVORVERIF  = 'survivor-verif';
  const AJAX_TRADE          = 'trade';
  const AJAX_GETTHROWDICE   = 'getThrowDice';

  /////////////////////////////////////////////////
  // Attributs
  const ATTR_ALT               = 'alt';
  const ATTR_CLASS             = 'class';
  const ATTR_HEIGHT            = 'height';
  const ATTR_HREF              = 'href';
  const ATTR_ID                = 'id';
  const ATTR_NAME              = 'name';
  const ATTR_SELECTED          = 'selected';
  const ATTR_SRC               = 'src';
  const ATTR_TITLE             = 'title';
  const ATTR_TYPE              = 'type';
  const ATTR_VALUE             = 'value';
  const ATTR_WIDTH             = 'width';
  // Data
  const ATTR_DATA_AJAXACTION   = 'data-ajaxaction';
  const ATTR_DATA_DISPLAYNAME  = 'data-displayname';
  const ATTR_DATA_EXPANSIONID  = 'data-expansion-id';
  const ATTR_DATA_ID           = 'data-id';
  const ATTR_DATA_KEYDECK      = 'data-keydeck';
  const ATTR_DATA_PAGED        = 'data-paged';
  const ATTR_DATA_SURVIVORID   = 'data-survivor-id';
  const ATTR_DATA_TIMESTAMP    = 'data-timestamp';
  const ATTR_DATA_TYPE         = 'data-type';

  /////////////////////////////////////////////////
  // Chat
  const CHAT_CLEAN           = '/clean';
  const CHAT_EXIT            = '/exit';
  const CHAT_GAMES           = '/games';
  const CHAT_HELP            = '/help';
  const CHAT_INVITE          = '/invite';
  const CHAT_JOIN            = '/join';
  const CHAT_USERS           = '/users';
  const CHAT_ACTIVATEZOMBIES = '/activateZombies';

  /////////////////////////////////////////////////
  // Les niveaux de danger
  const COLOR_BLUE           = 'blue';
  const COLOR_ORANGE         = 'orange';
  const COLOR_RED            = 'red';
  const COLOR_YELLOW         = 'yellow';

  /////////////////////////////////////////////////
  // On conserve malgré tout quelques constantes
  const CST_ACTIVE            = 'active';
  const CST_AJAXACTION        = 'ajaxAction';
  const CST_CHANGEPROFILE     = 'changeProfile';
  const CST_CHECKED           = 'checked';
  const CST_CLONE             = 'clone';
  const CST_COLORDER          = 'colorder';
  const CST_COLSORT           = 'colsort';
  const CST_DISABLED          = 'disabled';
  const CST_EDIT              = 'edit';
  const CST_EXPANSION         = 'expansion';
  const CST_FILTERS           = 'filters';
  const CST_HIDDEN            = 'hidden';
  const CST_NBPERPAGE         = 'nbperpage';
  const CST_MISSION           = 'mission';
  const CST_ONGLET            = 'onglet';
  const CST_PARAMETRE         = 'parametre';
  const CST_POSTACTION        = 'postAction';
  const CST_SELECTED          = 'selected';
  const CST_SKILL             = 'skill';
  const CST_SURVIVOR          = 'survivor';
  const CST_SURVIVORTYPEID_S  = 1;
  const CST_SURVIVORTYPEID_Z  = 2;
  const CST_SURVIVORTYPEID_U  = 3;
  const CST_SURVIVORTYPEID_UZ = 4;
  const CST_TRASH             = 'trash';
  const CST_ULTIMATE          = 'ultimate';
  const CST_ULTIMATEZ         = 'ultimatez';
  const CST_ZOMBIVOR          = 'zombivor';

  /////////////////////////////////////////////////
  // Fields
  const FIELD_ID               = 'id';
  const FIELD_ACTIVETILE       = 'activeTile';
  const FIELD_BACKGROUND       = 'background';
  const FIELD_CODE             = 'code';
  const FIELD_COORDX           = 'coordX';
  const FIELD_COORDY           = 'coordY';
  const FIELD_DATEUPDATE       = 'dateUpdate';
  const FIELD_DECKKEY          = 'deckKey';
  const FIELD_DESCRIPTION      = 'description';
  const FIELD_DISPLAYRANK      = 'displayRank';
  const FIELD_DURATIONID       = 'durationId';
  const FIELD_EQUIPMENTCARDID  = 'equipmentCardId';
  const FIELD_EXPANSIONID      = 'expansionId';
  const FIELD_HEIGHT           = 'height';
  const FIELD_KEYWORDID        = 'keywordId';
  const FIELD_LIVEABLE         = 'liveAble';
  const FIELD_LIVEID           = 'liveId';
  const FIELD_LEVELID          = 'levelId';
  const FIELD_MAXDURATION      = 'maxDuration';
  const FIELD_MINDURATION      = 'minDuration';
  const FIELD_MISSIONID        = 'missionId';
  const FIELD_NAME             = 'name';
  const FIELD_NBMISSIONS       = 'nbMissions';
  const FIELD_NBSURVIVANTS     = 'nbSurvivants';
  const FIELD_OBJECTIVEID      = 'objectiveId';
  const FIELD_OFFICIAL         = 'official';
  const FIELD_ORIGINEID        = 'origineId';
  const FIELD_PLAYERID         = 'playerId';
  const FIELD_PUBLISHED        = 'published';
  const FIELD_RULEID           = 'ruleId';
  const FIELD_SENDERID         = 'senderId';
  const FIELD_SENDTOID         = 'sendToId';
  const FIELD_SETTING          = 'setting';
  const FIELD_SKILLID          = 'skillId';
  const FIELD_SPAWNNUMBER      = 'spawnNumber';
  const FIELD_SURVIVORID       = 'survivorId';
  const FIELD_SURVIVORTYPEID   = 'survivorTypeId';
  const FIELD_TAGLEVELID       = 'tagLevelId';
  const FIELD_TEXTE            = 'texte';
  const FIELD_TILEID           = 'tileId';
  const FIELD_TIMESTAMP        = 'timestamp';
  const FIELD_TITLE            = 'title';
  const FIELD_ULTIMATE         = 'ultimate';
  const FIELD_WEAPONPROFILEID  = 'weaponProfileId';
  const FIELD_WIDTH            = 'width';
  const FIELD_ZOMBIVOR         = 'zombivor';

  /////////////////////////////////////////////////
  // Formats
  const FORMAT_DATE_YmdHis     = 'Y-m-d H:i:s';

  /////////////////////////////////////////////////
  // Identifiant DOM
  const ID_HEADER_UL_CHAT_SAISIE = 'header-ul-chat-saisie';
  const ID_ONLINE_CHAT_CONTENT   = 'online-chat-content';

  /////////////////////////////////////////////////
  // Labels
  const LBL_STANDARD             = 'Standard';
  const LBL_SURVIVANT            = 'Survivant';
  const LBL_ZOMBIVANT            = 'Zombivant';
  const LBL_ULTIMATE             = 'Ultimate';
  const LBL_ULTIMATEZOMBIVANT    = 'Ultimate Zombivant';

  /////////////////////////////////////////////////
  // Niveau de Danger
  const LVL_BLUE                 = 'blue';
  const LVL_YELLOW               = 'yellow';
  const LVL_ORANGE               = 'orange';
  const LVL_RED                  = 'red';

  /////////////////////////////////////////////////
  // Messages
  const MSG_CHAT_EMPTIED        = 'CHAT_EMPTIED';
  const MSG_CHAT_USER_LEFT      = 'CHAT_USER_LEFT';
  const MSG_CHAT_BACK_DEFAULT   = 'CHAT_BACK_DEFAULT';
  const MSG_CHAT_HELP           = 'CHAT_HELP';
  const MSG_CHAT_UNKNOWN_USER   = 'CHAT_UNKNOWN_USER';
  const MSG_CHAT_JOIN_INVITE    = 'CHAT_JOIN_INVITE';
  const MSG_CHAT_INVITE_SENT_TO = 'CHAT_INVITE_SENT_TO';
  const MSG_CHAT_USER_JOINED    = 'CHAT_USER_JOINED';

  /////////////////////////////////////////////////
  // Allowed Pages :
  const PAGE_ONLINE            = 'online';
  const PAGE_EQUIPMENT         = 'page-equipmentcards';
  const PAGE_EXTENSION         = 'page-extensions';
  const PAGE_MISSION           = 'page-missions';
  const PAGE_PISTE_DE_DES      = 'page-piste-de-des';
  const PAGE_SELECT_SURVIVORS  = 'page-selection-survivants';
  const PAGE_SKILL             = 'page-competences';
  const PAGE_SPAWN             = 'page-spawncards';
  const PAGE_SURVIVOR          = 'page-survivants';

  /////////////////////////////////////////////////
  // Session
  const SESSION_DECKKEY        = 'deckKey';

  const SQL_WHERE              = 'where';

  /////////////////////////////////////////////////
  // Tags
  const TAG_A                  = 'a';
  const TAG_BUTTON             = 'button';
  const TAG_DIV                = 'div';
  const TAG_I                  = 'i';
  const TAG_IMG                = 'img';
  const TAG_LI                 = 'li';
  const TAG_OPTION             = 'option';
  const TAG_SELECT             = 'select';
  const TAG_SPAN               = 'span';
  const TAG_UL                 = 'ul';

  /////////////////////////////////////////////////
  // Wordpress
  const WP_CAT                 = 'cat';
  const WP_CAT_EXPANSION_ID    = 77;
  const WP_CAT_MISSION_ID      = 2;
  const WP_CAT_NEWS_ID         = 54;
  const WP_CAT_OBJECTIVE_ID    = 71;
  const WP_CAT_RULE_ID         = 72;
  const WP_CAT_SKILL_ID        = 75;
  const WP_CAT_SURVIVOR_ID     = 58;
  const WP_CURPAGE             = 'cur_page';
  const WP_FIELD               = 'field';
  const WP_METAKEY             = 'meta_key';
  const WP_METAVALUE           = 'meta_value';
  const WP_NUMBERPOSTS         = 'numberposts';
  const WP_OFFSET              = 'offset';
  const WP_ORDER               = 'order';
  const WP_ORDERBY             = 'orderby';
  const WP_POST                = 'post';
  const WP_POSTSPERPAGE        = 'posts_per_page';
  const WP_POSTSTATUS          = 'post_status';
  const WP_POSTTAG             = 'post_tag';
  const WP_POSTTITLE           = 'post_title';
  const WP_POSTTYPE            = 'post_type';
  const WP_PUBLISH             = 'publish';
  const WP_SLUG                = 'slug';
  const WP_TAXONOMY            = 'taxonomy';
  const WP_TAXQUERY            = 'tax_query';
  const WP_TERMS               = 'terms';

  /////////////////////////////////////////////////
  // Divers
  const IMG_PNG                = '.png';
  const ORDER_ASC              = 'ASC';
  const ORDER_DESC             = 'DESC';
  const ORDER_RAND             = 'rand';



  /////////////////////////////////////////////////
  // Deprecated
  const CST_TIMESTAMP          = 'timestamp';




  /**
   * Chaîne Constante coordY
   */
  const CST_COORDY          = 'coordY';
  /**
   * Chaîne Constante cur_page
   */
  const CST_CURPAGE         = 'cur_page';
  /**
   * Chaîne Constante current
   */
  const CST_CURRENT         = 'current';
  /**
   * Chaîne Constante danger
   */
  const CST_DANGER          = 'danger';
  /**
   * Chaîne Constante description
   */
  const CST_DESCRIPTION     = 'description';
  /**
   * Chaîne Constante durationId
   */
  const CST_DURATIONID      = 'durationId';
  /**
   * Chaîne Constante equipment
   */
  const CST_EQUIPMENT       = 'equipment';
  /**
   * Chaîne Constante equipmentCardId
   */
  const CST_EQUIPMENTCARDID = 'equipmentCardId';
  /**
   * Chaîne Constante Y-m-d H:i:s
   */
  const CST_FORMATDATE      = 'Y-m-d H:i:s';
  /**
   * Chaîne Constante form-control
   */
  const CST_FORMCONTROL     = 'form-control';
  /**
   * Chaîne Constante firstRow
   */
  const CST_FIRSTROW        = 'firstRow';
  /**
   * Chaîne Constante future
   */
  const CST_FUTURE          = 'future';
  /**
   * Chaîne Constante keyAccess
   */
  const CST_KEYACCESS       = 'keyAccess';
  /**
   * Chaîne Constante level
   */
  const CST_LEVEL           = 'level';
  /**
   * Chaîne Constante levelId
   */
  const CST_LEVELID         = 'levelId';
  /**
   * Chaîne Constante liveDeckId
   */
  const CST_LIVEDECKID      = 'liveDeckId';
  /**
   * Chaîne Constante liveId
   */
  const CST_LIVEID          = 'liveId';
  /**
   * Chaîne Constante liveSurvivorId
   */
  const CST_LIVESURVIVORID  = 'liveSurvivorId';
  /**
   * Chaîne Constante minDuration
   */
  const CST_MINDURATION     = 'minDuration';
  /**
   * Chaîne Constante missionId
   */
  const CST_MISSIONID       = 'missionId';
  /**
   * Chaîne Constante name
   */
  const CST_NAME            = 'name';
  /**
   * Chaîne Constante nbMissions
   */
  const CST_NBMISSIONS      = 'nbMissions';
  /**
   * Chaîne Constante objective
   */
  const CST_OBJECTIVE       = 'objective';
  /**
   * Chaîne Constante order
   */
  const CST_ORDER           = 'order';
  /**
   * Chaîne Constante orderby
   */
  /**
   * Chaîne Constante origineId
   */
  const CST_ORIGINEID       = 'origineId';
  /**
   * Chaîne Constante pending
   */
  const CST_PENDING         = 'pending';
  /**
   * Chaîne Constante playerId
   */
  const CST_PLAYERID        = 'playerId';
  /**
   * Chaîne Constante post_status
   */
  const CST_POSTSTATUS      = 'post_status';
  /**
   * Chaîne Constante publish
   */
  const CST_PUBLISH         = 'publish';
  /**
   * Chaîne Constante published
   */
  const CST_PUBLISHED       = 'published';
  /**
   * Chaîne Constante rmvCol
   */
  const CST_RMVCOL          = 'rmvCol';
  /**
   * Chaîne Constante rmvRow
   */
  const CST_RMVROW          = 'rmvRow';
  /**
   * Chaîne Constante selected
   */
  /**
   * Chaîne Constante sendToId
   */
  const CST_SENDTOID        = 'sendToId';
  /**
   * Chaîne Constante setting
   */
  const CST_SETTING         = 'setting';
  /**
   * Chaîne Constante spawn
   */
  const CST_SPAWN           = 'spawn';
  /**
   * Chaîne Constante spawnNumber
   */
  const CST_SPAWNNUMBER     = 'spawnNumber';
  /**
   * Chaîne Constante square pointer
   */
  const CST_SQUAREPOINTER   = 'square pointer';
  /**
   * Chaîne Constante status
   */
  const CST_STATUS          = 'status';
  /**
   * Chaîne Constante success
   */
  const CST_SUCCESS         = 'success';
  /**
   * Chaîne Constante survivorTypeId
   */
  const CST_SURVIVORTYPEID  = 'survivorTypeId';
  /**
   * Chaîne Constante table
   */
  const CST_TABLE           = 'table';
  /**
   * Chaîne Constante tagLevelId
   */
  const CST_TAGLEVELID      = 'tagLevelId';
  /**
   * Chaîne Constante </td><td>
   */
  const CST_TD_SEP          = '</td><td>';
  /**
   * Chaîne Constante texte
   */
  const CST_TEXTE           = 'texte';
  /**
   * Chaîne Constante upload_files
   */
  const CST_UPLOADFILES     = 'upload_files';
  /**
   * Chaîne Constante window-close
   */
  const CST_WINDOWCLOSE     = 'window-close';
}
