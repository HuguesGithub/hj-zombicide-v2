<style>
  #wpwrap {background-color:#f1f1f1;}
</style>
<div class="wrap">
  <h1 class="wp-heading-inline">Extensions</h1>
  <a href="%3$s" class="page-title-action">Ajouter</a>
  <hr class="wp-header-end">

  <h2 class="screen-reader-text">Filtrer la liste des extensions</h2>
  <ul class="subsubsub">%4$s</ul>

  <form action="#" method="post" id="post-filters">
    <div class="tablenav top">
      <div class="alignleft actions bulkactions" style="display: inline; float: none;">
        <select name="action" id="bulk-action-selector-top" class="custom-select custom-select-sm filters">
          <option value="-1">Actions groupées</option>
          <option value="trash">Déplacer dans la corbeille</option>
        </select>
        <input id="doaction" class="button action" value="Appliquer" type="submit" name="postAction">
      </div>
      <div class="tablenav-pages" style="height: 34px;">%5$s</div>
      <br class="clear">
    </div>
    <table class="wp-list-table widefat fixed striped posts" aria-describedby="Listing des extensions">
      <thead>
        <tr>
          <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
          <th scope="col" id="code" class="manage-column">Code</th>
          <th scope="col" id="name" class="manage-column">Nom</th>
          <th scope="col" id="displayRank" class="manage-column">Rank d'affichage</th>
          <th scope="col" id="nbSurvivants" class="manage-column">Survivants</th>
          <th scope="col" id="nbMissions" class="manage-column">Missions</th>
          <th scope="col" id="official" class="manage-column column-date">Officiel</th>
        </tr>
      </thead>
      <tbody id="the-list">%1$s</tbody>
      <tfoot>
        <tr>
          <td class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
          <th scope="col" class="manage-column">Code</th>
          <th scope="col" class="manage-column">Nom</th>
          <th scope="col" class="manage-column">Rank d'affichage</th>
          <th scope="col" class="manage-column">Survivants</th>
          <th scope="col" class="manage-column">Missions</th>
          <th scope="col" class="manage-column column-date">Officiel</th>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
