/*
 * @version 1.04.26
 */
var $hj = jQuery;
$hj(document).ready(function(){
  /***************
   *** #01 - Home
   *** Si on est sur la Homepage
   ***************/
  if ($hj('#homeSectionArticles').length!=0 ) {
    $hj('#more_news').click(function() {
      var offset = $hj('#homeSectionArticles article').length;
      addMoreNews(offset);
    });
    addPageSurvivantAjaxActions();
  }
  /***************
   *** #02 - Compétences
   *** Si on est sur la page du Listing des Compétences
   ***************/
  if ($hj('#page-competences').length!=0 ) {
    addPageCompetenceAjaxActions();
  }
  /***************
   *** #03 - Missions
   *** Si on est sur la page du Listing des Missions
   ***************/
  if ($hj('#page-missions').length!=0 ) {
    addPageMissionAjaxActions();
  }
  /***************
   *** #04 - Survivants
   *** Si on est sur la page du Listing des Survivants
   ***************/
  if ($hj('#page-survivants').length!=0 ) {
    addPageSurvivantAjaxActions();
  }
  /***************
   *** #05 - Cartes Equipement
   *** Si on est sur la page du Listing des Cartes Equipement
   ***************/
  if ($hj('#page-equipements').length!=0 ) {
    addPageCardEquipmentAjaxActions();
  }
  /***************
   *** #06 - Page Tools
   *** Si on est sur une des sous pages Tools
   ***************/
  if ($hj('#page-tools').length!=0 ) {
    addPageToolsAjaxActions();
  }
  /***************
   *** #05 - Extensions
   *** Si on est sur la page du Listing des Extensions
   ***************/
  if ($hj('#page-extensions').length!=0 ) {
    addPageExpansionAjaxActions();
  }

});

/********
 * Ajax Actions - HomePage
 ********/
function addMoreNews(offset) {
  var obj;
  var data = {'action': 'dealWithAjax', 'ajaxAction': 'addMoreNews', 'value': offset};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
        if (obj['homeSectionArticles'] != '' ) {
          $hj('#homeSectionArticles').append(obj['homeSectionArticles']);
          if ($hj('#homeSectionArticles>article').length%6 != 0 ) {
            $hj('#more_news').remove();
          }
        } else {
          $hj('#more_news').remove();
        }
        addPageSurvivantAjaxActions();
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
function addPageExpansionAjaxActions() {
  // On ajoute une Action sur les actions Ajax
  $hj('#page-extensions .ajaxAction').unbind().click(function(){
    resolvePageExpansionAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action sur le changement de nombre d'éléments à afficher par page
  $hj('#displayedRows').change(function(){
    resolvePageExpansionAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action pour afficher/cacher le formulaire de filtre
  $hj('i.fa-filter').unbind().click(function(){
    $hj('#filters input').val('');
    $hj('#filters').toggle();
  });
}
function addPageCompetenceAjaxActions() {
  // On ajoute une Action sur les actions Ajax
  $hj('#page-competences .ajaxAction').unbind().click(function(){
    resolvePageCompetenceAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action sur le changement de nombre d'éléments à afficher par page
  $hj('#displayedRows').change(function(){
    resolvePageCompetenceAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action pour afficher/cacher le formulaire de filtre
  $hj('i.fa-filter').unbind().click(function(){
    $hj('#filters input').val('');
    $hj('#filters').toggle();
  });
}
function addPageMissionAjaxActions() {
  // On ajoute une Action sur les actions Ajax
  $hj('#page-missions .ajaxAction').unbind().click(function(){
    resolvePageMissionAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action sur le changement de nombre d'éléments à afficher par page
  $hj('#displayedRows').change(function(){
    resolvePageMissionAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action pour afficher/cacher le formulaire de filtre
  $hj('i.fa-filter').unbind().click(function(){
    $hj('#filters input').val('');
    $hj('#filters').toggle();
  });
}
function addPageSurvivantAjaxActions() {
  // On ajoute une Action sur les actions Ajax
  $hj('#page-survivants .ajaxAction').unbind().click(function(){
    resolvePageSurvivantAjaxActions($hj(this));
    return false;
  });
  // On ajoute une Action sur le changement de nombre d'éléments à afficher par page
  $hj('#displayedRows').change(function(){
    resolvePageSurvivantAjaxActions($hj(this));
    return false;
  });
  // On ajoute un action sur les cases à cocher des Profils.
  $hj('.publicSurvivorRow .changeProfile').unbind().click(function(){
      addPageSurvivantLocalActions($hj(this));
      return false;
  });
  // On ajoute une Action pour afficher/cacher le formulaire de filtre
  $hj('i.fa-filter').unbind().click(function(){
    $hj('#filters input').val('');
    $hj('#filters').toggle();
  });
}
function addPageSurvivantLocalActions(clicked) {
  var type = clicked.data('type');
  clicked.parent().parent().parent().parent().parent().removeClass('survivant zombivant ultimate ultimatez').addClass(type);
  clicked.parent().siblings().removeClass('active');
  clicked.parent().addClass('active');
}
function addPageCardEquipmentAjaxActions() {
  $hj('#filters select').unbind().change(function(){
    var set = $hj('#filter-expansionId').val();
    var cType = $hj('#filter-typeId').val();
    console.log(set);
    console.log(cType);
    $hj('#card-container .card').each(function(){
      if ((set=='' || set!=''&&$hj(this).hasClass(set)) && (cType==undefined || cType=='' || cType!=''&&$hj(this).hasClass(cType))) {
        $hj(this).css('display', 'inline-block');
      } else {
        $hj(this).css('display', 'none');
      }
    });
  });
  $hj('button[data-ajaxaction="reset"]').unbind().click(function(){
    $hj('#filter-expansionId').val('');
    $hj('#filter-typeId').val('');
    $hj('#card-container .card').each(function(){
      $hj(this).css('display', 'inline-block');
    });
    return false;
  });
}

function addPageToolsAjaxActions() {
  addSelectionSurvivantActions();
  addThrowDiceActions();
}
function addThrowDiceActions() {
  $hj('#proceedThrowDice').unbind().click(function() {
    var params = '';
    params  = 'nbDice='+$hj('#diceNumber').val();
    params += '&seuil='+$hj('#accuracy').val();
    params += '&modif='+$hj('#plusAuDe').val();
    params += '&surunsix='+$hj('#surUnSix').val();
    params += '&dual=0';
    params += '&barbauto=0';
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getThrowDice', 'params': params};
    resolveCallAjax(data, 'page-piste-de-des');
  });
}
function addSelectionSurvivantActions() {
  // Actions sur les boutons de sélections du nombre de survivants.
  $hj('#nbSurvSel button').unbind().click(function(){
    $hj(this).siblings().removeClass('active');
    $hj(this).addClass('active');
    return false;
  });
  // Actions sur les regroupements d'extensions
  $hj('#page-tools .btn-expansion-group').unbind().click(function(){
    $hj(this).nextUntil('.btn-expansion-group').toggleClass('hidden');
    $hj(this).find('i').toggleClass('fa-chevron-circle-down fa-chevron-circle-right');
    return false;
  });
  // Actions sur les boutons d'Extensions pour afficher ou non les Survivants associés.
  $hj('#page-tools .btn-expansion').unbind().click(function(){
    // S'il a la classe fa-square, on doit tous les afficher et les sélectionner.
    var expansionId= $hj(this).data('expansion-id');
    if ($hj(this).find('i').hasClass('fa-check-square')) {
      $hj(this).find('i').addClass('fa-square').removeClass('fa-check-square');
      $hj('#listing button[data-expansion-id="'+expansionId+'"]').each(function(){
        $hj(this).addClass('hidden');
        $hj(this).find('i').addClass('fa-square').removeClass('fa-check-square');
      });
    } else {
      $hj(this).find('i').removeClass('fa-square').addClass('fa-check-square');
      $hj('#listing button[data-expansion-id="'+expansionId+'"]').each(function(){
        $hj(this).removeClass('hidden');
        $hj(this).find('i').removeClass('fa-square').addClass('fa-check-square');
      });
    }
    $hj('#nbSelected').html($hj('#listing .fa-check-square').length);
    var nb = 0;
    $hj('#listing button.btn-survivor').each(function(){ if ($hj(this).is(':visible')) nb++;});
    $hj('#nbDisplayed').html(nb);
    return false;
  });
  // Actions sur la case à cocher du bouton d'un Survivant.
  $hj('#page-tools .btn-survivor').unbind().click(function(){
    // On inverse le statut de la case à cocher
    $hj(this).find('i').toggleClass('fa-square fa-check-square');
    // On récupère l'extension Parent
    var expansionId = $hj(this).data('expansion-id');
    var parentNode = $hj('#page-tools .btn-expansion[data-expansion-id="'+expansionId+'"]');
    // On va parcourir les enfants du Parent pour dénombrer les statuts.
    var children = $hj('#listing button[data-expansion-id="'+expansionId+'"]');
    var childNb = children.length;
    var checkedNb = 0;
    children.each(function(){
      if ($hj(this).find('i').hasClass('fa-check-square') ) { checkedNb++; }
    });
    if (checkedNb==0 ) {
      // Si aucun n'est coché
      parentNode.find('i').removeClass('fa-check-square fa-minus-square').addClass('fa-square');
    } else if (checkedNb==childNb ) {
      // S'ils sont tous cochés
      parentNode.find('i').removeClass('fa-square fa-minus-square').addClass('fa-check-square');
    } else {
      // Si une partie est cochée.
      parentNode.find('i').removeClass('fa-check-square fa-square').addClass('fa-minus-square');
    }
    $hj('#nbSelected').html($hj('#listing .fa-check-square').length);
  });
  // Si on clic sur le bouton Générer.
  $hj('#proceedBuildTeam').unbind().click(function(){
    // On récupère la liste des Survivants sélectionnés.
    var selection = '';
    var nbSurv = 0;
    $hj('#page-tools .btn-survivor').each(function(){
      if ($hj(this).find('i').hasClass('fa-check-square') ) {
        selection += (selection==''?'':',')+$hj(this).data('survivor-id');
        nbSurv++;
      }
    });
    // Et le nombre de Survivants souhaités
    var nbSurvSel = $hj('#nbSurvSel button.active').data('nb');
    // Si le nombre sélectionné est inférieur à celui souhaité, même pas la peine de chercher...
    if (nbSurv<nbSurvSel) {
      alert('Impossible');
    } else {
      var data = {'action': 'dealWithAjax', 'ajaxAction': 'getRandomTeam', 'nbSurvSel': nbSurvSel, 'value': selection};
      resolveCallAjax(data, 'page-selection-survivants');
    }
  });
  $hj('#loadOwnTeam').unbind().click(function(){
    // On supprime toute sélection précédente
    $hj('#listing button.btn-survivor').addClass('hidden');
    $hj('#listing button.btn-survivor i').removeClass('fa-check-square').addClass('fa-square');
    // On récupère la sélection soumise
    var teamLoader = $hj('#teamLoader').val();
    var arr = teamLoader.split(',');
    var nb = arr.length;
    // On parcour la liste pour cocher les trucs qui vont bien
    for (var i=0; i<nb; i++) {
      var subArr = arr[i].split('-');
      if (subArr.length==1) {
        // un Survivant spécifique
        $hj('button[data-survivor-id="'+subArr[0]+'"]').removeClass('hidden').click();
      } else {
        for (var j=subArr[0]; j<=subArr[1]; j++) {
          // Un intervalle de Survivants
          $hj('button[data-survivor-id="'+j+'"]').removeClass('hidden').click();
        }
      }
    }
    var nb = 0;
    $hj('#listing button.btn-survivor').each(function(){ if ($hj(this).is(':visible')) nb++;});
    $hj('#nbDisplayed').html(nb);
  });
  $hj('#saveOwnTeam').unbind().click(function(e){
    var strSave = '';
    $hj('#listing i.fa-check-square').each(function(){
      strSave += $hj(this).parent().attr('data-survivor-id')+',';
    });

    var textArea = document.createElement("textarea");
    textArea.value = strSave.substring(0,strSave.length-1);

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      var successful = document.execCommand('copy');
      var msg = successful ? 'successful' : 'unsuccessful';
      console.log('Fallback: Copying text command was ' + msg);
    } catch (err) {
      console.error('Fallback: Oops, unable to copy', err);
    }
    document.body.removeChild(textArea);
  });
}

function resolvePageExpansionAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  var callAjax = true;
  // On initialise les données de tri et de filtres.
  var colsort = 'name';
  var colorder = 'asc';
  var paged = 1;
  var nbPerPages = $hj('#displayedRows').val();
  var filters = 'name='+$hj('#filter-name').val();

  switch (ajaxaction) {
    // On change le nombre d'éléments affichés
    case 'display' :
    case 'filter' :
    break;
    // On change la page affichée
    case 'paged' :
      paged = clicked.data('paged');
    break;
    default :
      callAjax = false;
    break;
  }
  if (callAjax) {
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getExpansions', 'colsort': colsort, 'colorder': colorder, 'nbperpage': nbPerPages, 'paged': paged, 'filters': filters};
    resolveCallAjax(data, 'page-extensions');
  }
}
function resolvePageCompetenceAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  var callAjax = true;
  // On initialise les données de tri et de filtres.
  var colsort = 'name';
  var colorder = 'asc';
  var paged = 1;
  var nbPerPages = $hj('#displayedRows').val();
  var filters = '';
  if ($hj('#filter-description').val()!=undefined) {
    filters = 'description='+$hj('#filter-description').val();
  }

  switch (ajaxaction) {
    // On change le nombre d'éléments affichés
    case 'display' :
    case 'filter' :
    break;
    // On change la page affichée
    case 'paged' :
      paged = clicked.data('paged');
    break;
    default :
      callAjax = false;
    break;
  }
  if (callAjax) {
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSkills', 'colsort': colsort, 'colorder': colorder, 'nbperpage': nbPerPages, 'paged': paged, 'filters': filters};
    resolveCallAjax(data, 'page-competences');
  }
}
function resolvePageMissionAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  var callAjax = true;
  // On initialise les données de tri et de filtres.
  var colsort = 'title';
  var colorder = 'asc';
  var paged = 1;
  var nbPerPages = $hj('#displayedRows').val();
  var filters = 'title='+$hj('#filter-title').val();
  filters += '&levelId='+$hj('#filter-levelId').val();
  filters += '&playerId='+$hj('#filter-playerId').val();
  filters += '&durationId='+$hj('#filter-durationId').val();
  filters += '&origineId='+$hj('#filter-origineId').val();
  if ($hj('#filter-expansionId').val()!=null) {
    filters += '&expansionId='+$hj('#filter-expansionId').val();
  }

  switch (ajaxaction) {
    // On change le nombre d'éléments affichés
    case 'display' :
    case 'filter' :
    break;
    // On change la page affichée
    case 'paged' :
      paged = clicked.data('paged');
    break;
    default :
      callAjax = false;
    break;
  }
  if (callAjax) {
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getMissions', 'colsort': colsort, 'colorder': colorder, 'nbperpage': nbPerPages, 'paged': paged, 'filters': filters};
    resolveCallAjax(data, 'page-missions');
  }
}
function resolvePageSurvivantAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  var callAjax = true;
  // On initialise les données de tri et de filtres.
  var colsort = 'name';
  var colorder = 'asc';
  var paged = 1;
  var nbPerPages = $hj('#displayedRows').val();
  var filters = 'name='+$hj('#filter-name').val();
  if ($hj('#filter-expansionId').val()!=null) {
    filters += '&expansionId='+$hj('#filter-expansionId').val();
  }
  if ($hj('#filter-blue-skill').val()!=null) {
    filters += '&blue-skillId='+$hj('#filter-blue-skill').val();
  }
  if ($hj('#filter-yellow-skill').val()!=null) {
    filters += '&yellow-skillId='+$hj('#filter-yellow-skill').val();
  }
  if ($hj('#filter-orange-skill').val()!=null) {
    filters += '&orange-skillId='+$hj('#filter-orange-skill').val();
  }
  if ($hj('#filter-red-skill').val()!=null) {
    filters += '&red-skillId='+$hj('#filter-red-skill').val();
  }

  switch (ajaxaction) {
    // On change le nombre d'éléments affichés
    case 'display' :
    case 'filter' :
    break;
    // On change la page affichée
    case 'paged' :
      paged = clicked.data('paged');
    break;
    default :
      callAjax = false;
    break;
  }
  if (callAjax) {
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSurvivants', 'colsort': colsort, 'colorder': colorder, 'nbperpage': nbPerPages, 'paged': paged, 'filters': filters};
    console.log(data);
    resolveCallAjax(data, 'page-survivants');
  }
}

function resolveCallAjax(data, idPage) {
  $hj('.overlay').addClass('loading');
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        var obj = JSON.parse(response);
        if (obj[idPage] != '' ) {
          $hj('#'+idPage).replaceWith(obj[idPage]);
          switch (idPage) {
            case 'page-competences' : addPageCompetenceAjaxActions(); break;
            case 'page-missions'    : addPageMissionAjaxActions(); break;
            case 'page-selection-survivants' :
            case 'page-survivants'  : addPageSurvivantAjaxActions(); break;
            case 'page-extensions'  : addPageExpansionAjaxActions(); break;
            default: break;
          }
        }
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  ).done(
    function() {
      $hj('.overlay').removeClass('loading');
    }
  );
}
