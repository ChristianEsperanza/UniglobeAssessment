<?php

/**
 * The approach to the open ended restriction "Don't show their SSN" can be interpreted in a few ways, 
 * but primarily:
 *      1. Do not display the SSN at all (IE - Leave it blank).
 *      2. Encrypt the SSN and display a hashed version of it. 
 * The second interpretation seems to be the most realistic and the route I decided to go with. I also 
 * chose to put it in a separate file to represent an extra level of decoupling for security reasons when dealing 
 * with sensitive data such as an SSN.
 */


 /**
  * Function to represent SSN encryption by creating a hash representation for it.
  * A more secure way to handle this would be to encrypt the SSN upon entry, and anyone that wants to view 
  * the decrypted data would need a special key. 
  */
function encryptSSN($userSSN) 
{
    return password_hash($userSSN, PASSWORD_DEFAULT);
}

?>