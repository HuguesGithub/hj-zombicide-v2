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
            <span class="input-group-text">Titre</span>
          </div>
          <input type="text" class="form-control" placeholder="Titre" value="%3$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Synopsis</span>
          </div>
          <textarea class="form-control" disabled>%4$s</textarea>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Difficulté</span>
          </div>
          <input type="text" class="form-control" placeholder="Difficulté" value="%5$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nb de Survivants</span>
          </div>
          <input type="text" class="form-control" placeholder="Nb de Survivants" value="%6$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Durée</span>
          </div>
          <input type="text" class="form-control" placeholder="Durée" value="%7$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Origine</span>
          </div>
          <input type="text" class="form-control" placeholder="Origine" value="%8$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Extensions</span>
          </div>
          <input type="text" class="form-control" placeholder="Extensions" value="%9$s" disabled>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Dalles</span>
          </div>
          <input type="text" class="form-control" placeholder="Dalles" value="%10$s" disabled>
        </div>
      </div>

      <div class="col">
        Map<br>
        <img src="%11$s" alt="%3$s"/>
      </div>

      <div class="col">
      </div>
    </div>
    <div class="row">
      <input type="hidden" name="postAction" value="confirmEdit"/>
      <input type="hidden" name="id" value="%1$s"/>
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>
