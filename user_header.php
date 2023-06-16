<?php 
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
            <div class="message">
                <span  onclick="this.parentElement.remove();">'.$message.'</span>
            </div>
            ';
        }
    }
    ?>

<header class="header">
    <div class="flex">
        <a href="user.php" class="logo"> Página de<span> Postagem de fotos</span></a>
        <nav class="nav_bar">
            <a href="user.php">Imagens</a>
        </nav>
        <div class="icons" style="display: flex;">
            <div id="user-btn" class="bi bi-user-fill"><i class="bi bi-person-fill"></i> </div>
            <div id="menu-btn" class="bi bi-bars" ><i class="bi bi-list"></i> </div>
        </div>
        <div class="account-box">
            <p>usuário: <span><?php echo $_SESSION['user_name']; ?></span></p>
            <p>email: <span><?php echo $_SESSION['user_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">Sair</a>
        </div>
    </div>
    
</header>