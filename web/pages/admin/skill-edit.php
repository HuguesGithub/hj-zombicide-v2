<div class="col-wrap">
  <div class="form-wrap">
    <h2>%1$s</h2>
    <form class="row" action="%2$s" method="post" id="addSkill">
      <div class="col-4">
        <div class="form-field form-required term-name-wrap">
          <label for="name">Nom</label>
          <input type="text" size="40" value="%6$s" id="name" name="name">
        </div>
        <div class="form-field term-code-wrap half-column">
          <label for="code">Code</label>
              <input type="text" size="40" value="%7$s" id="code" name="code">
        </div>
        <div class="form-field term-description-wrap">
          <label for="description">Description</label>
          <textarea id="description" name="description">%8$s</textarea>
        </div>
      </div>
      <div class="col-8">&nbsp;</div>
      <div class="col-4">
        <p class="cancel"><a class="page-title-action" href="%3$s">Annuler</a></p>
        <p class="submit">
          <input type="submit" value="%1$s" class="button button-primary" id="submit" name="submit">
          <input type="hidden" value="%4$s" id="id" name="id">
          <input type="hidden" value="%5$s" id="postAction" name="postAction">
        </p>
      </div>
    </form>
  </div>
</div>
