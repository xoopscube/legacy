<?php
// v2.3.0 2021/05/15 @gigamaster XCL-PHP7

$content =
	'<div class="ui-tab-wrap">
	<input type="radio" id="ui-tab1" name="ui-tabGroup1" class="ui-tab" checked="">
	<label for="ui-tab1">XOOPSCube</label>

	<input type="radio" id="ui-tab2" name="ui-tabGroup1" class="ui-tab">
	<label for="ui-tab2">licence</label>

	<input type="radio" id="ui-tab3" name="ui-tabGroup1" class="ui-tab">
    <label for="ui-tab3">Prérequis</label>

    <div class="ui-tab-content">
    <p><b>XCL</b> est une <strong>Plateforme d\'application Web</strong> avec une architecture modulaire
     faisant de XCL un outil idéal pour développer des sites Web communautaires dynamiques de petite à grande taille,
     portails intra-entreprise, portails d\'entreprise, blogs et bien plus encore.
    </p>
    </div>

    <div class="ui-tab-content">
    <p>
    Le le noyau Cube est publié sous les termes de la <a href="https://github.com/xoopscube/legacy/blob/2.3/BSD_license.txt" target="_blank">New BSD License</a>.
    Il est librement redistribuable tant que vous respectez les termes de distribution New BSD License.
    </p>
    <p>
    Les modules XCL sont publiés sous les termes de la <a href="https://github.com/xoopscube/legacy/blob/2.3/gpl-2.0_license.md" target="_blank">GNU General Public License (GPL)</a>.
    Ils sont librement redistribuablea tant que vous respectez les termes de distribution GPL.
    </p>
    </div>

    <div class="ui-tab-content">
    <p>
    </p><ul>
    <li><a href="https://www.apache.org/" target="_blank" rel="external">Apache</a>, <a href="https://www.nginx.com/" target="_blank" rel="external">Nginx</a> ou tout autre serveur Web.</li>
    <li><a href="https://www.php.net/" target="_blank" rel="external">PHP7</a> ou supérieure</li>
    <li><a href="https://www.mysql.com/" target="_blank" rel="external">MySQL</a> ou <a href="https://mariadb.org/" target="_blank" rel="external">MariaDB</a> Base de données 5.6.x ou supérieure</li>
    </ul>
    <p></p>
    </div>
</div>
    <h3>Liste de contrôle pour l\'installation</h3>
    <p><input type="checkbox" required=""> Configurer serveur Web, PHP7 et base de données SQL.
    </p><p><input type="checkbox" required=""> Base de données utilisant <em>utf8mb4_general_ci</em> collation, utilisateur et mot de passe.
    </p><p>Rendre les répertoires et les fichiers accessibles en écriture :
    </p><p><input type="checkbox" required> <code>html/uploads/</code>
    </p><p><input type="checkbox" required> <code>xoops_trust_path/cache/</code>
    </p><p><input type="checkbox" required> <code>xoops_trust_path/templates_c/</code>
    </p><p><input type="checkbox" required> <code>html/mainfile.php</code>
    </p><p>Paramètres du navigateur Web
    </p><p><input type="checkbox" required=""> Activer les options: cookies et JavaScript.
    </p><h3>Prêt à installer</h3>
    <p><input type="checkbox" class="all-check" name="all-check" id="all-check"> Tout cocher</input></p>
    <div class="confirmInfo">Cliquez sur suivant et poursuivez les instructions supplémentaires fournies par l\'assistant d\'installation.</div>
';
