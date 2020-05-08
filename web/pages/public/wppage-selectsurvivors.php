<style>
#page-tools {
  background-color: #ffffff;
  border-radius: 10px;
  margin: 0 10px;
  padding: 10px;
}
#page-tools .publicSurvivorRow button {
  margin-right: 5px;
  margin-bottom: 5px;
}
#nbSurvSel button:first-child {
  border-bottom-left-radius: 0;
}
#nbSurvSel button:last-child {
  border-bottom-right-radius: 0;
}
#page-tools .survivorBackground {
  display: none;
}
</style>
<section id="page-tools">
  <div class="selectionContainer row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
      <div class="btn-group-vertical team-selection" role="group">
        <div class="btn-toolbar" role="toolbar">
          <div class="btn-group" id="nbSurvSel" role="group">
            <button type="button" class="btn btn-dark" data-nb="1">1</button>
            <button type="button" class="btn btn-dark" data-nb="2">2</button>
            <button type="button" class="btn btn-dark" data-nb="3">3</button>
            <button type="button" class="btn btn-dark" data-nb="4">4</button>
            <button type="button" class="btn btn-dark" data-nb="5">5</button>
            <button type="button" class="btn btn-dark active" data-nb="6">6</button>
          </div>
        </div>
        %1$s
        <div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> Fan-Made</div>
        %3$s
        <div type="button" class="btn btn-primary btn-expansion-group" id="proceedBuildTeam">
          <span><i class="far fa-check-circle"></i></span> Générer
        </div>
        <div class="input-group">
          <input type="text" id="teamLoader" class="form-control" placeholder="Votre sélection" style="border-top-left-radius: 0;">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="loadOwnTeam" style="border-top-right-radius: 0;"><i class="fas fa-upload"></i></span> Charger</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-8 col-log-9">
      <section id="listing">
        <div class="publicSurvivorRow tableHeader row"><span class="survivorPortraits col-12"><span id="nbDisplayed">0</span> Survivants éligibles</span></div>
        <div id="survivor-45" class="publicSurvivorRow survivant row">%2$s</div>
        <div class="publicSurvivorRow tableHeader row">
          <span class="survivorPortraits col-12 col-md-8"><span id="nbSelected">0</span> Survivants sélectionnés</span>
          <div class="col-12 col-md-4" style="float:right;">
            <button class="btn btn-sm btn-outline-secondary" type="button" id="saveOwnTeam"><i class="fas fa-download"></i></span> Sauvegarder</button>
          </div>
        </div>
      </section>
      <section id="page-selection-survivants"></section>
    </div>
  </div>
</section>

