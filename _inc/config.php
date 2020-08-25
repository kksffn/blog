<?php

// show all errors
//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//error_reporting(-1);


// require stuff
if( !session_id() ) @session_start();
require_once 'vendor/autoload.php';

//constants & settings

define( 'BASE_URL', 'http://localhost/blog');
define('APP_PATH', realpath(__DIR__.'/../')); //pathinfo(__DIR__, PATHINFO_DIRNAME)
define('IMAGE_PATH', APP_PATH . "/assets/img/post");
define('IMAGE_PATH_LOCALHOST', BASE_URL. "/assets/img/post");
define('MAX_IMAGE_SIZE', 2*1024*1024); //in bytes



// configurations
$config = [

    'use_email_activation' => true,

    'db' => [
        'type' => 'mysql',
        'name' => 'blog',
        'server'        => 'localhost',
        'username'      => 'root',
        'password'      => 'root',
        'charset'       => 'utf8'
    ],

];

//communication with DB (PDO)
$db = new PDO(
    "{$config['db']['type']}:host={$config['db']['server']};dbname={$config['db']['name']};
        charset={$config['db']['charset']}",
    $config['db']['username'], $config['db']['password']);

//Co má PDO dělat při SQL chybě: (ERRMODE_SILENT by se nestalo nic, WARNING by vyhodilo varování)
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//PDO před posláním query do db query připraví, což má význam jen u starších verzí mysql (do verze 4?),
// proto nastavíme false (novější mysql server si umí připravit query sám):
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


//PHPAuth
require_once("vendor/phpauth/phpauth/Config.php");
require_once("vendor/phpauth/phpauth/Auth.php");
//require_once("vendor/phpauth/phpauth/languages/cs_CZ.php");


// global functions
require_once 'functions-general.php';
require_once 'functions-string.php';
require_once 'functions-post.php';
require_once 'functions-auth.php';
require_once 'functions-user.php';
require_once 'functions-image.php';
require_once 'functions-tag.php';
require_once 'functions-comments.php';


$auth_config = new PHPAuth\Config($db, '', '', 'cs_CZ');
$auth   = new PHPAuth\Auth($db, $auth_config, "cs_CZ");

// PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//disabled - set in DB!
//// Instantiation and passing `true` enables exceptions
//$mail = new PHPMailer(true);
//
//try {
//    //Server settings
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
//    $mail->isSMTP();                                            // Send using SMTP
//    $mail->Host       = 'smtp1.example.com';                    // Set the SMTP server to send through
//    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
//    $mail->Username   = 'user@example.com';                     // SMTP username
//    $mail->Password   = 'secret';                               // SMTP password
//    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
//    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
//
//    //Recipients
//    $mail->setFrom('from@example.com', 'Mailer');
//    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
//    $mail->addAddress('ellen@example.com');               // Name is optional
//    $mail->addReplyTo('info@example.com', 'Information');
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('bcc@example.com');
//
//    // Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
//
//    // Content
//    $mail->isHTML(true);                                  // Set email format to HTML
//    $mail->Subject = 'Here is the subject';
//    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
//    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
//
//    $mail->send();
//    echo 'Message has been sent';
//} catch (Exception $e) {
//    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
//}


