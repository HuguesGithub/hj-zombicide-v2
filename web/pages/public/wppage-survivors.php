<section id="page-survivants">
  %2$s
  <section id="listing">
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-2">Zombivor</span>
      <span class="col-md-2">Ultimate</span>
      <span class="survivorExpansion col-md-4">Extension <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    <div class="publicSkillRow tableHeader row"  id="filters" style="border-radius:0;padding:10px;display:%4$s">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-name" class="filter-name">Nom</label>
        </div>
        <input type="text" class="form-control" id="filter-name" placeholder="Rechercher un Nom" aria-label="Nom" aria-describedby="filter-name" value="%5$s">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
        </div>
      </div>
      <div class="input-group">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-expansionId" style="height: 102px">Extension</label>
        </div>
        <select multiple="" class="form-control" id="filter-expansionId" name="filter-expansion">%6$s</select>
      </div>
    </div>
    %1$s
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom</span>
      <span class="col-md-2">Zombivor</span>
      <span class="col-md-2">Ultimate</span>
      <span class="survivorExpansion col-md-4">Extension</span>
    </div>
  </section>
  %3$s
</section>
