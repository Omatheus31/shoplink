<?php
// includes/email.php

// Carrega as classes do PHPMailer manualmente
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destinatario, $nome_destinatario, $assunto, $mensagemHTML) {
    $mail = new PHPMailer(true);

    try {
        // 1. Configurações do Servidor (SMTP do Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu_email@gmail.com'; // <--- COLOQUE SEU EMAIL AQUI
        $mail->Password   = 'SUA SENHA AQUI';     // <--- COLOQUE A SENHA DE 16 LETRAS AQUI
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Correção para XAMPP (Ignora verificação de certificado SSL local)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // 2. Remetente e Destinatário
        $mail->setFrom('seu_email@gmail.com', 'Shoplink - Suporte'); // <--- SEU EMAIL NOVAMENTE
        $mail->addAddress($destinatario, $nome_destinatario);

        // 3. Conteúdo do Email
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHTML;
        $mail->AltBody = strip_tags($mensagemHTML); // Versão texto puro para clientes antigos

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Para debug, você pode descomentar a linha abaixo:
        // echo "Erro no envio: {$mail->ErrorInfo}";
        return false;
    }
}
?>