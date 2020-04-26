<style>#wpwrap {background-color:#f1f1f1;}</style>
<section class="wrap">
  <h2>Contrôles des données</h2>
  <div class="container row">
    <div class="card col-4">
      <div class="card-body">
        <h5 class="card-title">Compétences</h5>
        <a href="http://zombicidev2.jhugues.fr/wp-admin/admin.php?page=hj-zombicide/admin_manage.php&amp;getAction=controlSkill" class="page-title-action">Vérifier</a>
        <p class="card-text">%2$s</p>
      </div>
    </div>

    <div class="card col-4">
      <div class="card-body">
        <h5 class="card-title">Missions</h5>
        <a href="http://zombicidev2.jhugues.fr/wp-admin/admin.php?page=hj-zombicide/admin_manage.php&amp;getAction=controlMission" class="page-title-action">Vérifier</a>
        <p class="card-text">%3$s</p>
      </div>
    </div>

    <div class="card col-4">
      <div class="card-body">
        <h5 class="card-title">Survivants</h5>
        <a href="http://zombicidev2.jhugues.fr/wp-admin/admin.php?page=hj-zombicide/admin_manage.php&amp;getAction=controlSurvivant" class="page-title-action">Vérifier</a>
        <p class="card-text"></p>
      </div>
    </div>
  </div>
</section>
<fieldset class="options">
  <legend>Sauvegarde programmée</legend>
  <p id="backup-time-wrap">Prochaine sauvegarde : <span id="next-backup-time">%1$s</span></p>
  <form action="#" method="post">
    <p><input type="submit" name="reset" value="Mettre à jour" class="btn btn-primary btn-xs"/></p>
  </form>
</fieldset>
