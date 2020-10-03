<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<section id="page-tools">
   <div class="selectionContainer row">
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
         <div class="btn-group-vertical" role="group">
            <div class="input-group mb-3">
               <div class="input-group-prepend"><span class="input-group-text badge-dark">Largeur : </span></div>
               <input type="number" id="width" name="width" class="form-control" min="1" max="5" value="1">
            </div>
            <div class="input-group mb-3">
               <div class="input-group-prepend"><span class="input-group-text badge-dark">Hauteur : </span></div>
               <input type="number" id="height" name="height" class="form-control" min="1" max="5" value="1">
            </div>
            <div class="form-group mb-3">
              <label class="input-group-text badge-dark">Extensions</label>
              <div class="btn-group-vertical mb-3" role="group">%1$s</div>
            </div>
            <div type="button" class="btn btn-primary btn-expansion-group" id="proceedRandomMap">
               <span><i class="far fa-check-circle"></i></span>  Générer
            </div>
         </div>
      </div>

      <div class="col-12 col-sm-6 col-md-8 col-lg-9">
         <section id="listing">
            <div class="publicSurvivorRow tableHeader row"><span class="col-11">Map aléatoire</span><span class="col-1"><i class="fa fa-camera pointer"></i></span></div>
            <section id="page-generation-map" class="row">
              <div class="overlay"><div class="spinner"></div></div>&nbsp;
            </section>
            <div class="publicSurvivorRow tableHeader row">&nbsp;</div>
         </section>
      </div>
   </div>
</section>

