var $hj = jQuery;
var lowestRatio = 1;
$hj(document).ready(function(){
  // Quand on affiche la page pour la première fois, on doit fixer les différents cadres.
  // Pour le moment ces données sont fixes. Elles pourraient être fonction du profil dans un deuxième temps.
  setSectionSizes();

  $hj('#cgu').click(function(){
    $hj('#validCGU').toggleClass('disabled');
  });
  $hj('#validCGU').click(function(){
    if (!$hj(this).hasClass('disabled')) {
      $hj('#formLogin > div').toggle();
    }
  });

  $hj('.chip').each(function(){
    setChipSizes($hj(this));
  });

  setChipsAction();
  setChipMenuActions();

  ////////////////////////////////////////////////////////////
  // Action sur l'accordéon d'aide de la sidebar
  $hj('#page-mission-online-help dt').click(function(){
    $hj(this).siblings('dd').hide();
    $hj(this).next('dd').show();
  });
  $hj('#page-mission-online-help dt .fa-window-minimize').click(function(event){
    $hj(this).parent().next('dd').hide();
    event.stopPropagation();
  });
  ////////////////////////////////////////////////////////////
  // Action sur l'accordéon des Survivants de la sidebar
  addSidebarSurvivorKnownActions();
  $hj('#page-mission-online-sidebar-survivors-detail dt').click(function(){
    $hj(this).siblings('dd').hide();
    $hj(this).next('dd').show();
  });
  $hj('#page-mission-online-sidebar-survivors-detail dt .fa-window-minimize').click(function(event){
    $hj(this).parent().next('dd').hide();
    event.stopPropagation();
  });
  $hj('i.fa-files-o').click(function(){
    $hj('#dataToPaste').focus();
    $hj('#dataToPaste').select();
    document.execCommand('copy');
    $hj('#dataToPaste').blur();
  });
  ////////////////////////////////////////////////////////////
  if($hj('#startMission').length) {
    $hj('#startMission').click(function(){
      $hj('#formLogin').submit();
    });

    $hj('.btn.btn-expansion.btn-dark').click(function(){
      $hj('.btn.btn-expansion.btn-dark i.fa-dot-circle-o').removeClass('fa-dot-circle-o').addClass('fa-circle-o');
      $hj(this).find('i').addClass('fa-dot-circle-o');
      $hj(this).next('input').trigger('click');
    });
  }

  $hj('#page-mission-online').on('click', function(){
    hideMenus();
  });
  $hj('#page-mission-online-tokens').on('contextmenu', function(event){
    event.preventDefault();
    hideMenus();
    posX = event.clientX;
    posY = event.clientY;
    $hj(this).next().addClass('show-menu').css('left', posX).css('top', posY);
    posX = event.clientX / lowestRatio;
    posY = event.clientY / lowestRatio;
    return false;
  });

  var element = document.getElementById("online-chat-content");
  if (element!=undefined) {
    element.scrollIntoView(false);
  }

  $hj('.modal .close').on('click', function(){
    $hj('.modal').hide();
  });

  if ($hj('#online-chat-content').length!=0) {
    tchatRefreshTimer = setInterval(sendTchatMsg, 30000);
  }

  $hj('#initSpawnDeck').click(function(){
    var uniqid   = $hj('#page-mission-online').data('uniqid');
    var interval = $hj('#newSpawnDeck').val();
    var data   = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': uniqid, 'act': 'init', 'type': 'Spawn', 'interval': interval};
    updateLiveMissionXml(data);
  });

  $hj('#initItemDeck').click(function(){
    var uniqid   = $hj('#page-mission-online').data('uniqid');
    var data   = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': uniqid, 'act': 'init', 'type': 'Item'};
    updateLiveMissionXml(data);
  });
});

  $hj(window).resize(function(){
    setSectionSizes();
  });

var lstElements = '';
function updateLiveMissionXml(data) {
  $hj('body').addClass('waiting');
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        var obj = JSON.parse(response);
        if (obj['lstElements'] != '' ) {
          lstElements = obj['lstElements'];
          var nbElements = lstElements.length;
          for (var i=0; i<nbElements; i++) {
            var oneElement = lstElements[i];
            var id = oneElement[0];
            var element = oneElement[1];
            if ($hj('#'+id).length==0) {
              // On a des exception
              switch (id) {
                case 'detail-survivor-new' :
                  $hj('#page-mission-online-sidebar-survivors-detail').append(element);
                break;
                case 'tchat-new' :
                  $hj('#online-chat-content').append(element);
                break;
                default :
                // Ajoute Nouveau.
                  $hj('#page-mission-online-tokens').append(element);
                break;
              }
              setChipSizes($hj('#'+id));
            } else {
              // On a des exception
              switch (id) {
                case 'portrait-new' :
                  $hj(element).insertBefore('#portrait-new');
                  addSidebarSurvivorKnownActions();
                  return false;
                break;
                case 'modalBody' :
                  $hj('#modalBody').html(element);
                  $hj('#modalSpawn').show();
                  return false;
                break;
                default :
                // Mais sinon, on replace existant
                  $hj('#'+id).replaceWith(element);
                break;
              }
              setChipSizes($hj('#'+id));
            }
            setChipAction($hj('#'+id));
          }
          setChipMenuActions();
          addSidebarSurvivorKnownActions();
        }

      } catch (e) {
        //console.log("error: "+e);
        //console.log(response);
      }
    }
  ).done(
    function() {
      $hj('body').removeClass('waiting');
      $hj('body').css('cursor', '');
      console.log('updateLiveMissionXml Done');
    }
  );
}


function hideMenus() {
  $hj('.show-menu').removeClass('show-menu');
}
var posX = 0;
var posY = 0;
function setChipMenuActions() {
  $hj('menu .menu-item').unbind().on('click', function(){
    if (!$hj(this).hasClass('disabled')) {
      var uniqid = $hj('#page-mission-online').data('uniqid');
      var id     = $hj(this).attr('id');
      var act    = $hj(this).data('menu-action');
      var qte    = $hj(this).data('quantite');
      var type   = $hj(this).data('type');
      if (act=='' || act==undefined) {
        return false;
      }
      var data   = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': uniqid, 'id': id, 'act': act, 'quantite': qte, 'type': type, 'coordx': posX, 'coordy': posY};
      updateLiveMissionXml(data);

      hideMenus();
      if (act=='pick') {
        $hj('#'+id).remove();
        $hj('#portrait-'+id).remove();
        $hj('#detail-survivor-'+id).remove();
      }
    }
  });
}


function setChipAction(obj) {
  obj.unbind().on('contextmenu', function(event){
    event.preventDefault();
    hideMenus();
    posX = event.clientX;
    posY = event.clientY;
    $hj(this).next().addClass('show-menu').css('left', posX).css('top', posY);
    posX = event.clientX / lowestRatio;
    posY = event.clientY / lowestRatio;
    return false;
  });

  if ((obj.hasClass('zombie') || obj.hasClass('survivor') || obj.hasClass('noise')) && !obj.hasClass('non-draggable')) {
    obj.draggable({
      containment: "#page-mission-online-tokens",
      scroll: false,
      stop: function(){
        var top = $hj(this).position().top / lowestRatio;
        var left = $hj(this).position().left / lowestRatio;
        var data = {
          'action': 'dealWithAjax',
          'ajaxAction': 'updateLiveMission',
          'uniqid': $hj('#page-mission-online').data('uniqid'),
          'id': $hj(this).attr('id'),
          'act': 'move',
          'top': top,
          'left': left
        };
        updateLiveMissionXml(data);
      }
    });
  }

}
function setChipsAction() {
  ///////////////////////////////////////
  // Gestion des clicks sur une Porte
  // Gestion des clicks sur la Zone de Sortie
  // Gestion des clicks sur un Objectif
  // Gestion des clicks sur un Spawn
  ///////////////////////////////////////
  $hj('div.chip.token').each(function(){
    setChipAction($hj(this));
  });





  ///////////////////////////////////////
  // Gestion des clicks sur les Survivants inconnus
  ///////////////////////////////////////
  addSidebarSurvivorUnknownActions();

  ///////////////////////////////////////
  // Gestion des clicks sur la Réserve de Survivants
  ///////////////////////////////////////
  $hj('#page-mission-online-survivor-reserve div').unbind().click(function(event){
    console.log('Add Survivor Clicked');
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'act' : 'add',
      'type' : 'Survivor',
      'survivorId' : $hj(this).data('survivorid')
    };
    updateLiveMissionXml(data);
    $hj('#page-mission-online-survivor-reserve .close span').trigger('click');
  });

  ///////////////////////////////////////
  // Fermeture de la Réserve de Survivants
  ///////////////////////////////////////
  $hj('#page-mission-online-survivor-reserve .close span').click(function(){
    $hj('#page-mission-online-survivor-reserve').css('top', -2500);
  });

}

  var survivorRankClicked = 0;

function addSidebarSurvivorKnownActions() {
  $hj('#page-mission-online-sidebar-survivors img.known').unbind().click(function(event){
    $hj('#page-mission-online-sidebar-survivors-detail').css('top', '0');
    $hj('#page-mission-online-help a').removeClass('active');
  });
  $hj('#page-mission-online-help a').unbind().click(function(){
    $hj('#page-mission-online-obj-rules').css('top', '-105%');
    $hj('#page-mission-online-help-website').css('top', '-105%');
    $hj('#page-mission-online-sidebar-survivors-detail').css('top', '-105%');
    $hj('#page-mission-online-setup').css('top', '-105%');

    $hj(this).siblings().removeClass('active');
    $hj(this).addClass('active');
    if ($hj(this).data('target')!=undefined) {
      $hj($hj(this).data('target')).css('top', '0');
    }
  });
  $hj('#page-mission-online-sidebar-survivors-detail li').unbind().click(function(){
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'act' : ($hj(this).hasClass('disabled') ? 'activate' : 'unactivate'),
      'id' : $hj(this).attr('id'),
    };
    updateLiveMissionXml(data);
    $hj(this).toggleClass('disabled');
  });
}
function addSidebarSurvivorUnknownActions() {
  $hj('#page-mission-online-sidebar-survivors img.unknown').unbind().click(function(event){
    posX = event.clientX;
    posY = event.clientY;
    $hj('#page-mission-online-survivor-reserve').css('left', posX-400).css('top', posY);
  });
}

function setSectionSizes() {
  // Fonction de l'écran
  var availableHeight = $hj('#shell').height();
  var shellWidth = $hj('#shell').width();
  // Selon le style, en théorie 200px;
  var sidebarWidth = 300;//$hj('#page-mission-online-sidebar').width();
  // Les dimensions disponibles :
  var availableWidth = shellWidth - sidebarWidth - 5;

  // Les dimensions de la map :
  var mapWidth = 1500;
  var mapHeight = 1000;
  // On calcule les ratio :
  var ratioWidth = availableWidth / mapWidth;
  var ratioHeight = availableHeight / mapHeight;
  lowestRatio = Math.min(ratioWidth, ratioHeight);

  $hj('#page-mission-online-content').width(mapWidth*lowestRatio);
  $hj('#page-mission-online-content').height(availableHeight);
  $hj('#page-mission-online-map').height(mapHeight*lowestRatio);
  $hj('#page-mission-online-tokens').height(mapHeight*lowestRatio);

  sidebarWidth = Math.max(300, shellWidth - $hj('#page-mission-online-content').width());
  $hj('#page-mission-online-sidebar').width(sidebarWidth);
}
function setChipSizes(obj) {
  if (!obj.hasClass('chip')) {
    return false;
  }
  var type = obj.data('type');
  if (type=='Objective' || type=='Spawn' || type=='Exit' || type=='Door' || type=='Survivor' || type=='Zombie' || type=='Noise') {
    posX   = lowestRatio * obj.data('coordx');
    posY   = lowestRatio * obj.data('coordy');
    var width  = lowestRatio * obj.data('width');
    var height = lowestRatio * obj.data('height');
  }
  obj.height(height);
  obj.width(width);
  obj.css('left', posX);
  obj.css('top', posY);
}

var tchatRefreshTimer = null;
function sendTchatMsg() {
  var tsTreshold = $hj('#online-chat-content li:last-child').data('timestamp');
  var data = {
    'action': 'dealWithAjax',
    'ajaxAction': 'updateLiveMission',
    'uniqid': $hj('#page-mission-online').data('uniqid'),
    'act' : 'tchat',
    'author' : '',
    'tsTreshold' : tsTreshold,
    'msg' : '',
  };
  updateLiveMissionXml(data);
  clearTimeout(tchatRefreshTimer);
  tchatRefreshTimer = setInterval(sendTchatMsg, 30000);
}
















/*
var arrHisto = [''];
var rkHisto = 0;
var $hj = jQuery;
$hj(document).ready(function(){
  // On veut pouvoir afficher et cacher les panneaux de compétences et d'équipement des Survivants
  $hj('article.liveSurvivor .nav-link').unbind().click(function(){
    var tab = $hj(this).data('tab');
    $hj('article.liveSurvivor .skillsLis').removeClass('active');
    $hj('article.liveSurvivor .equipList').removeClass('active');
    $hj(this).parent().parent().siblings('.'+tab).addClass('active');
  });

  // Déclencheurs d'actions sur les Boutons de la Toolbar
  initToolbarButtonActions();


    //////////////////////////////////////////////////////////////////////////////////
    // Setting Chat Height, according to # of Survivors
    var heightBoard = $hj('#online-board').height();
    var nbSurvivors = $hj('#online-sidebar-options article').length;
    $hj('#online-sidebar-chat').css('height', heightBoard - nbSurvivors*53);
    //////////////////////////////////////////////////////////////////////////////////


/*
    var height = $hj('body').height()-17;
    height -= $hj('#wpadminbar').height();
    height -= $hj('#shell > header').height();
    height -= $hj('#shell > footer').height();
    height -= $hj('#online-btn-actions').height();
    $hj('#online-board').css('height', height);

    height =  $hj('#online-sidebar-chat').height();
    height -= $hj('.online-chat-saisie').height();
    $hj('#online-chat-content').css('height', height);

    $hj('.online-chat-unfold').unbind().click(function(){ $hj(this).parent().parent().toggleClass('closed-chat'); });
    $hj('.online-chat-fold').unbind().click(function(){ $hj(this).parent().parent().toggleClass('closed-chat'); });
  *
    $hj('#online-chat-input').bind('keypress', function(e) {
      if (e.keyCode == 13 ) {
        sendMessage();
      } else if (e.keyCode == 38 ) {
        $hj('#online-chat-input').val(arrHisto[rkHisto]);
        if (rkHisto>0 ) { rkHisto--; }
      } else if (e.keyCode == 40 ) {
        if (rkHisto<arrHisto.length ) {
          rkHisto++;
          $hj('#online-chat-input').val(arrHisto[rkHisto]);
        } else {
          $hj('#online-chat-input').val('');
        }
      } else {
        //console.log(e.keyCode);
      }
    });
    $hj('#online-chat-submit').unbind().click(function(e){
      e.preventDefault();
      sendMessage();
      return false;
    });
    window.setInterval(function(){refreshChatContent()}, 5000);
});

function initToolbarButtonActions() {
  $hj('#online-btn-actions .btn').unbind().click(function(){
    if ($hj(this).hasClass('disabled')) {
      return false;
    }
    var obj;
    var ajaxAction = $hj(this).data('ajaxaction');
    var ajaxChildAction = $hj(this).data('ajaxchildaction');
    var liveSurvivorId = $hj(this).data('livesurvivor');
    var data = {'action': 'dealWithAjax', 'ajaxAction': ajaxAction, 'ajaxChildAction': ajaxChildAction, 'liveSurvivorId': liveSurvivorId};
    $hj.post(
      ajaxurl,
      data,
      function(response) {
        try {
          obj = JSON.parse(response);
          dealWithAjaxResponse(obj);
        } catch (e) {
          console.log("error: "+e);
          console.log(response);
        }
      }
    );
  });
}
function refreshChatContent() {
  var obj;
  var timestamp = $hj('#online-chat-content li:last-child').data('timestamp');
  var liveId = $hj('#online-sidebar-chat li a.active').data('liveid');
  var data = {'action': 'dealWithAjax', 'ajaxAction': 'refreshChat', 'liveId': liveId, 'timestamp': timestamp};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
        dealWithAjaxResponse(obj);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
function sendMessage() {
  var obj;
  var timestamp = $hj('#online-chat-content li:last-child').data('timestamp');
  var text = $hj('#online-chat-input').val();
  var arrWords = text.split(' ');
  var liveId = $hj('#online-sidebar-chat li a.active').data('liveid');
  var data = {'action': 'dealWithAjax', 'ajaxAction': 'postChat',  'liveId': liveId, 'texte': text, 'timestamp': timestamp};
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        obj = JSON.parse(response);
        if (arrWords[0] == '/clean' ) {
          $hj('#online-chat-content').html('');
        }
        $hj('#online-chat-input').val('');
        arrHisto.push(text);
        rkHisto = arrHisto.length-1;
        dealWithAjaxResponse(obj);
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
function dealWithAjaxResponse(obj) {
  for (var anchor in obj) {
    if ($hj('#'+anchor).length==1 ) {
      switch (anchor) {
        case 'online-chat-content'   :
          $hj('#'+anchor).append(obj[anchor]);
          addChatMsgActions();
          if ( obj['online-chat-content']!='' ) {
            $hj('#online-chat-content').stop().animate({ scrollTop: $hj('#online-chat-content')[0].scrollHeight }, 2000);
          }
          break;
        case 'header-ul-chat-saisie' :
          $hj('#'+anchor).html(obj[anchor]);
        break;
        case 'online-btn-actions' :
          $hj('#'+anchor).html(obj[anchor]);
          initToolbarButtonActions();
        break;
        case 'online-popup-modal' :
          $hj('#'+anchor).html(obj[anchor]);
          $hj('.btn.btn-choose-survivor').unbind().click(function(){
            var liveSurvivorId = $hj(this).data('livesurvivor-id');
            var data = {'action': 'dealWithAjax', 'ajaxAction': 'startTurn', 'liveSurvivorId': liveSurvivorId};
            $hj.post(
              ajaxurl,
              data,
              function(response) {
                try {
                  obj = JSON.parse(response);
                  dealWithAjaxResponse(obj);
                  initToolbarButtonActions();
                } catch (e) {
                  console.log("error: "+e);
                  console.log(response);
                }
              }
            );
          });
        break;
      }
    }
  }
}
function addChatMsgActions() {
  if ($hj('#online-chat-content').length != 0 ) {
    $hj('#online-chat-content .author').unbind().click(function(){
      $hj('#online-chat-input').val('@'+$hj(this).data('displayname')+' ');
    });
    $hj('#online-chat-content .keyDeck').unbind().click(function(){
      $hj('#online-chat-input').val('/join '+$hj(this).data('keydeck'));
    });
  }
}

*/

