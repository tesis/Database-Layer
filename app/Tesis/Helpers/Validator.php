<?php //app/Tesis/Helpers/Validator.php

namespace Core\Helpers;

class Validator {
    /**
     * validateEmail
     *
     * @param string $email
     *
     * @return bool
     *
    */
    public function validateEmail($email)
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new \Exception('Email is invalid');
        }
        return true;
    }
    /**
     * validatePassword check equality and hash the password
     *
     * @param string $password  first password, after comparison, hash it
     * @param string $password2 second password to compare
     *
     * @return string
     *
    */
    public function validatePassword($password, $password2)
    {
        if(empty($password) || empty($password2))
        {
            throw new \Exception('Arguments are missing');
        }

        if(strcmp($password, $password) != 0 ){
        {
            throw new \Exception('Password are not equal');
        }
        $this->rules('password', 3, 20);
        return password_hash($password, PASSWORD_DEFAULT);
    }
    /**
     * rules
     *
     * @param string $rule
     *
     * @return bool
     *
     * min:3
     * max:20
     * min:3|max:20
     * password:6:20
     *
     * //'regexp' => "/^([a-z0-9]){3,30}$/i"
     *
    */
    public function rules($rule='', $length='')
    {
        if(empty($rule))
        {
            throw new \Exception('Arguments are missing');
        }
        $range = '';
        if(!empty($length))
        {
            $length = explode(':', $length);
            if(sizeof($length) == 2)
            {
                //min and max
                $range = '{' . $min . ','. $max . '}';
            }
            else
            {
                //only min
                $range = '{' . $min . '}';
            }
        }

        //rule explode '|' or rule explode ':'
        //if not check switch
        switch($rule)
        {
            case 'onlyLetters':
                $options = array(
                    'options' => array(
                        'regexp' => "/^([a-z])" .$range. "$/i"
                    )
                );
                $flag = FILTER_VALIDATE_REGEXP;
                break;
            case 'onlyLettersNumbers':
                $options = array(
                    'options' => array(
                        'regexp' => "/^([a-z0-9])" .$range. "$/i"
                    )
                );
                $flag = FILTER_VALIDATE_REGEXP;
                break;
            case 'onlyNumbers':
                $options = array(
                    'options' => array(
                        'regexp' => "/^([0-9])" .$range. "$/i"
                    )
                );
                $flag = FILTER_VALIDATE_REGEXP;
                break;
                // Match password with 5-20 chars with letters and digits
                // Assert there is at least one letter, AND
                // Assert there is at least one upper-letter, AND
                // Assert there is at least one digit, AND
                // Assert the length is from 5 to 20 chars.
            case "password":
                if($range == '')
                {
                    $range = '{6,16}';
                }
                $regexp = '/^
                (?=.*?[a-z])
                (?=.*?[A-Z])
                (?=.*?[0-9])
                (?=.' . $range . '\z)
                /x';
                $options = array(
                    'options' => array(
                        'regexp' => $regexp
                    )
                );
                $flag = FILTER_VALIDATE_REGEXP;
                break;
        }
        return (!filter_var($rule, $flag, $options)) ? false : true;
    }

}
