<section id="page-missions">
  %2$s
  <section id="listing">
    <div class="publicMissionRow tableHeader row">
      <span class="col-12 col-md-3">Nom</span>
      <span class="col-md-4">Difficulté, Joueurs &amp; Durée <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-3">Extensions <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-2">Origine <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    <div class="publicSkillRow tableHeader row"  id="filters" style="border-radius:0;padding:10px;display:%4$s">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-title" class="filter-title">Titre</label>
        </div>
        <input type="text" class="form-control" id="filter-title" placeholder="Rechercher un Titre" aria-label="Titre" aria-describedby="filter-title" value="%5$s">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
        </div>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-levelId" class="filter-levelId">Difficulté</label>
        </div>
        <select class="form-control" id="filter-levelId" name="filter-levelId">%6$s</select>
        <div class="input-group-prepend input-group-append">
          <label class="input-group-text" for="filter-playerId">Survivants</label>
        </div>
        <select class="form-control" id="filter-playerId" name="filter-playerId">%7$s</select>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-durationId">Durée</label>
        </div>
        <select class="form-control" id="filter-durationId" name="filter-durationId">%8$s</select>
        <div class="input-group-prepend input-group-append">
          <label class="input-group-text" for="filter-origineId">Origine</label>
        </div>
        <select class="form-control" id="filter-origineId" name="filter-origineId">%9$s</select>
      </div>
      <div class="input-group">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-expansionId" style="height: 102px">Extension</label>
        </div>
        <select multiple="" class="form-control" id="filter-expansionId" name="filter-expansionId">%10$s</select>
      </div>
    </div>
    %1$s
    <div class="publicMissionRow tableHeader row">
      <span class="col-12 col-md-3">Nom</span>
      <span class="col-md-4">Difficulté, Joueurs &amp; Durée</span>
      <span class="col-md-3">Extensions</span>
      <span class="col-md-2">Origine</span>
    </div>
  </section>
  %3$s
</section>
