<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 
require '/var/www/html/MyApi/vendor/autoload.php';
require 'includes/DbOperations.php';
$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);
 
$app->post('/createuser', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('email', 'password', 'name', 'lastname'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $email = $request_data['email'];
        $password = $request_data['password'];
        $name = $request_data['name'];
        $lastname = $request_data['lastname']; 
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $db = new DbOperations; 
        $result = $db->createUser($email, $hash_password, $name, $lastname);
        
        if($result == USER_CREATED){
            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'User created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == USER_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }else if($result == USER_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'User Already Exists';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});
//CreateAccount
$app->post('/createaccount', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('groupa', 'accountname','amount'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $groupa = $request_data['groupa'];
        $accountname = $request_data['accountname'];
        $amount = $request_data['amount'];
        
       
        $db = new DbOperations; 
        $result = $db->createaccount($groupa, $accountname, $amount);
        
        if($result == USER_CREATED){
            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Account created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == USER_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }else if($result == USER_AUTHENTICATED){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'account';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});



$app->post('/userlogin', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('email', 'password'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $email = $request_data['email'];
        $password = $request_data['password'];
        
        $db = new DbOperations; 
        $result = $db->userLogin($email, $password);
        if($result == USER_AUTHENTICATED){
            
            $user = $db->getUserByEmail($email);
            $response_data = array();
            $response_data['error']=false; 
            $response_data['message'] = 'Login Successful';
            $response_data['user']=$user; 
            $response->write(json_encode($response_data));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);    
        }else if($result == USER_NOT_FOUND){
            $response_data = array();
            $response_data['error']=true; 
            $response_data['message'] = 'User not exist';
            $response->write(json_encode($response_data));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(202);    
        }else if($result == USER_PASSWORD_DO_NOT_MATCH){
            $response_data = array();
            $response_data['error']=true; 
            $response_data['message'] = 'Invalid credential';
            $response->write(json_encode($response_data));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(203);  
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});
 
$app->get('/allusers', function(Request $request, Response $response){
    $db = new DbOperations; 
    $users = $db->getAllUsers();
    $response_data = array();
    $response_data['error'] = false; 
    $response_data['users'] = $users; 
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});
$app->get('/allaccount', function(Request $request, Response $response){
  
  $db = new DbOperations; 
    $account = $db->getAllAccount();
    $response_data = array();
    $response_data['error'] = false; 
    $response_data['account'] = $account; 
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});
 
 
$app->put('/updateuser/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    if(!haveEmptyParameters(array('email','name','lastname'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $email = $request_data['email'];
        $name = $request_data['name'];
        $lastname = $request_data['lastname']; 
     
        $db = new DbOperations; 
        if($db->updateUser($email, $name, $lastname, $id)){
            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'User Updated Successfully';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);  
        
        }else{
            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);  
              
        }
    }
    
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/* Create Transaction */


$app->post('/createtrans', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('account', 'category', 'amount', 'date'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $account = $request_data['account'];
        $category = $request_data['category'];
        $amount = $request_data['amount'];
        $date = $request_data['date'];
        $db = new DbOperations; 
        $result = $db->createTrans($account, $category, $amount, $date);
        
        if($result == USER_CREATED){
            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Trans created successfully';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        }else if($result == USER_FAILURE){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }else if($result == USER_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'User Already Exists';
            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});

/* Trans Ended */











/* get all transaction */
 
$app->get('/alltrans', function(Request $request, Response $response){
    $db = new DbOperations; 
    $users = $db->getAllTrans();
    $response_data = array();
    $response_data['error'] = false; 
    $response_data['users'] = $users; 
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});








/* Update Transaction  */

$app->put('/updatetrans/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    if(!haveEmptyParameters(array('account','category','amount','date'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $account = $request_data['account'];
        $category = $request_data['category'];
        $amount = $request_data['amount']; 
        $date = $request_data['date']; 
     
        $db = new DbOperations; 
        if($db->updateTrans($account, $category, $amount, $date, $id)){
            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'Trans Updated Successfully';
            $user = $db->getUserById($id);
            $response_data['user'] = $user; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);  
        
        }else{
            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';
            $user = $db->getUserById($id);
            $response_data['user'] = $user; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);  
              
        }
    }
    
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});






/* Update Account */

$app->put('/updateaccount/{accountid}', function(Request $request, Response $response, array $args){
    $accountid = $args['accountid'];

    if(!haveEmptyParameters(array('groupa','accountname','amount'), $request, $response)){
        $request_data = $request->getParsedBody(); 
        $groupa = $request_data['groupa'];
        $accountname = $request_data['accountname'];
        $amount = $request_data['amount']; 
     
        $db = new DbOperations; 
        if($db->updateAccount($groupa, $accountname, $amount, $accountid)){
            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'Account Updated  Successfully';
            $account = $db->getAccountById($accountid);
            $response_data['account'] = $account ; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
              
        
        }else{
            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';
            $account = $db->getAccountById($id);
            $response_data['account'] = $account; 
            $response->write(json_encode($response_data));
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(204);  
              
        }
    }
    
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});







/* Delete Transaction */

$app->delete('/deletetrans/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $db = new DbOperations; 
    $response_data = array();
    if($db->deleteTrans($id)){
        $response_data['error'] = false; 
        $response_data['message'] = 'Trans has been deleted';    
    }else{
        $response_data['error'] = true; 
        $response_data['message'] = 'Plase try again later';
    }
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);
});
 









$app->put('/updatepassword', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('currentpassword', 'newpassword', 'email'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 
        $currentpassword = $request_data['currentpassword'];
        $newpassword = $request_data['newpassword'];
        $email = $request_data['email']; 
        $db = new DbOperations; 
        $result = $db->updatePassword($currentpassword, $newpassword, $email);
        if($result == PASSWORD_CHANGED){
            $response_data = array(); 
            $response_data['error'] = false;
            $response_data['message'] = 'Password Changed';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }else if($result == PASSWORD_DO_NOT_MATCH){
            $response_data = array(); 
            $response_data['error'] = true;
            $response_data['message'] = 'You have given wrong password';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }else if($result == PASSWORD_NOT_CHANGED){
            $response_data = array(); 
            $response_data['error'] = true;
            $response_data['message'] = 'Some error occurred';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(201);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);  
});
 
$app->delete('/deleteuser/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $db = new DbOperations; 
    $response_data = array();
    if($db->deleteUser($id)){
        $response_data['error'] = false; 
        $response_data['message'] = 'User has been deleted';    
    }else{
        $response_data['error'] = true; 
        $response_data['message'] = 'Plase try again later';
    }
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);
});
/*Delate account*/
$app->delete('/deleteaccount/{accountid}', function(Request $request, Response $response, array $args){
    $accountid = $args['accountid'];
    $db = new DbOperations; 
    $response_data = array();
    if($db->deleteAccount($accountid)){
        $response_data['error'] = false; 
        $response_data['message'] = 'User has been deleted';    
    }else{
        $response_data['error'] = true; 
        $response_data['message'] = 'Plase try again later';
    }
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);
});
 
function haveEmptyParameters($required_params, $request, $response){
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 
    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }
    if($error){
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}
 
$app->run();
