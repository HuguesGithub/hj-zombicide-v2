<link rel="stylesheet" href="/wp-content/plugins/mycommon/web/rsc/css/bootstrap-4.min.css" type="text/css" media="all" />
<style>
  #wpwrap {background-color:#f1f1f1;}
  .tab-pane {    background: #fff;    padding: 5px;}
</style>
<div class="wrap">
  <form method="post" action="#">
    <div class="row">
      <div class="col">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Id</span>
          </div>
          <input type="text" class="form-control" placeholder="Id" value="%1$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Code</span>
          </div>
          <input type="text" class="form-control" placeholder="Code" value="%2$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nom</span>
          </div>
          <input type="text" class="form-control" placeholder="Nom" value="%3$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Rang d'affichage</span>
          </div>
          <input type="text" class="form-control" placeholder="Rang d'affichage" value="%4$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" disabled %9$s>
            <label class="form-check-label">Officielle</label>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nombre de Survivants</span>
          </div>
          <input type="text" class="form-control" placeholder="Nombre de Survivants" name="nbSurvivants" value="%5$s">
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nombre de Survivants attendus</span>
          </div>
          <input type="text" class="form-control" placeholder="Nombre de Survivants attendus" value="%6$s" disabled>
        </div>
        <blockquote>%10$s</blockquote>
      </div>
      <div class="col">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nombre de Missions</span>
          </div>
          <input type="text" class="form-control" placeholder="Nombre de Missions" name="nbMissions" value="%7$s">
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nombre de Missions attendues</span>
          </div>
          <input type="text" class="form-control" placeholder="Nombre de Missions attendues" value="%8$s" disabled>
        </div>
      </div>
    </div>
    <div class="row">
      <input type="hidden" name="postAction" value="confirmEdit"/>
      <input type="hidden" name="id" value="%1$s"/>
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>
