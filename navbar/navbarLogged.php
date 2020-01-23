<div class="container-fluid" style="background-color: #867fce; margin-bottom: 3%;">
    <nav class="container navbar navbar-expand-lg navbar-light">
        <div class="navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link text-light" href=".\index.php">Accueil</a>
                </li>
            </ul>
            <span class="navbar-text">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link text-light" href=".\profil.php?id=<?= sha1($_SESSION['nickname']);?>">Mon Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href=".\logout.php">Déconnexion</a>
                    </li>
                </ul>
            </span>
        </div>
    </nav>
</div>