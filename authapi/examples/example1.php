<?php
session_start();
require_once("../AuthCore.php");

$core = new AuthCore();
$user = new User($core);
if (isset($_GET['reg'])) {
    if (isset($_POST['email']) && isset($_POST['pass1']) && isset($_POST['pass2']) && isset($_POST['name']) && isset($_POST['surname']) && !empty($_POST['email'])) {
        if ($_POST['pass1'] == $_POST['pass2']) {
            $email = mysql_real_escape_string($_POST['email']);
            $pass = mysql_real_escape_string($_POST['pass1']);
            $name = mysql_real_escape_string($_POST['name']);
            $surname = mysql_real_escape_string($_POST['surname']);

            if (($res = $core->registerUser($email, $pass, $name, $surname)) == 0) {
                echo "Se ha enviado un email para verificar la cuenta!";
            } else {
                if ($res == -1) {
                    echo "Error al enviar el mensaje de verificación";
                } else {
                    echo "Este email ya existe!";
                }
            }
        } else {
            echo "Error al crear la cuenta!";
        }
    } else {
        printReg();
    }
} else {
    if (isset($_GET['logout'])) {
        unset($_SESSION['user_token']);
        unset($_SESSION['user_email']);
    }
    if (isset($_GET['login'])) {
        if (isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])) {
            $token = $core->getTokenByPass($_POST['email'], $_POST['password']); //get token
            if (!empty($token)) {
                $_SESSION['user_token'] = $token;
                $_SESSION['user_email'] = $_POST['email'];
                $GLOBALS['login_result'] = 1;
                //header("Location: /chat");
                //return;
                echo "Login correcto!<br>";
            } else {

                echo "Error al conectarse!";
            }
        } else {
            echo "Error al conectarse!";
        }
    }

    if (isset($_SESSION['user_token']) && isset($_SESSION['user_email'])) {
        if ($user->login($_SESSION['user_email'], $_SESSION['user_token'])) {
            if (!$user->isVerificated()) {
                if (isset($_GET['code'])) {
                    if ($user->verify($_GET['code'])) {
                        echo "Cuenta Verificada <a href=\"?\">Ver mi perfil</a>";
                    }
                } else {
                    echo "Verifique su cuenta!";
                    echo "<form action=\"?\" method=\"GET\">
				        
        Código recibido en el EMail: <input name=\"code\" /> <input type=\"submit\" value=\"verificar\" />
                                                </form>";
                }
            } else {
                echo "<a href=\"?logout\">Cerrar Sesion</a><br>";
                echo "Hola " . $user->getName() . "!<br> Que tal?";
            }
        } else {

            unset($_SESSION['user_token']);
        }
    }

    if (!isset($_SESSION['user_token'])) {
        echo "<form action=\"?login\" method=\"post\">
			Email: <input type=\"text\" name=\"email\" /><br>
			Pass: <input type=\"password\" name=\"password\" />
			<input type=\"submit\" value=\"Conectar\" />
<br>
<a href=\"?reg\">Registrarse</a>		

";
    }
}

function printReg() {
    ?>
    <script>
        function final_check()
        {
            if (check_password())
            {
                if (check_email())
                {
                    //document.getElementById("singup_form").submit();
                    return true;
                }
                else
                {
                    alert("Revise el Correo electronico");
                }
            }
            else
            {
                alert("Revise la contraseña");
            }
            return false;
        }
        function check_email()
        {
            var email = document.getElementById("singup_form").email.value;
            var patt1 = new RegExp("[a-z.-_]@[a-z]*[.][a-z]{2}", "g");
            if (patt1.test(email))
            {
                document.getElementById("email_result").className = "control-group";
                return true;
            }
            else
            {
                document.getElementById("email_result").className = "control-group error";
                return false;
            }
        }
        function check_password()
        {
            var pass = document.getElementById("singup_form").pass1.value;
            if (pass.length > 7)
            {
                document.getElementById("pass1_result").className = "alert alert-info";
                var streng = 0;
                //solo letras minusculas
                var patt1 = new RegExp("[a-z]");
                if (patt1.test(pass))
                    streng++;
                var patt1 = new RegExp("[A-Z]");
                if (patt1.test(pass))
                    streng++;
                patt1 = new RegExp("[0-9]");
                if (patt1.test(pass))
                    streng++;
                patt1 = new RegExp("[^a-z^A-Z^0-9]");
                if (patt1.test(pass))
                    streng++;
                document.getElementById("pass1_result").innerHTML = "Fortaleza: " + streng + " de 4";
                if (pass == document.getElementById("singup_form").pass2.value)
                    return true;
                else
                    return false;
            }
            else
            {
                document.getElementById("pass1_result").className = "alert alert-error";
                document.getElementById("pass1_result").style.display = "inline";
                document.getElementById("pass1_result").innerHTML = "La contraseña deve de ser mayor de 7 caracteres";
                return false;
            }
        }
    </script>
    <form action="?reg" method="post" id="singup_form" charset="utf-8" onsubmit="return final_check()">

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span5">
                    <!--Sidebar content-->

                    <fieldset>
                        <legend>Informacion personal</legend>
                        <p>
                            <label for="name">Nombre</label><input type="text" name="name" id="name"  />
                        </p>
                        <p>
                            <label for="surname">Apellidos</label><input type="text" name="surname" id="surname" />
                        </p>

                    </fieldset>
                </div>
                <div class="span5">
                    <!--Body content-->

                    <fieldset>
                        <legend>Datos de acceso</legend>
                        <p>
                            <label for="email">Direccion de Email</label>
                        <div class="control-group" id="email_result">
                            <?php
                            if ($error & 1) {
                                ?>
                                <div class="alert alert-error">
                                    <button class="close" data-dismiss="alert">×</button>
                                    Este email ya existe
                                </div>
                                <?php
                                }
                                ?>
                                <input type="text" name="email" onkeyup="check_email()" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />

                            </div>
                            </p>
                            <p>
                                <?php
                                if ($error & 2) {
                                    ?>
                    <div class="alert alert-error">
                        <button class="close" data-dismiss="alert">×</button>
                        Las contraseñas no coinciden!
                    </div>
                    <?php
                    }
                    ?>
                    <label for="pass1">Contraseña</label><input type="password" name="pass1" id="pass1" onkeyup="check_password()" />
                    <div class="alert alert-info" id="pass1_result" style="display:none">

                    </div>
                    </p>
                    <p>
                        <label for="pass2">Repetir</label><input type="password" name="pass2" id="pass2" />
                    </p>
                </fieldset>
            </div>
        </div>
    </div>
    <input class="btn btn-primary" onclick="" type="submit" name="signin" value="Crear Cuenta" />
    <input class="btn btn-danger" type="reset" name="signin" value="Limpiar" />
</form>
<?php
}