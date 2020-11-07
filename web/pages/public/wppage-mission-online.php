<link rel="stylesheet" href='http://zombicide.jhugues.fr/wp-content/plugins/hj-zombicide/web/rsc/zombicide-online.css'></link>
<script src='http://zombicide.jhugues.fr/wp-content/plugins/hj-zombicide/web/rsc/zombicide-online.js'></script>
<section id="page-mission-online" data-uniqid="%5$s">
  <section id="page-mission-online-content">
    <section id="page-mission-online-map" class="%1$s">%2$s</section>
    <section id="page-mission-online-zones" class="%1$s">
      %3$s
    </section>
    <section id="page-mission-online-tokens" class="%1$s">
      %4$s
    </section>
    <menu class="menu">
      <li class="menu-item" data-menu-action="add" data-type="Noise">
        <button type="button" class="menu-btn"><i class="fa fa-plus-circle"></i><span class="menu-text">Ajouter 1 Bruit</span></button>
      </li>
    </menu>
    <section id="page-mission-online-obj-rules">
      <div>%6$s</div>
      <div>%7$s</div>
    </section>
    <section id="page-mission-online-help-website">
      <ul>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Mécanique</header>
            <p>Aucun contrôle de cohérence n'est effectué dans vos Actions. Aussi vous pouvez déplacer un Survivant derrière une porte fermée par exemple. Vous êtes le garant de l'application des règles.<br>Il y a cependant quelques petits contrôles ergonomiques bénins. Par exemple, l'action pour ouvrir une porte n'est pas disponible sur une porte ouverte.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Objectifs</header>
            <p>Cliquez avec le bouton droit sur un Objectif pour afficher le menu contextuel. Vous pouvez révéler un Objectif (pour les Objectifs rouges parmi lesquels un Objectif de couleur peut se cacher) non révélé ou prendre un Objectif révélé.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Portes</header>
            <p>Cliquez avec le bouton droit pour afficher le menu contextuel. Vous pouvez ouvrir ou fermer une porte.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Zone d'invasion</header>
            <p>Cliquez avec le bouton droit pour afficher le menu contextuel. Vous pouvez activer ou désactiver une Zone d'invasion. Vous pouvez même la supprimer. De plus, sur une Zone d'invasion activ, vous pouvez placer des Zombies Standards.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Zombies</header>
            <p>Cliquez avec le bouton droit pour afficher le menu contextuel. Vous pouvez ajouter ou supprimer des Zombies. En maintenant le clic gauche, vous pouvez déplacer un Zombie.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Survivants</header>
            <p>Cliquez avec le bouton droit pour afficher le menu contextuel. Vous pouvez ajouter ou retirer des points d'expérience, d'action ou de vie. Vous pouvez même supprimer un Survivant. En maintenant le clic gauche, vous pouvez déplacer un Survivant.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Bruits</header>
            <p>Cliquez avec le bouton droit pour afficher le menu contextuel. Vous pouvez ajouter ou supprimer des Bruits. En maintenant le clic gauche, vous pouvez déplacer un Bruit. Pour placer un Bruit, cliquer n'importe où sur la Map.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Barre Latérale - Portraits</header>
            <p>Cliquer sur le Portrait inconnu permet d'ajouter un nouveau Survivant. Cliquer sur un Portrait de Survivant permet d'afficher l'écran d'informations des Survivants. On y retrouve pour chaque Survivant, son portrait, son nom, ses points d'expérience, d'action et de vie. On y retrouve aussi ses Compétences débloquées, ou non.<br>Cliquer sur une Compétence permet d'en changer le statut.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Barre Latérale - Icones en bas à droite</header>
            <p>Les 3 icones permettent d'afficher respectivement, de gauche à droite, la Map, les Objectifs et Règles Spéciales de la Mission, cette aide de jeu.</p>
          </div>
        </li>
        <li class="objRule hasTooltip">
          <div class="tooltip">
            <header>Barre Latérale - en haut à dtoire</header>
            <p>La chaîne de caractère (ici <strong>%5$s</strong>) est celle à conserver pour reprendre la même partie, ou inviter des personnes à rejoindre cette partie (attention, pour le moment aucun droit ni aucune synchronisation automatique). Le premier icone permet de copier ce code, le deuxième de quitter la partie.</p>
          </div>
        </li>

      </ul>
    </section>
    <section id="page-mission-online-setup">%11$s</section>
    <section id="page-mission-online-sidebar-survivors-detail">%9$s</section>
  </section>
  <section>
    <div class="modal" id="modalSpawn">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="modal-body" id="modalBody"></div>
        </div>
      </div>
    </div>
  </section>
  <section id="page-mission-online-sidebar">
    <h1><input id="dataToPaste" type="text" value="%5$s"/><a href="?logout"><i class="fa fa-sign-out float-right"></i></a><i class="fa fa-clipboard float-right" data-paste="%5$s"></i></h1>
    <section id="page-mission-online-sidebar-survivors">%8$s</section>
    <section id="page-mission-online-sidebar-chat" class="chat">
      <!-- https://codepen.io/drehimself/pen/KdXwxR -->
      <div class="chat-history">
          <ul id="online-chat-content">%10$s</ul>

        </div> <!-- end chat-history -->
      <div class="online-chat-saisie">
        <content>
          <textarea id="online-chat-input" class="form-control"></textarea>
        </content>
      </div>
    </section>
    <section id="page-mission-online-help">
      <nav class="nav nav-pills nav-fill" aria-label="Raccourcis">
        <a class="nav-item nav-link active" href="#"><i class="fa fa-map-o"></i></a>
        <a class="nav-item nav-link" href="#" data-target="#page-mission-online-setup"><i class="fa fa-cog"></i></a>
        <a class="nav-item nav-link" href="#" data-target="#page-mission-online-obj-rules"><i class="fa fa-book"></i></a>
        <a class="nav-item nav-link" href="#" data-target="#page-mission-online-help-website"><i class="fa fa-question-circle"></i></a>
      </nav>
    </section>
  </section>
  <section id="page-mission-online-survivor-reserve">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <nav aria-label="Survivants disponibles">
      <div class="chip survivor" data-survivorid="s1" data-src="pamy"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pamy.jpg" alt="Amy"></div>
      <div class="chip survivor" data-survivorid="s2" data-src="pdoug"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pdoug.jpg" alt="Doug"></div>
      <div class="chip survivor" data-survivorid="s3" data-src="pjosh"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pjosh.jpg" alt="Josh"></div>
      <div class="chip survivor" data-survivorid="s4" data-src="pned"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pned.jpg" alt="Ned"></div>
      <div class="chip survivor" data-survivorid="s5" data-src="pphil"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pphil.jpg" alt="Phil"></div>
      <div class="chip survivor" data-survivorid="s6" data-src="pwanda"><img src="/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/pwanda.jpg" alt="Wanda"></div>
    </nav>
  </section>
</section>

