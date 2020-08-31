<?php
    require_once('vendor/autoload.php');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"), true);

    $card   = substr($data['card'], strlen($data['card'])-4, strlen($data['card']));
    $name   = $data['name'];
    $email  = $data['email'];
    $phone  = $data['phone'];
    $amount = $data['amount'];
    $ref    = $data['ref'];

    \Stripe\Stripe::setApiKey("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");


    try{
      $token = \Stripe\Token::create([
        'card' => [
          'number' => $data['card'],
          'exp_month' => $data['month'],
          'exp_year' => $data['year'],
          'cvc' => $data['cvc'],
        ],
      ]);
      $charge = \Stripe\Charge::create([
        //'amount' => ($data['amount']*100),
        'amount' => (9990*100),
        'currency' => 'mxn',
        'source' => $token,
      ]);

      sendData($card, $name, $email, $phone, $amount, $ref);
    }catch(Exception $e){
      $response = array('title' => 'rejected', 'message' => $e->getMessage());
      http_response_code(403);
      echo json_encode($response);
    }
    

    function sendData($card, $name, $email, $phone, $amount, $ref){
      //$url = "https://webhook.site/0d65736d-f81d-43f5-9e61-2267e35ad8f3";
      $url = "https://hook.integromat.com/r721dpl12yjfk54a2eqlm6kvkgxti2ea";
      $payload = json_encode(array('card' => $card, 'name' => $name, 'email' => $email, 'phone'  => $phone, 'amount' => $amount, 'ref'  => $ref));
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      $res = curl_exec($ch);
      curl_close($ch);
      $response = array('message' => 'completed');
      echo json_encode($response);
    }
?>