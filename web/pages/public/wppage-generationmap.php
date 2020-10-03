<style>
#page-tools {
  background-color: #ffffff;
  border-radius: 10px;
  margin: 0 10px;
  padding: 10px;
}
</style>
<section id="page-tools">
  <div class="selectionContainer row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
      <div class="btn-group-vertical" role="group">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text badge-dark">Largeur : </span>
          </div>
          <select class="custom-select" id="width" name="width">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text badge-dark">Hauteur : </span>
          </div>
          <select class="custom-select" id="height" name="height">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="input-group-text badge-dark">Extensions</label>
          <select multiple size="10" class="form-control" id="expansionIds">%1$s</select>
        </div>
        <!--
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text badge-dark">Seuil de Réussite</span>
          </div>
          <input type="number" id="accuracy" name="accuracy" class="form-control" placeholder="4+" min="1" max="6" value="4">
        </div>
        <div class="input-group mb-3">
          <select class="custom-select" id="plusAuDe" name="plusAuDe">
            <option value="0">+0</option>
            <option value="1">+1</option>
            <option value="2">+2</option>
          </select>
          <div class="input-group-append">
            <label class="input-group-text badge-dark"> au résultat du dé</label>
          </div>
        </div>
        -->
        <div type="button" class="btn btn-primary btn-expansion-group" id="proceedRandomMap">
          <span><i class="far fa-check-circle"></i></span> Générer
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-8 col-lg-9">
      <section id="listing">
        <div class="publicSurvivorRow tableHeader row"><span class="col-12">Map aléatoire</div>
        <section id="page-generation-map" class="row">&nbsp;</section>
        <div class="publicSurvivorRow tableHeader row">&nbsp;</div>
      </section>
    </div>
  </div>
</section>

