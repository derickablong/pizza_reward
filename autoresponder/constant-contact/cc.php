<?php
/**
 * Constant Contact
 *
 * @author Derick
 * @since  2018
 */

define('APIKEY', '88xavv6jxs6xjpyq9q4gyebn');
define('CONSUMER_SECRET', '2RG26v66DHh3fSgBh2HNt6cZ');
define('AUTH_URL', 'https://api.constantcontact.com/mashery/account/' . APIKEY);
define('LIST_ID', '1971612648');

 
require_once( RWDIR . 'autoresponder/constant-contact/sdk/Ctct/src/Ctct/autoload.php');
require_once( RWDIR . 'autoresponder/constant-contact/sdk/vendor/vendor/autoload.php');

use Ctct\Components\Contacts\Contact;
use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;


class RewardCCIntegration
{
    
    public $object;
    public $lists;
    public $tokens;
    public $message;
    public $ID;



    public function setup()
    {

        /**
         * CC Tokens
         */
        $this->tokens();


        /**
         * Save Tokens
         */
        $this->save_tokens();
        

    }


    public function save_tokens()
    {
        if ( isset( $_POST['cc-save-tokens'] ) ) {

            update_option('reward_cc_options', array(
                'ID' => $_POST['cc-id'],
                'tokens' => $_POST['cc-tokens']
            ));

            $this->message = array(
                'status' => 'success',
                'text' => '<strong>Access Tokens</strong> was successfully saved. Reward system can now add contacts to your <strong>Constant Contact</strong>.'
            );
        }
    }


    public function tokens()
    {
        if ( get_option('reward_cc_options') ) {
            
            $options = get_option('reward_cc_options');
            $this->ID = $options['ID'];
            $this->tokens = $options['tokens'];

        } else {

            add_option('reward_cc_options', array(
                'ID' => LIST_ID,
                'tokens' => ''
            ));        

            $this->ID = LIST_ID;
            $this->tokens = '';

        }
    }


    public function has_tokens()
    {
        if ( empty( $this->tokens ) )
            return false;
        return true;
    }


    public function get_tokens()
    {
        if (empty( $this->tokens )) {
            $options = get_option('reward_cc_options');
            $this->ID = $options['ID'];
            $this->tokens = $options['tokens'];
        }
        return $this->tokens;
    }


    public function get_ID()
    {        
        return $this->ID;
    }


    public function create_tokens()
    {
        $this->setup();
        include( RWDIR . 'autoresponder/constant-contact/form.php');
    }

    public function message()
    {
        if ( is_array( $this->message ) ) {
            ?>
            <div id="setting-error-settings_updated" class="update <?php echo $this->message['status'] ?> updated settings-error notice is-dismissible"> 
                <p><?php echo $this->message['text'] ?></p>
            </div>
            <?php
        }
    }

    public function add_contacts( $args = array() )
    {

        $options = get_option('reward_cc_options');
        $ID = $options['ID'];
        $tokens = $options['tokens'];
        

        try {
            



            $cc = new ConstantContact(APIKEY);
            $response = $cc->contactService->getContacts(
                $tokens, array("email" => $args['email'])
            );           


            if (count($response->results) <= 0) {           

                $contact = new Contact();
                $contact->addEmail($args['email']);
                $contact->addList($ID);
                $contact->first_name = $args['fname'];
                $contact->last_name = $args['lname'];
                
                $returnContact = $cc->contactService->addContact(
                    $tokens, 
                    $contact
                );              

                
            } 
            else {
               
                $contact = $response->results[0];
                if ($contact instanceof Contact) {
                   
                    $contact->addList($ID);
                    $contact->first_name = $args['fname'];
                    $contact->last_name = $args['lname'];
                   
                    $returnContact = $cc->contactService->updateContact(
                        $tokens, 
                        $contact
                    );

                } 
            }


           
        } catch (CtctException $ex) {           
            
        }







    }    

}