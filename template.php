<?php
require_once './fragment/require-user.php';

error_reporting(E_ALL);
if (!haveLogin()) {
    header("Location: login.php");
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <?php require_once "fragment/head.php"; ?>
        </head>

        <?php require_once "fragment/menu.php"; ?>

        <div class="container">

            Web code

            <?php require_once "fragment/footer.php"; ?>

        </div>

        <?php require_once "fragment/scripts.php"; ?>

    </body>
    </html>

    <?php
}
?>