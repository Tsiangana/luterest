<?php
include 'config.php';
session_start();

if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'. $image;

    $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('A busca falhou');

    if (mysqli_num_rows($select_product_name) > 0) {
        $message[] = 'Um produto com esse nome já existe';
    } else {
        $add_product_query = mysqli_query($conn, "INSERT INTO `products` (name, price, image) VALUES ('$name', '$price', '$image')") or die('Query failed');

        if ($add_product_query) {
            if ($image_size > 2000000) {
                $message[] = 'Imagem do produto é muito grande';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Produto adicionado com sucesso';
            }
        } else {
            $message[] = 'Não foi possível adicionar o produto';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('Query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/'.$fetch_delete_image['image']);
    mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('Query failed');
    header('location:admin_page.php');
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = $_POST['update_price'];

    mysqli_query($conn, "UPDATE `products` SET name = '$update_name', price = '$update_price' WHERE id = '$update_p_id'") or die('Query failed');

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/'.$update_image;
    $update_old_image = $_POST['update_old_image'];

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'A imagem inserida é muito grande, por favor escolha outra';
        } else {
            mysqli_query($conn, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") or die('Query failed');
            move_uploaded_file($update_image_tmp_name, $update_folder);
            unlink('uploaded_img/'.$update_old_image);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo @Luterest</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="bootstrap-icons/bootstrap-icons.css">

    <style>
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 1/1;
        }
    </style>
</head>
<body>

    <script src="jquery-3.6.1.slim.js"></script>


    <header>

        <h1 style="cursor: pointer;"><a href="index.php" style="text-decoration: none;color: #e60023;">Luterest</a></h1>

        <nav>
            <ul>
                <li><a href="#">Sobre</a></li>
            </ul>
            <ul>
                <li><a href="#">Negócios</a></li>
            </ul>
            <ul>
                <li><a href="#">Blogue</a></li>
            </ul>

            <div class="buttons">
                <a href="login.php" style="text-decoration: none;">
                    <button class="login" style="cursor: pointer;" id="login">Login</button>
                </a>
                <a href="cadastrar.php" style="text-decoration: none;">
                    <button class="signup" style="cursor: pointer;">Sign Up</button>
                </a>
            </div>
        </nav>

    </header>

    <div class="container-one">

        <form action="" method="get" style="align-items: center;justify-content: center;">
            <input type="text" placeholder="Pesquisar..." id="search-box" name="search_query">
            <button type="submit">Pesquisar</button>
        </form>

        <!-- show products start-->

        <section class="show-products">
            <?php
            if (isset($_GET['search_query'])) {
                $search_query = mysqli_real_escape_string($conn, $_GET['search_query']);
        
                $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_query%'") or die('Query failed');
        
                if (mysqli_num_rows($select_products) > 0) {
                    echo '<div class="box-container">';
        
                    while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                        echo '<div class="box">';
                        echo '<a href="inicio.php?update=' . $fetch_products['id'] . '">';
                        echo '<img src="uploaded_img/' . $fetch_products['image'] . '" alt="" data-search="' . $fetch_products['name'] . '" id="' . $fetch_products['id'] . '">';
                        echo '</a>';
                        echo '</div>';
                    }
        
                    echo '</div>';
                } else {
                    echo '<h1 class="empty" style="text-align: center;">Nenhuma imagen encontrada</h1>';
                }
            } else {
                echo '<section class="show-products">';
                echo '<div class="box-container">';
        
                $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed');
                if (mysqli_num_rows($select_products) > 0) {
                    while ($fetch_products = mysqli_fetch_assoc($select_products)) {
        
                        echo '<div class="box">';
                        echo '<a href="inicio.php?update=' . $fetch_products['id'] . '"><img src="uploaded_img/' . $fetch_products['image'] . '" alt="" data-search="' . $fetch_products['name'] . '" id="' . $fetch_products['id'] . '"></a>';
                        echo '</div>';
                    }
                } else {
                    echo '<h1 class="empty" style="text-align: center;">Ainda não foi adicionado nenhuma imagem</h1>';
                }
                echo '</div>';
                echo '</section>';
            }
            ?>

            
        </section>

        <section class="edit-product-form">
            <?php
            if (isset($_GET['update'])) {
                $update_id = $_GET['update'];
                $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('Query failed');
                if (mysqli_num_rows($update_query) > 0) {
                    while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                        ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="fechar">
                                <input type="reset" value="Fechar" id="close-update" class="option-btn" style="width: 80%;height: 30px; background: #e60023;cursor: pointer;color: #fff; border-radius: 25px;text-align: center;padding: 2px;">
                            </div>
                            <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                            <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                            <div class="img">
                                <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                                <div class="texts">
                                    <p style="text-align: left;"><?php echo $fetch_update['name']; ?></p>
                                    <br>
                                    <p style="text-align: left;font-size: 2rem;width: 70%;word-wrap: break-word;word-wrap: break-word;padding: 10px;"><?php echo $fetch_update['price']; ?></p>
                                    <br>
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                }
            } else {
                echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
            }
            ?>

        </section>

        <!-- show products end-->

        <script>
            document.querySelector('#close-update').onclick = () => {
                document.querySelector('.edit-product-form').style.display = 'none';
                window.location.href = 'inicio.php';
            }

            let images = document.querySelectorAll('.show-products .box-container .box img');

            document.querySelector('#search-box').oninput = () => {
                var value = document.querySelector('#search-box').value.toLowerCase();
                images.forEach(img => {
                    var dataSearch = img.getAttribute('data-search');
                    if (dataSearch.indexOf(value) > -1) {
                        img.style.display = 'inline-block';
                    } else {
                        img.style.display = 'none';
                    }
                });
            }
        </script>

    </div>

</body>
</html>
