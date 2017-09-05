<?php



if (!class_exists('Ss_email_auto_cancelled_order') && class_exists('WC_Email')) {



    class Ss_email_auto_cancelled_order extends WC_Email {



        public function __construct() {



            // set ID, this simply needs to be a unique name

            $this->id = 'ss_auto_cancelled_order';

            $this->customer_email   = true;



            // this is the title in WooCommerce Email settings

            $this->title = 'Auto Cancelled Order';



            // this is the description in WooCommerce email settings

            $this->description = 'Auto Cancelled Order Notification';



            // these are the default heading and subject lines that can be overridden using the settings

            $this->heading = 'Unpaid order cancelled';

            $this->subject = 'Unpaid order cancelled';



            // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar

            $this->template_html    = 'emails/customer-auto-cancelled-order.php';

            $this->template_plain   = 'emails/plain/customer-auto-cancelled-order.php';


            add_action( 'woocommerce_order_status_pending_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constructor to load any other defaults not explicity defined here

            parent::__construct();



        }



        /**

         * Determine if the email should actually be sent and setup email merge variables

         *

         * @since 0.1

         * @param int $order_id

         */

        public function trigger($order_id) {

            // bail if no order ID is present

            if (!$order_id)

                return;


            if ($order_id) {

                $this->object = wc_get_order($order_id);

                $this->recipient = $this->object->billing_email;


                $this->find['order-date'] = '{order_date}';

                $this->find['order-number'] = '{order_number}';



                $this->replace['order-date'] = date_i18n(wc_date_format(), strtotime($this->object->order_date));

                $this->replace['order-number'] = $this->object->get_order_number();

            }


            if (!$this->is_enabled() || !$this->recipient)

                return;

            // woohoo, send the email!
            $this->send($this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());



        }



        /**

         * get_content_html function.

         *

         * @since 0.1

         * @return string

         */

        public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false,
			'email'			=> $this,
		) );
	}




        /**

         * get_content_plain function.

         *

         * @since 0.1

         * @return string

         */

        public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true,
			'email'			=> $this,
		) );
	}



        /**

         * Initialize Settings Form Fields

         *

         * @since 2.0

         */

        public function init_form_fields() {



            $this->form_fields = array(

                'enabled' => array(

                    'title' => 'Enable/Disable',

                    'type' => 'checkbox',

                    'label' => 'Enable this email notification',

                    'default' => 'yes'

                ),



                'subject' => array(

                    'title' => 'Subject',

                    'type' => 'text',

                    'description' => sprintf('This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject),

                    'placeholder' => '',

                    'default' => ''

                ),

                'heading' => array(

                    'title' => 'Email Heading',

                    'type' => 'text',

                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.'), $this->heading),

                    'placeholder' => '',

                    'default' => ''

                ),

                'email_type' => array(

                    'title' => 'Email type',

                    'type' => 'select',

                    'description' => 'Choose which format of email to send.',

                    'default' => 'html',

                    'class' => 'email_type',

                    'options' => array(

                        'plain' => __('Plain text', 'woocommerce'),

                        'html' => __('HTML', 'woocommerce'),

                        'multipart' => __('Multipart', 'woocommerce'),

                    )

                )

            );

        }



    }



}
