<?php
header('Content-type: application/json');
//header("Access-Control-Allow-Origin: https://ilergic.com/");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

if($_POST)
{
    $to_email="oi@reconsidere.com.br"; // Recipient email, Replace with own email here
    
    // check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        
        $output = json_encode(array( //create JSON data
        'type'=>'error',
        'text' => 'A requisição deve ser do tipo POST.'
        ));
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
        die($output); //exit script outputting json data
    }
    
    // Sanitize input data using PHP filter_var().
    $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST["message"], FILTER_SANITIZE_STRING);
    
    // email subject
    $subject = ' [Reconsidere - Enviado via formulário de contato]';
    
    // email body
    $message_body = $message."\r\n\r\n-".$name."\r\nE-mail : ".$email."\r\nTelefone: ".$phone;
    
    // uses wwordwrap() if lines of text are longer than 70 characters
    $message_body = wordwrap($message_body, 70);
    
    // Set enconding
    $encoded_name = '=?UTF-8?B?'.base64_encode($name).'?=';
    $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
    
    // proceed with PHP email.
    $headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n".
    'From: '.$encoded_name.'<'.$email.'>'."\r\n" .
    'Reply-To: '.$encoded_name.'<'.$email.'>' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    $send_mail = mail($to_email, $subject, $message_body, $headers);
    
    if(!$send_mail)
    {
        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        $output = json_encode(array('type'=>'error', 'text' => 'Não foi possível enviar a mensagem, tente novamente mais tarde.'));
        die($output);
    }else{
        $output = json_encode(array('type'=>'success', 'text' => 'Olá '.$name .', recebemos a sua mensagem, entraremos em contato o mais rápido possível.'));
        die($output);
    }
}

?>