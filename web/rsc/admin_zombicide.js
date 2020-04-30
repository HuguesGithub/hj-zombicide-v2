/*
 * @version 1.04.30
 */
var $hj = jQuery;
$hj(document).ready(function(){
  /***************
   *** #01 - Home
   *** Si on est sur la Homepage
   ***************/
  $hj('#homeAdminBoard .ajaxAction').unbind().click(function(){
    resolveHomeAdminBoardAjaxActions($hj(this));
    return false;
  });
});

function resolveHomeAdminBoardAjaxActions(clicked) {
  var ajaxaction = clicked.data('ajaxaction');
  // On initialise les données de tri et de filtres.
  var data = {'action': 'dealWithAjax', 'ajaxAction': ajaxaction};
  resolveCallAjax(data, ajaxaction);
}

function resolveCallAjax(data, idPage) {
  $hj.post(
    ajaxurl,
    data,
    function(response) {
      try {
        var obj = JSON.parse(response);
        if (obj[idPage] != '' ) {
          $hj('#'+idPage).html(obj[idPage]);
        }
      } catch (e) {
        console.log("error: "+e);
        console.log(response);
      }
    }
  );
}
