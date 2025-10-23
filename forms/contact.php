<?php

  $receiving_email_address = 'gbeti7669@gmail.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $_POST['name'];
  $contact->from_email = $_POST['email'];
  $contact->subject = $_POST['subject'];

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  // Verify reCAPTCHA
  $recaptcha_secret = 'YOUR_SECRET_KEY'; // Replace with your actual reCAPTCHA secret key
  $recaptcha_response = $_POST['g-recaptcha-response'];

  if (empty($recaptcha_response)) {
    echo json_encode(['status' => 'error', 'message' => 'reCAPTCHA verification failed']);
    exit;
  }

  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array(
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response
  );

  $options = array(
    'http' => array(
      'header' => "Content-type: application/x-www-form-urlencoded\r\n",
      'method' => 'POST',
      'content' => http_build_query($data)
    )
  );

  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  $result_json = json_decode($result, true);

  if (!$result_json['success']) {
    echo json_encode(['status' => 'error', 'message' => 'reCAPTCHA verification failed']);
    exit;
  }

  $contact->add_message( $_POST['name'], 'From');
  $contact->add_message( $_POST['email'], 'Email');
  if(isset($_POST['phone'])) {
    $contact->add_message( $_POST['phone'], 'Phone');
  }
  $contact->add_message( $_POST['message'], 'Message', 10);

  echo $contact->send();
?>
