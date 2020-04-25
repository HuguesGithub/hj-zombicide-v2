<section id="page-survivants">
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
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-name" class="filter-name">Nom</label>
      </div>
      <input type="text" class="form-control" id="filter-name" placeholder="Rechercher un Nom" aria-label="Nom" aria-describedby="filter-name" value="%14$s">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
      </div>
    </div>
    <div class="input-group">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-expansionId" style="height: 102px">Extension</label>
      </div>
      <select multiple="" class="form-control" id="filter-expansionId" name="filter-expansion">%12$s</select>
    </div>
  </section>
  <section id="listing">
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-2">Zombivor</span>
      <span class="col-md-2">Ultimate</span>
      <span class="survivorExpansion col-md-4">Extension <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    %4$s
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom</span>
      <span class="col-md-2">Zombivor</span>
      <span class="col-md-2">Ultimate</span>
      <span class="survivorExpansion col-md-4">Extension</span>
    </div>
  </section>
  <nav>
    <div class="input-group justify-content-between">
      <div class="">
        <div class="input-group-text" id="btnGroupAddon">Affichés %5$s à %6$s sur %7$s résultats</div>
      </div>
      <ul class="pagination">
        <li class="page-item%8$s"><a class="page-link ajaxAction" href="#" data-paged="1" data-ajaxaction="paged">&laquo;</a></li>
        %9$s
        <li class="page-item%10$s"><a class="page-link ajaxAction" href="#" data-paged="%11$s" data-ajaxaction="paged">&raquo;</a></li>
      </ul>
    </div>
  </nav>
</section>
