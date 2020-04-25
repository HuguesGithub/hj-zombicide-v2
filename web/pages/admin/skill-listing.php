<div class="wrap">
  <h1 class="wp-heading-inline">Compétences</h1>
  <a href="%3$s" class="page-title-action">Ajouter</a>
  <hr class="wp-header-end">
  
  <h2 class="screen-reader-text">Filtrer la liste des compétences</h2>
  <ul class="subsubsub">%4$s</ul>
  
  <form action="#" method="post" id="post-filters">
    <div class="tablenav top">
      <div class="alignleft actions bulkactions" style="display: inline; float: none;">
        <select name="action" id="bulk-action-selector-top" class="custom-select custom-select-sm filters">
          <option value="-1">Actions groupées</option>
          <option value="trash">Déplacer dans la corbeille</option>
      <!--
          <option value="edit" class="hide-if-no-js">Modifier</option>
          <option value="duplicate_post_clone">Dupliquer</option>
      -->
        </select>
        <input id="doaction" class="button action" value="Appliquer" type="submit" name="postAction">
      </div>
      <div class="tablenav-pages" style="height: 34px;">%5$s</div>
      <br class="clear">
    </div>
    <table class="table table-striped table-bordered table-hover table-sm">
      <thead>
        <tr>
          <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
          <th scope="col" id="code" class="manage-column sortable %6$s">
            <a href="%7$s" style="padding: 0;"><span>Code</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" id="name" class="manage-column column-primary sortable %8$s">
            <a href="%9$s" style="padding: 0;"><span>Nom</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" id="description" class="manage-column">Description</th>
        </tr>
      </thead>
      <tbody id="the-list">%1$s</tbody>
      <tfoot>
        <tr>
          <td class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
          <th scope="col" class="manage-column sortable %6$s">
            <a href="%7$s" style="padding: 0;"><span>Code</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" class="manage-column column-primary sortable %8$s">
            <a href="%9$s" style="padding: 0;"><span>Nom</span><span class="sorting-indicator"></span></a>
          </th>
          <th scope="col" class="manage-column">Description</th>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
