<section id="page-missions">
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
  <section id="filters" style="display:%12$s">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-title" class="filter-title">Titre</label>
      </div>
      <input type="text" class="form-control" id="filter-title" placeholder="Rechercher un Titre" aria-label="Titre" aria-describedby="filter-title" value="%13$s">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
      </div>
    </div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-levelId" class="filter-levelId">Difficulté</label>
      </div>
      <select class="form-control" id="filter-levelId" name="filter-levelId">%14$s</select>
      <div class="input-group-prepend input-group-append">
        <label class="input-group-text" for="filter-playerId">Survivants</label>
      </div>
      <select class="form-control" id="filter-playerId" name="filter-playerId">%15$s</select>
    </div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-durationId">Durée</label>
      </div>
      <select class="form-control" id="filter-durationId" name="filter-durationId">%16$s</select>
      <div class="input-group-prepend input-group-append">
        <label class="input-group-text" for="filter-origineId">Origine</label>
      </div>
      <select class="form-control" id="filter-origineId" name="filter-origineId">%17$s</select>
    </div>
    <div class="input-group">
      <div class="input-group-prepend">
        <label class="input-group-text" for="filter-expansionId" style="height: 102px">Extension</label>
      </div>
      <select multiple="" class="form-control" id="filter-expansionId" name="filter-expansionId">%18$s</select>
    </div>
  </section>
  <section id="listing">
    <div class="publicMissionRow tableHeader row">
      <span class="col-12 col-md-3">Nom</span>
      <span class="col-md-4">Difficulté, Joueurs &amp; Durée <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-3">Extensions <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-2">Origine <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    %11$s
    <div class="publicMissionRow tableHeader row">
      <span class="col-12 col-md-3">Nom</span>
      <span class="col-md-4">Difficulté, Joueurs &amp; Durée</span>
      <span class="col-md-3">Extensions</span>
      <span class="col-md-2">Origine</span>
    </div>
  </section>
  %4$s
</section>
