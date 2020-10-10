var $hj = jQuery;
var lowestRatio = 1;
$hj(document).ready(function(){
  // Quand on affiche la page pour la première fois, on doit fixer les différents cadres.
  // Pour le moment ces données sont fixes. Elles pourraient être fonction du profil dans un deuxième temps.
  setSectionSizes();

  $hj('.chip').each(function(){
    setChipSizes($hj(this));
  });

  setChipsAction();

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
});

  $hj(window).resize(function(){
    setSectionSizes();
  });

var lstElements = '';
function updateLiveMissionXml(data) {
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        var obj = JSON.parse(response);
        if (obj['lstElements'] != '' ) {
          lstElements = obj['lstElements'];
          var id = lstElements[0];
          var element = lstElements[1];
          if ($hj('#'+id).length==0) {
            // Ajoute Nouveau.
            $hj('#page-mission-online-tokens').prepend(element);
            setChipSizes($hj('#'+id));
            setZombieChipActions($hj('#'+id));
          } else {
            // Replace existant
            $hj('#'+id).replaceWith(element);
            setChipSizes($hj('#'+id));
            switch ($hj('#'+id).data('type')) {
              case 'Door' :
                setDoorChipActions($hj('#'+id));
              break;
              case 'Exit' :
                setExitChipActions($hj('#'+id));
              break;
              case 'Objective' :
                setObjectiveChipActions($hj('#'+id));
              break;
              case 'Spawn' :
                setSpawnChipActions($hj('#'+id));
              break;

            }
          }
        }

      } catch (e) {
        //console.log("error: "+e);
        //console.log(response);
      }
    }
  ).done(
    function() {
      console.log('updateLiveMissionXml Done');
      //setChipsAction();
      //setChipSizes();
    }
  );
}


//////////////////////////////////////////////////
// Gestion des clicks sur un Objectif  ///////////
//////////////////////////////////////////////////
function setObjectiveChipActions(obj) {
  obj.unbind().click(function(){
    var dataStatus = $hj(this).prop('data-status') || $hj(this).data('status');
    var newStatus = (dataStatus=='Unveiled' ? 'Unactive' : 'Picked');
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': $hj('#page-mission-online').data('uniqid'), 'id': $hj(this).attr('id'), 'status': newStatus};
    updateLiveMissionXml(data);
    if (newStatus=='Picked') {
      $hj(this).remove();
    }
  });
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////

///////////////////////////////////////
// Gestion des clicks sur la Zone de Sortie
///////////////////////////////////////
function setExitChipActions(obj) {
  obj.unbind().click(function(){
    var dataStatus = $hj(this).prop('data-status') || $hj(this).data('status');
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': $hj('#page-mission-online').data('uniqid'), 'id': $hj(this).attr('id'), 'status': (dataStatus=='Unactive' ? 'Active' : 'Unactive')};
    updateLiveMissionXml(data);
  });
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//////////////////////////////////////////////////
// Gestion des clicks sur une Porte    ///////////
//////////////////////////////////////////////////
function setDoorChipActions(obj) {
  obj.unbind().click(function(){
    var dataStatus = $hj(this).prop('data-status') || $hj(this).data('status');
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': $hj('#page-mission-online').data('uniqid'), 'id': $hj(this).attr('id'), 'status': (dataStatus=='Closed' ? 'Opened' : 'Closed')};
    updateLiveMissionXml(data);
  });
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//////////////////////////////////////////////////
// Gestion des clicks sur une Porte    ///////////
//////////////////////////////////////////////////
function setSpawnChipActions(obj) {
  obj.unbind().on('contextmenu', function(event){
    var dataStatus = $hj(this).prop('data-status') || $hj(this).data('status');
    var newStatus = (dataStatus=='Unactive' ? 'Picked' : 'Unactive');
    var data = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': $hj('#page-mission-online').data('uniqid'), 'id': $hj(this).attr('id'), 'status': newStatus};
    updateLiveMissionXml(data);
    if (newStatus=='Picked') {
      $hj(this).remove();
    }
    event.preventDefault();
    return false;
  }).click(function(event){
    var dataStatus = $hj(this).prop('data-status') || $hj(this).data('status');
    // Le Left Click sur un Spawn permet de le rendre actif s'il est inactif. Ou d'ajouter des Zombies s'il est actif.
    if (dataStatus=='Unactive') {
      var data = {'action': 'dealWithAjax', 'ajaxAction': 'updateLiveMission', 'uniqid': $hj('#page-mission-online').data('uniqid'), 'id': $hj(this).attr('id'), 'status': 'Active'};
      updateLiveMissionXml(data);
    } else {
      var posX = event.clientX;
      var posY = event.clientY;
      $hj('#page-mission-online-zombie-reserve').css('left', posX-125).css('top', posY);
    }
    return false;
  });
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////






function setZombieChipActions(obj) {
  obj.on('mousedown', function(){
    $hj(this).css('cursor', 'grabbing');
  }).draggable({
    containment: "#page-mission-online-tokens",
    scroll: false,
    stop: function(){
      $hj(this).css('cursor', 'grab');
      var top = $hj(this).position().top / lowestRatio;
      var left = $hj(this).position().left / lowestRatio;
      var data = {
        'action': 'dealWithAjax',
        'ajaxAction': 'updateLiveMission',
        'uniqid': $hj('#page-mission-online').data('uniqid'),
        'id': $hj(this).attr('id'),
        'top': top,
        'left': left
      };
      updateLiveMissionXml(data);
    }
  }).on('contextmenu', function(event){
    console.log('Zombie Clicked, Remove one');
    event.preventDefault();
    var dataQty = $hj(this).prop('data-quantity') || $hj(this).data('quantity');
    dataQty = dataQty*1-1;
    if (dataQty==0) {
      $hj(this).remove();
    } else {
      $hj(this).prop('data-quantity', dataQty);
      $hj(this).find('div').html(dataQty);
    }
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'id': $hj(this).attr('id'),
      'quantity': dataQty
    };
    updateLiveMissionXml(data);
    $hj(this).css('cursor', 'grab');
    return false;
  }).click(function(){
    console.log('Zombie Clicked, Add one');
    var dataQty = $hj(this).prop('data-quantity') || $hj(this).data('quantity');
    dataQty = dataQty*1+1;
    $hj(this).prop('data-quantity', dataQty);
    $hj(this).find('div').html(dataQty);
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'id': $hj(this).attr('id'),
      'quantity': dataQty
    };
    updateLiveMissionXml(data);
    $hj(this).css('cursor', 'grab');
  });
}
function setChipsAction() {
  ///////////////////////////////////////
  // Gestion des clicks sur une Porte
  ///////////////////////////////////////
  $hj('div.chip.token[data-type="Door"]').each(function(){
    setDoorChipActions($hj(this));
  })
  ///////////////////////////////////////
  // Gestion des clicks sur la Zone de Sortie
  ///////////////////////////////////////
  $hj('div.chip.token[data-type="Exit"]').unbind().click(function(){
    setExitChipActions($hj(this));
  });
  ///////////////////////////////////////
  // Gestion des clicks sur un Objectif
  ///////////////////////////////////////
  $hj('div.chip.token[data-type="Objective"]').each(function(){
    setObjectiveChipActions($hj(this));
  })
  ///////////////////////////////////////
  // Gestion des clicks sur un Spawn
  ///////////////////////////////////////
  $hj('div.chip.token[data-type="Spawn"]').each(function(){
    setSpawnChipActions($hj(this));
  })















  ///////////////////////////////////////
  // Gestion des clicks sur un Survivant
  ///////////////////////////////////////
  $hj('div.chip.survivor').on('mousedown', function(){
    $hj(this).css('cursor', 'grabbing');
  }).draggable({
    containment: "#page-mission-online-tokens",
    scroll: false,
    stop: function(){
      $hj(this).css('cursor', 'grab');
      var top = $hj(this).position().top / lowestRatio;
      var left = $hj(this).position().left / lowestRatio;
      var data = {
        'action': 'dealWithAjax',
        'ajaxAction': 'updateLiveMission',
        'uniqid': $hj('#page-mission-online').data('uniqid'),
        'id': $hj(this).attr('id'),
        'top': top,
        'left': left
      };
      updateLiveMissionXml(data);
    }
  });



  ///////////////////////////////////////
  // Gestion des clicks sur les Zombies
  ///////////////////////////////////////
  $hj('#page-mission-online-tokens div.chip.zombie').each(function(){
    setZombieChipActions($hj(this));
  });



  ///////////////////////////////////////
  // Gestion des clicks sur la Réserve de Zombies
  ///////////////////////////////////////
  $hj('#page-mission-online-zombie-reserve div').click(function(event){
    console.log('Add Zombie Clicked');
    var dataType = $hj(this).data('type');
    var posX = event.clientX / lowestRatio;
    var posY = event.clientY / lowestRatio;
    // Ajout dans le fichier
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'type': dataType,
      'coordx': posX,
      'coordy': posY
    };
    $hj('#page-mission-online-zombie-reserve .close span').trigger('click');
    updateLiveMissionXml(data);
  });

  ///////////////////////////////////////
  // Fermeture de la Réserve de Zombies
  ///////////////////////////////////////
  $hj('#page-mission-online-zombie-reserve .close span').click(function(){
    $hj('#page-mission-online-zombie-reserve').css('top', -2500);
  });

  ///////////////////////////////////////
  ///////////////////////////////////////
  ///////////////////////////////////////
  ///////////////////////////////////////

  ///////////////////////////////////////
  // Gestion des clicks sur les Survivants inconnus
  ///////////////////////////////////////
  addSidebarSurvivorUnknownActions();

  ///////////////////////////////////////
  // Gestion des clicks sur la Réserve de Survivants
  ///////////////////////////////////////
  $hj('#page-mission-online-survivor-reserve div').click(function(event){
    console.log('Add Survivor Clicked');
    var data = {
      'action': 'dealWithAjax',
      'ajaxAction': 'updateLiveMission',
      'uniqid': $hj('#page-mission-online').data('uniqid'),
      'type' : 'survivor',
      'rank': survivorRankClicked,
      'survivorId' : $hj(this).data('survivorid')
    };
    updateLiveMissionXml(data);

    var src = $hj(this).data('src');
    var srcImg = $hj('#page-mission-online-sidebar-survivors img[data-rank="'+survivorRankClicked+'"]').attr('src').replace('p.jpg', src+'.jpg');
    $hj('#page-mission-online-sidebar-survivors img[data-rank="'+survivorRankClicked+'"]').removeClass('unknown').addClass('known').attr('src', srcImg);
    $hj('#page-mission-online-survivor-reserve .close span').trigger('click');
    addSidebarSurvivorKnownActions();
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
    survivorRankClicked = $hj(this).data('rank');
    console.log('Affichage Detail Survivant '+survivorRankClicked);
    $hj('#page-mission-online-sidebar-survivors-detail dl').css('right', '-350px');
    $hj('#detail-survivor-'+survivorRankClicked).css('right', '0');
  });
  $hj('#page-mission-online-sidebar-survivors-detail .fa-times-circle').click(function(){
    $hj('#page-mission-online-sidebar-survivors-detail dl').css('right', '-350px');
  });
}
function addSidebarSurvivorUnknownActions() {
  $hj('#page-mission-online-sidebar-survivors img.unknown').click(function(event){
    var posX = event.clientX;
    var posY = event.clientY;
    $hj('#page-mission-online-survivor-reserve').css('left', posX-400).css('top', posY);
    survivorRankClicked = $hj(this).data('rank');
    console.log(survivorRankClicked);
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
  var type = obj.data('type');
  if (type=='Objective' || type=='Spawn' || type=='Exit' || type=='Door' || type=='Survivor' || type=='Zombie') {
    var posX   = lowestRatio * obj.data('coordx');
    var posY   = lowestRatio * obj.data('coordy');
    var width  = lowestRatio * obj.data('width');
    var height = lowestRatio * obj.data('height');
  }
  obj.height(height);
  obj.width(width);
  obj.css('left', posX);
  obj.css('top', posY);
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
