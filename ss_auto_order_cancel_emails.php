<?php
/*
  Plugin Name: Auto Order Cancel Emails

  Plugin URI: https://github.com/syedasumaya

  Description: This is a plugin to sent email after automatically cancel an order in woocommerce

  Version: 1.0.0

  Author: Syeda Sumaya Yesmin

  Author URI: https://github.com/syedasumaya

  Text Domain: Ss_auto_order_cancel_emails

  License: https://github.com/syedasumaya

 */

if (!defined('ABSPATH')) {

    exit();

}

if (!class_exists('Ss_auto_order_cancel_emails')) {



    class Ss_auto_order_cancel_emails {



        public function __construct() {



            add_filter('woocommerce_email_classes', array($this, 'edit_woocommerce_email_classes'));
			
	        add_action( 'woocommerce_order_status_changed', array($this, 'ss_woocommerce_order_status_failed'),10, 3 );

        }





        public function edit_woocommerce_email_classes($email_classes) {

        
            require_once(plugin_dir_path(__FILE__) . 'inc/ss_email_auto_cancelled_order.php');
			
            $email_classes['Ss_email_auto_cancelled_order'] = new Ss_email_auto_cancelled_order();
			
            return $email_classes;

        }
		
		public function ss_woocommerce_order_status_failed( $order_id, $old_status, $new_status){ 

			if($new_status == 'cancelled') {
				 $mailer = WC()->mailer();
                                $mails = $mailer->get_emails();
                                                    if ( ! empty( $mails ) ) {

                         foreach ( $mails as $mail ) { 

                              if ( $mail->id == 'ss_auto_cancelled_order' ) {
                                      $mail ->trigger($order_id );

                               }

                          }

                     }

			
			}
		
		}



    }



}

new Ss_auto_order_cancel_emails();

