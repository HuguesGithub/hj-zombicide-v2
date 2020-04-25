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
  */
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

