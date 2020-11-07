<link rel="stylesheet" href='http://zombicide.jhugues.fr/wp-content/plugins/hj-zombicide/web/rsc/zombicide-online.css'></link>
<script src='http://zombicide.jhugues.fr/wp-content/plugins/hj-zombicide/web/rsc/zombicide-online.js'></script>
<section id="page-mission-online">
    <section id="page-mission-online-login" class="row">
      <form id="formLogin" method="post" action="#">
        <div class="form-group">
          <div class="btn-group-vertical mb-3" role="group">
            <p>
              Je certifie posséder au moins une Saison Stand-Alone de Zombicide Moderne et pouvoir ainsi profiter de cette application.<br><br>
              Je reconnais avoir connaissance du fait que cette application n'est pas un produit officiel, qu'il n'est qu'en version Bêta et qu'il peut contenir des bugs.
            </p>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="cgu" name="cgu">
              <span class="form-check-label" for="cgu">Cocher pour validation</span>
            </div>
            <div type="button" class="btn btn-primary btn-expansion-group disabled" id="validCGU">
              <span><i class="far fa-check-circle"></i></span> Valider
            </div>
          </div>
        </div>
        <div class="form-group" style="display:none;">
          <div class="btn-group-vertical mb-3" role="group">
            <div type="button" class="btn btn-expansion btn-dark"><span><i class="fa %4$s"></i></span> Partie Sauvegardée</div>
            <input type="radio" class="hidden" name="radioChoice" value="old"/>
            <div class="input-group">
              <div class="input-group-prepend">
                <label class="btn btn-expansion btn-dark" type="button" style="border-radius: 0;">Code</button>
              </div>
              <input type="text" class="form-control" placeholder="Sauvegarde" style="border-radius: 0;" name="saveCode" %2$s>
            </div>
            <div type="button" class="btn btn-expansion btn-dark"><span><i class="fa %5$s"></i></span> Nouvelle Partie</div>
            <input type="radio" class="hidden" name="radioChoice" value="new" %3$s/>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="btn btn-expansion btn-dark" style="border-radius: 0;">Mission : </span>
              </div>
              <select class="custom-select" style="border-radius: 0;" name="selectMission">
                <option value="AJ01">AJ01 - Joueur en Détresse</option>
              </select>
            </div>
            <div type="button" class="btn btn-primary btn-expansion-group" id="startMission"><span><i class="far fa-check-circle"></i></span> Jouer</div>
          </div>
        </div>
      </form>
      %1$s
    </section>
</section>
