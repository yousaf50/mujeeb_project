<?php 
    /*
        Author: Belal Khan
        Post: PHP Rest API Example using SLIM
    */
 
    class DbOperations{
        //the database connection variable
        private $con; 
        public $emai;
 
        //inside constructor
        //we are getting the connection link
        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
 
 
        /*  The Create Operation 
            The function will insert a new user in our database
        */
        public function createUser($email, $password, $name, $lastname){
            $emai=$email;
           if(!$this->isEmailExist($email)){
                $stmt = $this->con->prepare("INSERT INTO users (email, password, name, lastname) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $email, $password, $name, $lastname);
                if($stmt->execute()){
                    return USER_CREATED; 
                }else{
                    return USER_FAILURE;
                }
           }
           return USER_EXISTS; 
        }
public function createAccount($groupa,$accountname,$amount)
        {
          
            $query = "INSERT INTO accounts($groupa, $accountname, $amount) VALUES (?,?,?)";
            $stmt1 = $this->con->prepare($query);
            
        $stmt=$this->con->prepare("INSERT INTO account(groupa,accountname,amount) VALUES (?,?,?)");
       
        $stmt->bind_param("sss", $groupa, $accountname, $amount);
        if($stmt->execute()){
            return USER_CREATED;
        }
          else{
              return USER_FAILURE;
          }
       
       
        }
        
        public function getAllAccount()
        {
            $stmt = $this->con->prepare("SELECT accountid,groupa,accountname,amount FROM account ");
           // $stmt->bind_parm($accountid);
            $stmt->execute(); 
            $stmt->bind_result($accountid,$groupa,$accountname,$amount);
            
            $stmt->fetch(); 
            $account = array(); 
            while($stmt->fetch())
{
    $accounts=array();
            $accounts['accountid']=$accountid;
            $accounts['groupa'] = $groupa; 
            $accounts['accountname']=$accountname;
            $accounts['amount']=$amount; 
            array_push($account,$accounts);
        }         
            return  $account;
        }
    


      
 
        
    
        /* 
            The Read Operation 
            The function will check if we have the user in database
            and the password matches with the given or not 
            to authenticate the user accordingly    
        */
        public function userLogin($email, $password){
            if($this->isEmailExist($email)){
                $hashed_password = $this->getUsersPasswordByEmail($email); 
                if(password_verify($password, $hashed_password)){
                    return USER_AUTHENTICATED;
                }else{
                    return USER_PASSWORD_DO_NOT_MATCH; 
                }
            }else{
                return USER_NOT_FOUND; 
            }
        }
 
        /*  
            The method is returning the password of a given user
            to verify the given password is correct or not
        */
        private function getUsersPasswordByEmail($email){
            $stmt = $this->con->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->bind_result($password);
            $stmt->fetch(); 
            return $password; 
        }
 
        /*
            The Read Operation
            Function is returning all the users from database
        */
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT id, email, name, lastname FROM users;");
            $stmt->execute(); 
            $stmt->bind_result($id, $email, $name, $lastname);
            $users = array(); 
            while($stmt->fetch()){ 
                $user = array(); 
                $user['id'] = $id; 
                $user['email']=$email; 
                $user['name'] = $name; 
                $user['lastname'] = $lastname; 
                array_push($users, $user);
            }             
            return $users; 
        }
 
        /*
            The Read Operation
            This function reads a specified user from database
        */
        public function getUserByEmail($email){
            $stmt = $this->con->prepare("SELECT id, email, name, lastname FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->bind_result($id, $email, $name, $lastname);
            $stmt->fetch(); 
            $user = array(); 
            $user['id'] = $id; 
            $user['email']=$email; 
            $user['name'] = $name; 
            $user['lastname'] = $lastname; 
            return $user; 
        }


        
    
 
 
        /*
            The Update Operation
            The function will update an existing user
            from the database 
        */
        public function updateUser($email, $name, $lastname, $id){
            $stmt = $this->con->prepare("UPDATE users SET email = ?, name = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("sssi", $email, $name, $lastname, $id);
            if($stmt->execute())
                return true; 
            return false; 
        }
       /*  The Create Transaaction 
            The function will insert a Transaction in our database
        */
        public function createTrans($account, $category, $amount,$date){
        
            $stmt = $this->con->prepare("INSERT INTO transaction (account, category, amount,date) VALUES ( ?,?, ?, ?)");
            $stmt->bind_param("ssss", $account, $category, $amount, $date);
            if($stmt->execute()){
                return TRANS_RECORDED; 
            }else{
                return TRANS_FAILURE;
            }
       
    }
    
    
    
    
    
    /*
       The Read Operation
       Function is returning all the users transaction from database
   */

   public function getAllTrans(){
       $stmt = $this->con->prepare("SELECT id, account, category, amount, date FROM transaction;");
       $stmt->execute(); 
       $stmt->bind_result($id, $account, $category, $amount, $date);
       $users = array(); 
       while($stmt->fetch()){ 
           $user = array(); 
           $user['id'] = $id; 
           $user['account']=$account; 
           $user['category'] = $category; 
           $user['amount'] = $amount;
           $user['date'] = $date; 
           array_push($users, $user);
       }             
       return $users; 
   }

   
   
   
   
   
   
   
    /*
       The Read Operation
       This function reads a specified User ID from database
   */

   public function getUserById($id){
       $stmt = $this->con->prepare("SELECT id, account, category, amount, date FROM transaction WHERE id = ?");
       $stmt->bind_param("i", $id);
       $stmt->execute(); 
       $stmt->bind_result($id, $account, $category, $amount, $date);
       $stmt->fetch(); 
       $user = array(); 
       $user['id'] = $id; 
       $user['account']=$account; 
       $user['category'] = $category; 
       $user['amount'] = $amount;
       $user['date'] = $date;
       return $user; 
   }








/*
            The Update Operation
            The function will update an existing Account
            from the database 
        */
        public function updateAccount($groupa, $accountname, $amount, $accountid){
            $stmt = $this->con->prepare("UPDATE account SET groupa = ?, accountname = ?, amount = ? WHERE accountid = ?");
            $stmt->bind_param("sssi", $groupa, $accountname, $amount, $accountid);
            if($stmt->execute())
                return true; 
            return false; 
        }


        /*
            The Read Operation
            This function reads a specified User Account from database
        */

        public function getAccountById($accountid){
            $stmt = $this->con->prepare("SELECT accountid, groupa, accountname, amount FROM account WHERE accountid = ?");
            $stmt->bind_param("i", $accountid);
            $stmt->execute(); 
            $stmt->bind_result($accountid, $groupa, $accountname, $amount);
            $stmt->fetch(); 
            $account = array(); 
            $account['accountid'] = $accountid; 
            $account['groupa']=$groupa; 
            $account['accountname'] = $accountname; 
            $account['amount']= $amount;
            return $account; 
        }











/*
       The Update Operation
       The function will update an existing Transaction
       from the database 
   */
   public function updateTrans($account, $category, $amount, $date, $id){
       $stmt = $this->con->prepare("UPDATE transaction SET account = ?, category = ?, amount = ?, date = ? WHERE id = ?");
       $stmt->bind_param("ssssi", $account, $category, $amount, $date, $id);
       if($stmt->execute())
           return true; 
       return false; 
   }









/*
       The Delete Operation
       This function will delete the Transaction from database
   */
   public function deleteTrans($id){
       $stmt = $this->con->prepare("DELETE FROM transaction WHERE id = ?");
       $stmt->bind_param("i", $id);
       if($stmt->execute())
           return true; 
       return false; 
   }
        /*
            The Update Operation
            This function will update the password for a specified user
        */
        public function updatePassword($currentpassword, $newpassword, $email){
            $hashed_password = $this->getUsersPasswordByEmail($email);
            
            if(password_verify($currentpassword, $hashed_password)){
                
                $hash_password = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $this->con->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss",$hash_password, $email);
                if($stmt->execute())
                    return PASSWORD_CHANGED;
                return PASSWORD_NOT_CHANGED;
            }else{
                return PASSWORD_DO_NOT_MATCH; 
            }
        }
 
        /*
            The Delete Operation
            This function will delete the user from database
        */
        public function deleteUser($id){
            $stmt = $this->con->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute())
                return true; 
            return false; 
        }
        /*delet account*/
        public function deleteAccount($accountid){
            $stmt = $this->con->prepare("DELETE FROM account WHERE accountid = ?");
            $stmt->bind_param("i", $accountid);
            if($stmt->execute())
                return true; 
            return false; 
        }
 
 
        /*
            The Read Operation
            The function is checking if the user exist in the database or not
        */
        private function isEmailExist($email){
            $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
        }
    }