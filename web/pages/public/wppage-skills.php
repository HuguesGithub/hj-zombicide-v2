<section id="page-competences">
  <div class="overlay"><div class="spinner"></div></div>
  <section class="row">
    <div class="col-5">%2$s</div>
    <div class="col-7">%3$s</div>
  </section>
  <section id="listing">
    <div class="publicSkillRow tableHeader row">
      <span class="skillName col-12 col-sm-3 col-md-2">Nom</span>
      <span class="skillDescription col-12 col-sm-9 col-md-8">Description <i class="fas fa-filter float-right" style="margin-top:3px;"></i></span>
      <span class="skillRepByLevel d-none d-md-block col-md-2">Niveau</span>
    </div>
    <div class="publicSkillRow tableHeader row" id="filters" style="border-radius:0;padding:10px;display:%4$s;">
      <div class="input-group">
        <div class="input-group-prepend">
          <label class="input-group-text" for="filter-description" class="filter-description">Description</label>
        </div>
        <input type="text" class="form-control" id="filter-description" placeholder="Rechercher dans la Description ou le Nom" aria-label="Description" aria-describedby="filter-description" value="%5$s">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary ajaxAction" data-ajaxaction="filter" type="button">Rechercher</button>
        </div>
      </div>
    </div>
    %1$s
    <div class="publicSkillRow tableHeader row">
      <span class="skillName col-12 col-sm-3 col-md-2">Nom</span>
      <span class="skillDescription col-12 col-sm-9 col-md-8">Description</span>
      <span class="skillRepByLevel d-none d-md-block col-md-2">Niveau</span>
    </div>
  </section>
</section>
