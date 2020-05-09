  /***************
   *** More News
   ***************

  if ($hj('#page-missions').length!=0 ) {
    $hj('#page-missions .ajaxAction').unbind().click(function(){
      addPageMissionAjaxActions($hj(this));
      return false;
    });
  }


  if ($hj('#filters').length!=0 ) {
    $hj('#filters select').unbind().change(function(){
      var set = $hj(this).val();
      $hj('#card-container .card').each(function(){
        if (set == '' || $hj(this).hasClass(set) ) {
          $hj(this).css('display', 'inline-block');
        } else {
          $hj(this).css('display', 'none');
        }
      })
    });
    $hj('#idReset').unbind().click(function(){
      $hj('#filters select').val('');
      $hj('#card-container .card').each(function(){
        $hj(this).css('display', 'inline-block');
      });
      return false;
    });
  }

  if ($hj('#page-live-spawn').length!=0 ) {
    addPageLiveSpawnActions();
    return false;
  }
  if ($hj('#page-live-equipment').length!=0 ) {
    addPageLiveEquipmentActions();
    return false;
  }

  if ($hj('#page-online').length!=0 ) {
    $hj('#startGame').unbind().click(function(){ $hj('form')[0].submit(); });

    // Sélection de la Mission
    $hj('.btn.btn-mission').unbind().click(function(){
      var missionId = $hj(this).data('mission-id');
      $hj(this).find('input').prop('checked', true);
      $hj(this).siblings().removeClass('active');
      $hj(this).toggleClass('active');
      $hj(this).siblings().find('svg').removeClass('fa-check-square').addClass('fa-square');
      $hj(this).find('svg').removeClass('fa-square').addClass('fa-check-square');
    });

    // Sélection des Survivants
    $hj('.btn.btn-survivor').unbind().click(function(){
      var survivorId = $hj(this).data('survivor-id');
      var isChecked = $hj(this).find('input').prop('checked');
      $hj(this).find('input').prop('checked', !isChecked);
      $hj(this).toggleClass('active');
      $hj(this).find('svg').toggleClass('fa-square fa-check-square');
    });

  }
  if ($hj('#canvas-background').length !=0 ) {
    $hj('canvas').drawImage({
      source: srcImg,
      x: xStart, y: yStart
    });
  }

  $hj('.menuBurger').click(function(){
    $hj(this).toggleClass('open');
  });













  /********
   * Filtres sur l'interface Survivors
   ********
  $hj('.survivorFilters a').unbind().click(function() {
    if (!$hj(this).hasClass('selected') ) {
      var exp = $hj(this).data('exp');
      var nb = $hj('article[data-exp="'+exp+'"]').length;
      if (nb==0 ) { getSurvivorsByExpansionCode(exp); }
    }
    $hj(this).toggleClass('selected');
    filterSurvivors();
  });

  $hj('.survivorFilters select').unbind().change(function() {
    filterSurvivors();
  });

  $hj('.survivorTool .selectSkill a').unbind().click(function() {
    var exp = $hj(this).data('exp');
    var txt = $hj(this).html();
    var strLi  = '<li class=\\"active\\"><article><input type=\\"checkbox\\" checked=\\"checked\\" name=\\"cb-sk-'+exp+'\\" id=\\"cb-sk-'+exp+'\\" class=\\"hidden\\">';
    strLi += '<i class=\\"glyphicon glyphicon-check pull-right\\"></i><span>'+txt+'</span></article></li>';
    var json = '{"selectionSkills":"'+strLi+'"}';
    var obj = JSON.parse(json);
    reloadComponents(obj, 'append');
    $hj('#selectionSkills i').unbind().click(function() {
      $hj(this).parent().parent().remove();
    })
  });

  $hj('.survivorTool .selectSurvivor a').unbind().click(function() {
    var exp = $hj(this).data('exp');
    if (!$hj(this).hasClass('selected') ) {
      var nb = $hj('article[data-exp="'+exp+'"]').length;
      if (nb!=0 ) {
        $hj('article[data-exp="'+exp+'"]').each(function(e) {
          $hj(this).parent().remove();
        });
      }
      getSurvivorsByExpansionCode(exp, 'cartouche');
    } else {
      $hj('article[data-exp="'+exp+'"]').each(function() {
        $hj(this).parent().remove();
        /*
        $hj(this).parent().removeClass('active');
        var inpSib = $hj(this).find('input');
        inpSib.prop("checked", !inpSib.prop("checked"))
        *
      })
    }
    $hj(this).toggleClass('selected');
  });

  $hj('#generateTeam').click(function(e) {
    e.preventDefault();
    var obj;
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getTeam', 'filters': $hj('#filters').serialize()};
    $hj.post(
      ajaxurl,
      data,
      function(response) {
        try {
          obj = JSON.parse(response);
        } catch (e) {
          console.log("error: "+e);
          console.log(response);
        }
        reloadComponents(obj, 'replace');
      }
    );
    return false;
  });

  $hj('#exportSelectionTeam').click(function(e) {
    var data = '';
    $hj('#selectionSurvivors input').each(function(e) {
      if ($hj(this).is(':checked') ) {
        if (data !='' ) {
        data += ';';
        }
        data += $hj(this).attr('name').substr(8);
      }
    });
    var filename = 'selectionTeam.txt';
    var file = new Blob([data], {type: 'text'});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var a = document.createElement("a"),
                url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 0);
    }
    return false;
  });

  $hj('#importSelectionTeam').change(function(e) {
    var obj;
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSurvivorsForImport', 'value': $hj(this).val()};
    $hj.post(
      ajaxurl,
      data,
      function(response) {
        try {
          obj = JSON.parse(response);
        } catch (e) {
          console.log("error: "+e);
          console.log(response);
        }
        reloadComponents(obj, 'replace');
        $hj('#selectionSurvivors i').unbind().click(function() {
          $hj(this).parent().parent().toggleClass('active');
          var inpSib = $hj(this).siblings('input');
          inpSib.prop("checked", !inpSib.prop("checked"))
        });
      }
    );
  });

  /********
   * Filtres sur l'interface Equipements
   ********
  $hj('.equipmentFilters select').unbind().change(function() {
    if ($hj(this).hasClass('zoom') ) {
      $hj('.batchArticles.equipments').removeClass().addClass('batchArticles equipments '+$hj(this).val());
    } else {
      filterCards();
    }
  });
  $hj('.invasionFilters select').unbind().change(function() {
    if ($hj(this).hasClass('zoom') ) {
      $hj('.batchArticles.invasions').removeClass().addClass('batchArticles invasions '+$hj(this).val());
    } else {
      filterCards();
    }
  });

  /********
   * Filtres sur l'interface Skills
   ********
  $hj('input.chosen-search-input-skill').keyup(function() {
    var filter = $hj('input.chosen-search-input-skill').val().toLowerCase();
    $hj('.batchArticles.skills article').each(function() {
      var value = $hj(this).data('title');
      if (value.toLowerCase().indexOf(filter)!=-1 ) {
        $hj(this).show();
      } else {
        $hj(this).hide();
      }
    });
  });

  /********
   * Filtres sur l'interface Missions
   ********
  $hj('.missionFilters a').unbind().click(function() {
    var isSelected = $hj(this).hasClass('selected');
    if (isSelected ) {
      if ($hj(this).parent().parent().find('a.selected').length == 1 ) {
        $hj(this).parent().siblings('li').find('a[data-filter="all"]').addClass('selected');
      }
    } else {
      $hj(this).parent().siblings('li').find('a[data-filter="all"]').removeClass('selected');
    }
    $hj(this).toggleClass('selected');
    filterMissions();
  });

  $hj('div.chosen-container.chosen-container-single a')
    .unbind()
    .click(
      function(){
        fillOptionsList();
        $hj(this).parent().toggleClass('chosen-with-drop chosen-container-active');
        $hj('ul.chosen-results li.result-selected').addClass('highlighted');
        $hj('input.chosen-search-input').val('').focus();
      }
    );
  addActionsOnList();
  $hj('input.chosen-search-input').keyup(function() { fillOptionsList(); });

  /***************
   *** Mission Online
   ***************
  $hj('#genKey').click(function(e){
    e.preventDefault();
    var n = 16;
    var str = 'azertyupqsdfghjkmwxcvbnAZERTYUPQSDFGHJKMWXCVBN23456789';
    var max = str.length;
    var password = '';
    for (var i=1; i<=n; i++ ) {
      var start = Math.floor(Math.random()*max);
      password += str.substring(start, start+1);
    }
    $hj('#deckKey').val(password);
  });

  $hj('.slideinfo').click(function(){
    if ($hj(this).hasClass('show') ) {
      $hj('.slideinfo').removeClass('show');
    } else {
      $hj('.slideinfo').removeClass('show');
      $hj(this).addClass('show');
    }
  });

  $hj('#liveMissionSelection li input').click(function(){
    var id = $hj(this).val();
    $hj('.hideAllMaps > li').hide();
    $hj('.hideAllMaps > li[data-missionId="'+id+'"]').show();
  });

  $hj('#filter-expansion').change(function(){
    var filtre = $hj(this).val();
    $hj('#liveSurvivorsSelection li').each(function(){
      if ($hj(this).hasClass(filtre) || filtre=='' ) {
        $hj(this).show();
      } else {
        $hj(this).hide();
      }
    });
  });

  $hj('#liveSurvivorsSelection input').change(function(){
    $hj('#nbSurvivorsSelected').html($hj('#liveSurvivorsSelection input:checked').length);
  });

  $hj('#randomSelect').unbind().click(function(e){
    e.preventDefault();
    var nb = 6-$hj('#liveSurvivorsSelection input:checked').length;
    var nbEligible = $hj('#liveSurvivorsSelection input:visible:not(:checked)').length;
    if (nbEligible <= nb ) {
      $hj('#liveSurvivorsSelection input:visible:not(:checked)').each(function(){
        $hj(this).prop('checked', true);
      });
    } else {
      for (var i=nb; i>0; i-- ) {
        var rk = Math.floor(Math.random() * nbEligible);
        $hj('#liveSurvivorsSelection input:visible:not(:checked)').eq(rk).prop('checked', true);
        nbEligible--;
      }
    }
    $hj('#nbSurvivorsSelected').html($hj('#liveSurvivorsSelection input:checked').length);
  });

  $hj('.checkall').click(function(){
    $hj(this).parent().siblings().find('input').prop('checked', $hj(this).prop('checked'));
  });

  $hj('#spawnSetupSelection input').click(function(){
    var isChecked = $hj(this).is(':checked');
    var span = $hj(this).data('href');
    var node = $hj('#invasionSpanSelection');
    if (isChecked && node.val().indexOf(span)==-1 ) {
      node.val(node.val()+span);
    } else if (!isChecked && node.val().indexOf(span)!=-1 ) {
      node.val(node.val().replace(span, ''));
    }
  });

  $hj(function() {
    $hj("#sortable1, #sortable2" ).sortable({
      connectWith: ".connectedSortable"
    }).disableSelection();
  });




  /***
  ***
  $hj('.toolEquipments ul li i').unbind().click(function() {
    $hj(this).toggleClass('glyphicon-triangle-bottom').toggleClass('glyphicon-triangle-top');
  });
  $hj('#piocheXCards').unbind().click(function() {
    $hj(this).parent().toggleClass('open');
  });


  * */


/*
function addActionsOnList() {
  $hj('ul.chosen-results li.active-result')
    .unbind()
    .hover(
      function() {
        $hj(this).siblings().removeClass('highlighted');
        $hj(this).addClass('highlighted');
      },
      function() {
        $hj(this).removeClass('highlighted');
      }
    )
    .click(
      function() {
        $hj(this).siblings().removeClass('result-selected');
        $hj(this).addClass('result-selected');
        var rank = $hj(this).data('option-array-index');
        var libelle = $hj('#searchSkill option:nth-child('+rank+')').text();
        var value = $hj('#searchSkill option:nth-child('+rank+')').val();
        getSurvivorsBySkillId(value);
        $hj('a.chosen-single span').html(libelle);
        $hj('div.chosen-container.chosen-container-single a').parent().removeClass('chosen-with-drop chosen-container-active');
      }
    );
}
function fillOptionsList() {
  var filter = $hj('input.chosen-search-input').val().toLowerCase();
  var str = '';
  var cpt = 1;
  var nb = 0;
  $hj('select.chosen-select option').each(function() {
    var value = $hj(this).html();
    if (value.toLowerCase().indexOf(filter)!=-1 ) {
      var libelle = $hj('#searchSkill option:nth-child('+cpt+')').text().toLowerCase();
      if (filter!='' ) {
        libelle = libelle.replace(filter, '<em>'+filter+'</em>');
      }
      str += '<li class="active-result'+(nb==0 ? ' highlighted' : '')+'" style="" data-option-array-index="'+cpt+'">'+libelle+'</li>';
      nb++;
    }
    cpt++;
  }).promise()
    .done(function() {
      $hj('ul.chosen-results').html(str);
      addActionsOnList();
    });
}


/********
 * Filtres sur les articles Missions
 ********
function filterMissions() {
  $hj('.batchArticles article.mission').each(function(){
    var nodeArticle = $hj(this);
    var showArticle = true;
    var cpt = 0;
    $hj('.missionFilters ul').each(function(){
      var tmpShow = false;
      $hj(this).find('a.selected').each(function(){
        var filter = $hj(this).data('filter');
        if (filter == 'all' ){
          tmpShow = true;
          cpt++;
        } else {
          if (nodeArticle.hasClass(filter) ) {
            tmpShow = true;
          }
        }
      });
      showArticle = (showArticle && tmpShow);
    });
    if (!showArticle && cpt==4 ) { showArticle = true; }
    if (showArticle && !nodeArticle.is(':visible')
       || !showArticle && nodeArticle.is(':visible') ) {
       nodeArticle.animate({width: 'toggle'}, 2500);
    }
  });
}

/********
 * Filtres sur les articles Equipements
 ********
function filterCards() {
  $hj('.batchArticles article.equipment').each(function(){
    var nodeArticle = $hj(this);
    var showArticle = true;
    $hj('.equipmentFilters select').each(function(){
      var select = $hj(this).val();
      if (select!='' && !$hj(this).hasClass('zoom') ) {
        if (!nodeArticle.hasClass(select) ) {
          showArticle = false;
        }
      }
    });
    if (showArticle && !nodeArticle.is(':visible')
       || !showArticle && nodeArticle.is(':visible') ) {
       nodeArticle.animate({width: 'toggle'}, 2500);
    }
  });
  $hj('.batchArticles article.invasion').each(function(){
    var nodeArticle = $hj(this);
    var showArticle = true;
    $hj('.invasionFilters select').each(function(){
      var select = $hj(this).val();
      if (select!='' && !$hj(this).hasClass('zoom') ) {
        if (!nodeArticle.hasClass(select) ) {
          showArticle = false;
        }
      }
    });
    if (showArticle && !nodeArticle.is(':visible')
       || !showArticle && nodeArticle.is(':visible') ) {
       nodeArticle.animate({width: 'toggle'}, 2500);
    }
  });
}

/********
 * Filtres sur les articles Survivants
 ********
function filterSurvivors() {
  var typeSelected = $hj('#survivorType').val();
  $hj('section.batchArticles')
    .removeClass()
    .addClass('batchArticles show-'+typeSelected);

  $hj('.batchArticles article.survivor').each(function() {
    var nodeSurvivor = $hj(this);
    var showArticle = true;
    var expValue = $hj(this).data('exp');

    if (!$hj('.survivorFilters a[data-exp="'+expValue+'"]').hasClass('selected') ) {
      showArticle = false;
    }

    var blueSelection = $hj('#searchBlue').val();
    if (blueSelection!=0 && showArticle ) {
      showArticle = false;
      nodeSurvivor.find('li.compBlue').each(function() {
        if ($hj(this).data('idskill') == blueSelection ) {
          showArticle = true;
        }
      });
    }

    var yellowSelection = $hj('#searchYellow').val();
    if (yellowSelection!=0 && showArticle ) {
      showArticle = false;
      nodeSurvivor.find('li.compYellow').each(function() {
        if ($hj(this).data('idskill') == yellowSelection ) {
          showArticle = true;
        }
      });
    }

    var orangeSelection = $hj('#searchOrange').val();
    if (orangeSelection!=0 && showArticle ) {
      showArticle = false;
      nodeSurvivor.find('li.compOrange').each(function() {
        if ($hj(this).data('idskill') == orangeSelection ) {
          showArticle = true;
        }
      });
    }

    var redSelection = $hj('#searchRed').val();
    if (redSelection!=0 && showArticle ) {
      showArticle = false;
      nodeSurvivor.find('li.compRed').each(function() {
        if ($hj(this).data('idskill') == redSelection ) {
          showArticle = true;
        }
      });
    }

    var allSelection = $hj('#searchAll').val();
    if (allSelection!=0 && showArticle ) {
      showArticle = false;
      nodeSurvivor.find('li').each(function() {
        if ($hj(this).data('idskill') == allSelection ) {
          showArticle = true;
        }
      });
    }

    if (typeSelected=='zombivor' && !$hj(this).hasClass('zombivor')
       || (typeSelected=='usurvivor' || typeSelected=='uzombivor') && !$hj(this).hasClass('ultimate') ) {
      showArticle = false;
    }

    if (showArticle && !nodeSurvivor.is(':visible')
       || !showArticle && nodeSurvivor.is(':visible') ) {
       nodeSurvivor.animate({width: 'toggle'}, 2500);
    }
  });
}
function getSurvivorsBySkillId(skillId) {
  var obj;
  var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSurvivorsBySkillId', 'value': skillId};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
      reloadComponents(obj, 'replace');
    }
  );
}
function getSurvivorsByExpansionCode(exp, type='') {
  var obj;
  var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSurvivorsByExpansionCode', 'value': exp, 'type': type};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
      reloadComponents(obj, 'append');
      if (type=='' ) {
        filterSurvivors();
        sortSurvivors();
      } else if (type=='cartouche' ) {
        $hj('#selectionSurvivors i').unbind().click(function() {
          $hj(this).parent().parent().toggleClass('active');
          var inpSib = $hj(this).siblings('input');
          inpSib.prop("checked", !inpSib.prop("checked"))
        });
      }
    }
  );
}

/********
 * Ajax Utilities
 ********
function reloadComponents(obj, type) {
  for (var anchor in obj ) {
    if ($hj('#'+anchor).length==1 ) {
      switch (anchor ) {
        case 'descSkill'            :
        case 'homeSectionArticles'  :
        case 'moreSurvivors'        :
        case 'selectionSkills'       :
        case 'selectionSurvivors'   :
          switch (type ) {
            case 'append'    : $hj('#'+anchor).append(obj[anchor]); break;
            case 'prepend'  : $hj('#'+anchor).prepend(obj[anchor]); break;
            case 'replace'  : $hj('#'+anchor).html(obj[anchor]); break;
          }
          break;
      }
    }
  }
}
function sortSurvivors() {
  $hj('#moreSurvivors article')
    .sort(sortSurvivorsArticles)
    .appendTo('#moreSurvivors');
}
function sortSurvivorsArticles(a, b){
  return ($hj(b).data('name')) < ($hj(a).data('name')) ? 1 : -1;
}
























/********
 * Ajax Actions - Missions Page
 ********
var displayValue = $hj('#displayedRows').val();
var colsort = $hj('section th[data-colorder="_asc"]').data('colsort');
if (colsort=='' ) { colsort = $hj('section th[data-colorder="_asc"]').data('colsort'); }
var colorder = 'asc';
var paged = $hj('.pagination.justify-content-end .page-item.disabled a').data('paged');
function addPageMissionAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  var callAjax = true;
  switch (ajaxaction ) {
    // On change le critère de tri
    case 'sort' :
      colsort = clicked.data('colsort');
      if (!clicked.hasClass('sorting') ) {
        var actualorder = clicked.data('colorder');
        if (actualorder=='_asc' ) { colorder = 'desc'; }
        else { colorder = 'asc'; }
      }
    break;
    // On change le nombre d'éléments affichés
    case 'display' :
      displayValue = clicked.val();
      paged = 1;
    break;
    // On change la page affichée
    case 'paged' :
      paged = clicked.data('paged');
    break;
    case 'filter' :
      if ($hj('#rowFilterMission').hasClass('hidden') ) {
        callAjax = false;
        $hj('#rowFilterMission').removeClass('hidden');
      }
    break;
    default : callAjax = false; break;
  }
  if (callAjax ) {
    var obj;
    var filters = $hj('select.filters').serialize();
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'getMissions', 'colsort': colsort, 'colorder': colorder, 'nbperpage': displayValue, 'paged': paged, 'filters': filters};
    $hj.post(
      ajaxurl,
      data,
      function(response) {
        try {
          obj = JSON.parse(response);
          if (obj['page-missions'] != '' ) {
            $hj('#page-missions').replaceWith(obj['page-missions']);
            $hj('#page-missions .ajaxAction').unbind().click(function(){
              addPageMissionAjaxActions($hj(this));
              return false;
            });
          }
        } catch (e) {
          console.log("error: "+e);
          console.log(response);
        }
      }
    );
  }
}
function addPageSurvivantAjaxActions(clicked) {
    var ajaxaction = clicked.data('ajaxaction');
    var callAjax = true;
    switch (ajaxaction ) {
      // On change le critère de tri
      case 'sort' :
        colsort = clicked.data('colsort');
        if (!clicked.hasClass('sorting') ) {
          var actualorder = clicked.data('colorder');
          if (actualorder=='_asc' ) { colorder = 'desc'; }
          else { colorder = 'asc'; }
        }
      break;
      // On change le nombre d'éléments affichés
      case 'display' :
        displayValue = clicked.val();
        paged = 1;
      break;
      // On change la page affichée
      case 'paged' :
        paged = clicked.data('paged');
      break;
      case 'filter' :
        var filter = clicked.data('filter');
        var top = clicked.offset().top;
        var left = clicked.offset().left;
        var widthCol = clicked.width();
        var widthPopup = $hj('#popover'+filter.charAt(0).toUpperCase()+filter.slice(1)).width();
        $hj('#popover'+filter.charAt(0).toUpperCase()+filter.slice(1))
          .toggleClass('show')
          .css('transform', 'translate3d('+(left+widthCol-widthPopup+34)+'px, '+(top-182)+'px, 0px)')
          .find('.arrow').css('left', (widthPopup-34)+'px');
        callAjax = !$hj('#popover'+filter.charAt(0).toUpperCase()+filter.slice(1)).hasClass('show');
      break;
      default : callAjax = false; break;
    }
    if (callAjax ) {
      var obj;
      var filters = $hj('input.filters').serialize();
      var data = {'action': 'dealWithAjax', 'ajaxAction': 'getSurvivants', 'colsort': colsort, 'colorder': colorder, 'nbperpage': displayValue, 'paged': paged, 'filters': filters};
      $hj.post(
        ajaxurl,
        data,
        function(response) {
          try {
            obj = JSON.parse(response);
            if (obj['page-survivants'] != '' ) {
              $hj('#page-survivants').replaceWith(obj['page-survivants']);
              $hj('#page-survivants .ajaxAction').unbind().click(function(){
                addPageSurvivantAjaxActions($hj(this));
              });
              $hj('#page-survivants .changeProfile').unbind().click(function(){
                addPageSurvivantLocalActions($hj(this));
                return false;
              });
            }
          } catch (e) {
            console.log("error: "+e);
            console.log(response);
          }
        }
      );
    }
  }

function doEquipmentDeckActions(data, type) {
  var obj;
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
        if (type == 'reload' ) {
          location.href = 'http://zombicide.jhugues.fr/page-live-pioche-equipment/';
        }
        for (var anchor in obj ) {
          if (type == 'insert' ) {
            $hj('#'+anchor).html(obj[anchor]);
          }
        }
        $hj('.discardEquipButton').unbind().click(function(){
          var data = {'action': 'dealWithAjax', 'ajaxAction': 'discardEquippedCard', 'keyAccess': $hj(this).data('keyaccess'), 'id': $hj(this).data('id')};
          doEquipmentDeckActions(data, 'insert');
        });
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
function doSpawnDeckActions(data, type) {
  var obj;
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
        if (type == 'reload' ) {
          location.href = 'http://zombicide.jhugues.fr/page-live-pioche-invasion/';
        }
        for (var anchor in obj ) {
          if (type == 'insert' ) {
            $hj('#'+anchor).html(obj[anchor]);
          }
        }
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
function addGenKeyActions() {
  $hj('#genKey').click(function(e){
    e.preventDefault();
    var n = 16;
    var str = 'azertyupqsdfghjkmwxcvbnAZERTYUPQSDFGHJKMWXCVBN23456789';
    var max = str.length;
    var password = '';
    for (var i=1; i<=n; i++ ) {
      var start = Math.floor(Math.random()*max);
      password += str.substring(start, start+1);
    }
    $hj('#keyAccess').val(password);
  });
}
function addPageLiveEquipmentActions() {
  addGenKeyActions();
  $hj('#equipmentSetupSelection .btn-expansion').click(function(){
    $hj(this).toggleClass('active');
    $hj(this).find('svg').toggleClass('fa-square fa-check-square');
    var expansionIds = '';
    $hj('.btn-expansion.active').each(function(){
      if (expansionIds!='') {
        expansionIds+=',';
      }
      expansionIds += $hj(this).data('expansion-id');
    });
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'pregenEquipmentCard', 'expansionIds': expansionIds};
    doEquipmentDeckActions(data, 'insert');
  });
  if ($hj('#btnDrawEquipmentCard').length!=0 ) {
    $hj('.withEquipmentAction').unbind().click(function(){
      var action = $hj(this).data('action');
      var keyAccess = $hj(this).data('keyaccess');
      var data = {'action': 'dealWithAjax', 'ajaxAction': 'EquipmentDeck' , 'ajaxChildAction': action, 'keyAccess': keyAccess};
      doEquipmentDeckActions(data, $hj(this).data('type'));
    });
  }
}
function addPageLiveSpawnActions() {
  addGenKeyActions();
  $hj('#spawnSetupSelection .btn-expansion span').click(function(){
    $hj(this).find('svg').toggleClass('fa-square fa-check-square');
    var isChecked = $hj(this).find('svg').hasClass('fa-check-square');
    var span = $hj(this).data('spawnspan');
    var node = $hj('#invasionSpanSelection');
    if (isChecked && node.val().indexOf(span)==-1 ) {
      node.val(node.val()+span);
    } else if (!isChecked && node.val().indexOf(span)!=-1 ) {
      node.val(node.val().replace(span, ''));
    }
  });

  if ($hj('#btnDrawSpawnCard').length!=0 ) {
    $hj('.withSpawnAction').unbind().click(function(){
      var action = $hj(this).data('action');
      var keyAccess = $hj(this).data('keyaccess');
      var data = {'action': 'dealWithAjax', 'ajaxAction': 'SpawnDeck' , 'ajaxChildAction': action, 'keyAccess': keyAccess};
      doSpawnDeckActions(data, $hj(this).data('type'));
    });
  }
}
function joinGame() {
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'joinGame', 'keyAccess': $hj('#keyAccess').val()};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        var obj = JSON.parse(response);
        console.log(obj);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
*/

