<style>
  #wpwrap {background-color:#f1f1f1;}
  .tab-pane {    background: #fff;    padding: 5px;}
</style>
<div class="wrap">
  <div class="row">
    <div class="col">----
    </div>
    <div class="col">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text">Id</span>
        </div>
        <input type="text" class="form-control" placeholder="Id" value="%5$s" disabled>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text">Nom</span>
        </div>
        <input type="text" class="form-control" placeholder="Nom" value="%6$s" disabled>
      </div>
      <div class="input-group mb-3">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" disabled %7$s>
          <label class="form-check-label">Survivant</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" disabled %8$s>
          <label class="form-check-label">Zombivant</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" disabled %9$s>
          <label class="form-check-label">Ultimate</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" disabled %10$s>
          <label class="form-check-label">Ultimate Zombivant</label>
        </div>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text">Extension</span>
        </div>
        <input type="text" class="form-control" placeholder="Extension" value="%11$s" disabled>
      </div>
    </div>
  </div>
  <ul class="nav nav-tabs" id="survivorSkillsTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="survivor-tab" data-toggle="tab" href="#survivor-tabContent" role="tab" aria-controls="survivor" aria-selected="true">Survivant</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="zombivor-tab" data-toggle="tab" href="#zombivor-tabContent" role="tab" aria-controls="zombivor" aria-selected="false">Zombivant</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="ultimate-tab" data-toggle="tab" href="#ultimate-tabContent" role="tab" aria-controls="ultimate" aria-selected="false">Ultimate</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="ultimatez-tab" data-toggle="tab" href="#ultimatez-tabContent" role="tab" aria-controls="ultimatez" aria-selected="false">Ultimate Zombivant</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="survivor-tabContent" role="tabpanel" aria-labelledby="survivor-tab">
      <div class="row">
        <div class="col"><span class="badge badge-blue-skill col-3">Bleu</span></div>
        <div class="col"><span class="badge badge-yellow-skill col-3">Jaune</span></div>
        <div class="col"><span class="badge badge-orange-skill col-3">Orange</span></div>
        <div class="col"><span class="badge badge-red-skill col-3">Rouge</span></div>
      </div>
      %1$s
    </div>
    <div class="tab-pane fade row" id="zombivor-tabContent" role="tabpanel" aria-labelledby="zombivor-tab">
      <div class="row">
        <div class="col"><span class="badge badge-blue-skill col-3">Bleu</span></div>
        <div class="col"><span class="badge badge-yellow-skill col-3">Jaune</span></div>
        <div class="col"><span class="badge badge-orange-skill col-3">Orange</span></div>
        <div class="col"><span class="badge badge-red-skill col-3">Rouge</span></div>
      </div>
      %2$s
    </div>
    <div class="tab-pane fade" id="ultimate-tabContent" role="tabpanel" aria-labelledby="ultimate-tab">
      <div class="row">
        <div class="col"><span class="badge badge-blue-skill col-3">Bleu</span></div>
        <div class="col"><span class="badge badge-yellow-skill col-3">Jaune</span></div>
        <div class="col"><span class="badge badge-orange-skill col-3">Orange</span></div>
        <div class="col"><span class="badge badge-red-skill col-3">Rouge</span></div>
      </div>
      %3$s
    </div>
    <div class="tab-pane fade" id="ultimatez-tabContent" role="tabpanel" aria-labelledby="ultimatez-tab">
      <div class="row">
        <div class="col"><span class="badge badge-blue-skill col-3">Bleu</span></div>
        <div class="col"><span class="badge badge-yellow-skill col-3">Jaune</span></div>
        <div class="col"><span class="badge badge-orange-skill col-3">Orange</span></div>
        <div class="col"><span class="badge badge-red-skill col-3">Rouge</span></div>
      </div>
      %4$s
    </div>
  </div>
</div>
  <script>
  var $hj = jQuery;
  $hj(document).ready(function(){
  $hj('#survivorSkillsTab a').unbind().on('click', function (e) {
    e.preventDefault();
    $hj('#survivorSkillsTab a').removeClass('active');
    $hj(this).addClass('active');
    var id = $hj(this).attr('href');
    $hj(id).siblings().removeClass('show active');
    $hj(id).addClass('show active');
    return false;
  })
  });
  </script>
