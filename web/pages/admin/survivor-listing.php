<style>
  #wpwrap {background-color:#f1f1f1;}
</style>
<div class="wrap">
  <h1 class="wp-heading-inline">Survivants</h1>
  <a href="%3$s" class="page-title-action">Ajouter</a>
  <hr class="wp-header-end">

  <h2 class="screen-reader-text">Filtrer la liste des survivants</h2>
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
    <table class="wp-list-table widefat fixed striped posts" aria-describedby="Listing des survivants">
      <thead>
        <tr>
          <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
          <th scope="col" >Portrait</th>
          <th scope="col" id="name" class="manage-column column-primary sortable %8$s">
            <a href="%9$s" style="padding: 0;"><span>Nom</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" id="zombivor" class="manage-column">Zombivant</th>
          <th scope="col" id="ultimate" class="manage-column">Ultimate</th>
          <th scope="col" id="expansionId" class="manage-column">Extension</th>
          <th scope="col" id="background" class="manage-column">Background</th>
          <th scope="col" id="altImgName" class="manage-column">Nom&nbsp;Alt&nbsp;Img</th>
        </tr>
      </thead>
      <tbody id="the-list">%1$s</tbody>
      <tfoot>
        <tr>
          <td class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
          <th scope="col" >Portrait</th>
          <th scope="col" class="manage-column column-primary sortable %8$s">
            <a href="%9$s" style="padding: 0;"><span>Nom</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" class="manage-column">Zombivant</th>
          <th scope="col" class="manage-column">Ultimate</th>
          <th scope="col" class="manage-column">Extension</th>
          <th scope="col" class="manage-column">Background</th>
          <th scope="col" class="manage-column">Nom Alt Img</th>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js"
  integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
