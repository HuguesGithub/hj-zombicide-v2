<section id="page-survivants">
  <div class="overlay"><div class="spinner"></div></div>
  <section class="row">
    <div class="col-5">%2$s</div>
    <div class="col-7">%3$s</div>
  </section>
  <section id="listing">
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="col-md-4">Compétences <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="survivorExpansion col-md-4">Extension <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
    </div>
    <div class="publicSurvivorRow tableHeader row"  id="filters" style="border-radius:0;padding:10px;display:%4$s">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-name" class="filter-name">Nom</label>
        </div>
        <input type="text" class="form-control" id="filter-name" placeholder="Rechercher un Nom" aria-label="Nom" aria-describedby="filter-name" value="%5$s">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
        </div>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-expansionId" style="height: 102px">Extension</label>
        </div>
        <select multiple="" class="form-control" id="filter-expansionId" name="filter-expansion">%6$s</select>
      </div>
      <div class="input-group">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-skills">Compétences</label>
        </div>
        <select class="form-control" id="filter-blue-skill" name="filter-blue-skill">%7$s</select>
        <select class="form-control" id="filter-yellow-skill" name="filter-yellow-skill">%8$s</select>
        <select class="form-control" id="filter-orange-skill" name="filter-orange-skill">%9$s</select>
        <select class="form-control" id="filter-red-skill" name="filter-red-skill">%10$s</select>
      </div>
    </div>
    %1$s
    <div class="publicSurvivorRow tableHeader row">
      <span class="survivorPortraits col-12 col-md-1"></span>
      <span class="survivorName col-12 col-md-3">Nom</span>
      <span class="col-md-4">Compétences</span>
      <span class="survivorExpansion col-md-4">Extension</span>
    </div>
  </section>
</section>
