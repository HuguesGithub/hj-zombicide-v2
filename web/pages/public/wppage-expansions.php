<section id="page-extensions">
  <div class="overlay"><div class="spinner"></div></div>
  <section class="row">
    <div class="col-5">%2$s</div>
    <div class="col-7">%3$s</div>
  </section>
  <section id="listing">
    <div class="publicExpansionRow tableHeader row">
      <span class="expansionName col-12 col-md-3">Nom <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    <div class="publicExpansionRow tableHeader row"  id="filters" style="border-radius:0;padding:10px;display:%4$s">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-name" class="filter-name">Nom</label>
        </div>
        <input type="text" class="form-control" id="filter-name" placeholder="Rechercher un Nom" aria-label="Nom" aria-describedby="filter-name" value="%5$s">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
        </div>
      </div>
    </div>
    %1$s
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorName col-12 col-md-3">Nom</span>
    </div>
  </section>
</section>
