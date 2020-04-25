<section id="page-competences">
  <div class="dropdown">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="displayedRows">Afficher par page :</label>
      </div>
      <select id="displayedRows" class="custom-select" data-ajaxaction="display">
        <option value="10" %1$s class="ajaxAction" data-ajaxaction="display">10 résultats</option>
        <option value="25" %2$s class="ajaxAction" data-ajaxaction="display">25 résultats</option>
        <option value="50" %3$s class="ajaxAction" data-ajaxaction="display">50 résultats</option>
      </select>
    </div>
  </div>
  <section id="filters" style="display:%15$s;">
    <div class="input-group">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-name" class="filter-name">Nom</label>
      </div>
      <input type="text" class="form-control" id="filter-name" placeholder="Rechercher un Nom" aria-label="Nom" aria-describedby="filter-name" value="%14$s">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
      </div>
    </div>
    <div class="form-group">
      <label for="filter-expansion">Extension</label>
      <select multiple="" class="form-control" id="filter-expansion" name="filter-expansion">%12$s</select>
    </div>
  </section>
  <section id="listing">
    <div class="publicSkillRow tableHeader row">
      <span class="skillName col-12 col-sm-3 col-md-2"><span class="sorting%5$s ajaxAction" data-colsort="name" data-colorder="%5$s" data-ajaxaction="sort">Nom</span></span>
      <span class="skillDescription col-12 col-sm-9 col-md-8">Description <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="skillRepByLevel d-none d-md-block col-md-2">Niveau</span>
    </div>
    %6$s
    <div class="publicSkillRow tableHeader row">
      <span class="skillName col-12 col-sm-3 col-md-2">Nom</span>
      <span class="skillDescription col-12 col-sm-9 col-md-8">Description</span>
      <span class="skillRepByLevel d-none d-md-block col-md-2">Niveau</span>
    </div>
  </section>
  <nav>
    <div class="input-group justify-content-between">
      <div class="">
        <div class="input-group-text" id="btnGroupAddon">Affichés %7$s à %8$s sur %9$s résultats</div>
      </div>
      <ul class="pagination">
        <li class="page-item%11$s"><a class="page-link ajaxAction" href="#" data-paged="1" data-ajaxaction="paged">&laquo;</a></li>
        %10$s
        <li class="page-item%12$s"><a class="page-link ajaxAction" href="#" data-paged="%13$s" data-ajaxaction="paged">&raquo;</a></li>
      </ul>
    </div>
  </nav>
</section>
