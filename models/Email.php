<?php
/* TODO: librerias necesarias para que el proyecto pueda enviar emails */
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class Email extends PHPMailer {

    protected $gCorreo;
    protected $gContrasena;

    public function __construct() {
        parent::__construct();

        // Cargar el archivo .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        // Asignar las variables de entorno a las propiedades de la clase
        $this->gCorreo = $_ENV['GMAIL_USER']; // Obtiene el correo desde $_ENV
        $this->gContrasena = $_ENV['GMAIL_PASS']; // Obtiene la contraseña desde $_ENV
    }

    private function configureSMTP() {
        $this->IsSMTP();
        $this->Host = $_ENV['SMTP_HOST']; // Obtiene el host SMTP desde $_ENV
        $this->Port = $_ENV['SMTP_PORT']; // Obtiene el puerto SMTP desde $_ENV
        $this->SMTPAuth = true;
        $this->SMTPSecure = $_ENV['SMTP_SECURE']; // Obtiene el tipo de seguridad SMTP desde $_ENV
        $this->Username = $this->gCorreo;
        $this->Password = $this->gContrasena;
        $this->CharSet = 'UTF-8';
        $this->setFrom($this->gCorreo, 'Sistema de Tickets');
    }

    /* TODO: Alertar al momento de generar un ticket */
    public function ticket_abierto($tick_id) {
        $this->configureSMTP(); // Configura SMTP antes de enviar el correo

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $this->addAddress($correo);
        $this->IsHTML(true);
        $this->Subject = "Ticket Abierto";

        $cuerpo = file_get_contents('../public/NuevoTicket.html'); // Ruta del template en formato HTML
        // Reemplazar parámetros en el template
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Ticket Abierto");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /* TODO: Alertar al momento de Cerrar un ticket */
    public function ticket_cerrado($tick_id) {
        $this->configureSMTP(); // Configura SMTP antes de enviar el correo

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]);

        $this->addAddress($correo);

        if ($datos[0]["usu_asig"] != "") {
            $this->addAddress($datos2[0]["usu_correo"]);
        }

        $this->WordWrap = 50;
        $this->IsHTML(true);
        $this->Subject = "Ticket Cerrado";

        $cuerpo = file_get_contents('../public/CerradoTicket.html'); // Ruta del template en formato HTML
        // Reemplazar parámetros en el template
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Ticket Cerrado");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /* TODO: Alertar al momento de Asignar un ticket */
    public function ticket_asignado($tick_id) {
        $this->configureSMTP(); // Configura SMTP antes de enviar el correo

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]);

        $this->addAddress($correo);
        $this->addAddress($datos2[0]["usu_correo"]);
        $this->WordWrap = 50;
        $this->IsHTML(true);
        $this->Subject = "Ticket Asignado";

        $cuerpo = file_get_contents('../public/AsignarTicket.html'); // Ruta del template en formato HTML
        // Reemplazar parámetros en el template
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Ticket Asignado");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /* TODO: Alertar al momento de Comentar un ticket */
    public function ticket_comentario($tick_id) {
        $this->configureSMTP(); // Configura SMTP antes de enviar el correo

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]);

        $this->addAddress($correo);

        if ($datos[0]["usu_asig"] != "") {
            $this->addAddress($datos2[0]["usu_correo"]);
        }

        $this->WordWrap = 50;
        $this->IsHTML(true);
        $this->Subject = "Ticket Comentario";

        $cuerpo = file_get_contents('../public/ComentarioTicket.html'); // Ruta del template en formato HTML
        // Reemplazar parámetros en el template
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Ticket Comentario");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /* TODO: Enviar correo para recuperar contraseña */
    public function recuperar_contrasena($usu_correo) {
        $this->configureSMTP(); // Configura SMTP antes de enviar el correo

        $usuario = new Usuario();
        $usuario->get_cambiar_contra_recuperar($usu_correo);

        $datos = $usuario->get_usuario_x_correo($usu_correo);
        foreach ($datos as $row) {
            $usu_id = $row["usu_id"];
            $usu_ape = $row["usu_ape"];
            $usu_nom = $row["usu_nom"];
            $correo = $row["usu_correo"];
            $usu_pass = $row["usu_pass"];
        }

        $this->addAddress($correo);
        $this->IsHTML(true);
        $this->Subject = "Recuperar Contraseña";

        $cuerpo = file_get_contents('../public/RecuperarContra.html'); // Ruta del template en formato HTML
        // Reemplazar parámetros en el template
        $cuerpo = str_replace("xusunom", $usu_nom, $cuerpo);
        $cuerpo = str_replace("xusuape", $usu_ape, $cuerpo);
        $cuerpo = str_replace("xnuevopass", $usu_pass, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Recuperar Contraseña");

        try {
            $this->Send();
            $usuario->encriptar_nueva_contra($usu_id, $usu_pass);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
