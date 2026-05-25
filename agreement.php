<?php
/* =========================================================================
   AGREEMENT SYSTEM (agreement.php)
   Shortcode: [agreement_form]
   Features: Creation Form, Signature (Draw/Type), Password Protection
   ========================================================================= */

// 1. Register 'Agreement' Post Type
function register_SnakTap_agreement_cpt() {
    $args = array(
        'labels'      => array(
            'name'          => 'Agreements',
            'singular_name' => 'Agreement',
            'menu_name'     => 'Agreements'
        ),
        'public'      => true,
        'show_ui'     => true,
        'has_archive' => false,
        'supports'    => array('title', 'editor', 'custom-fields'),
        'menu_icon'   => 'dashicons-media-document',
        'rewrite'     => array('slug' => 'view-agreement'), 
    );
    register_post_type('agreement', $args);
}
add_action('init', 'register_SnakTap_agreement_cpt');

// 2. Shortcode to Display the CREATION Form
add_shortcode('agreement_form', 'render_agreement_form');

function render_agreement_form() {
    if (!is_user_logged_in()) {
        return '<p style="text-align:center; padding:50px; font-family:\'Inter\', sans-serif;">Please <a href="/wp-login.php">log in</a> to create an agreement.</p>';
    }

    $message = '';

    if (isset($_POST['submit_agreement_bttn'])) {
        
        // A. Sanitize Data
        $creator   = sanitize_text_field($_POST['created_by']);
        $merchant  = sanitize_text_field($_POST['merchant_name']);
        $email     = sanitize_email($_POST['merchant_email']);
        $phone     = sanitize_text_field($_POST['merchant_phone']);
        $status    = sanitize_text_field($_POST['agreement_status']);
        $deal      = sanitize_text_field($_POST['deal_type']);
        $cap       = sanitize_text_field($_POST['monthly_cap']);
        $loc_type  = sanitize_text_field($_POST['location_type']);
        $pos       = isset($_POST['pos_system']) ? sanitize_text_field($_POST['pos_system']) : '';
        
        $comm_rate = isset($_POST['commission_rate_val']) ? sanitize_text_field($_POST['commission_rate_val']) : '';
        $order_rate = isset($_POST['proposed_rate_val']) ? sanitize_text_field($_POST['proposed_rate_val']) : '';
        $flat_fee  = isset($_POST['monthly_fee_val']) ? sanitize_text_field($_POST['monthly_fee_val']) : '';

        // B. Generate Password & Title
        // $password = wp_generate_password(8, false); // --- PREVIOUS: Unique Password (Commented Out) ---
        $password = 'yourapp'; // --- NEW: Static Password ---
        
        $post_title = $merchant . ' - Agreement';

        // C. GENERATE AGREEMENT CONTENT
        $html_content = '
        <div style="font-family:\'Inter\', sans-serif; line-height:1.6; color:#333; max-width:1250px; margin:0 auto; width:100%; padding:20px; box-sizing:border-box;">
            <h2 style="text-align:center; margin-bottom:30px;">Merchant Terms and Conditions for the SnakTap Platform</h2>
            
            <p>SnakTap, Inc. (“SnakTap”, “us”, “our”) will provide <strong>'. $merchant .'</strong> (“you”, “your”, or “'. $merchant .'”) with a mobile application (also “Mobile App”) subject to the terms outlined in the following SnakTap Merchant Terms and Conditions (“Merchant Terms”).</p>

            <p><strong>1. Authorization of use:</strong> SnakTap authorizes your use of the Mobile App for the purpose of enabling '. $merchant .' customers (“users”) to initiate transactions on the “'. $merchant .'” Mobile App for iPhone and Android devices, and for the development, implementation, execution, tracking, and/or management of promotional and marketing campaigns executed through the '. $merchant .' Mobile App. By utilizing the SnakTap, Inc. software products (Mobile App, backend for viewing order details, customer information, etc.), and as an express condition of such use and access, you agree to comply with the following Merchant Terms that form a complete and binding agreement between '. $merchant .' and SnakTap, Inc.</p>

            <p><strong>2. Term:</strong> The term of this agreement is for 1 year, with an automatic renewal on a monthly basis with the same terms as outlined in this agreement, unless a new Merchant Agreement is agreed to. The continuation of using the Mobile App will be subject to mutually agreed terms. '. $merchant .' can determine which store locations to include in the Mobile App menu for customers to view and order from. Additional locations can be added by email request and are subject to the same terms of the master agreement. This agreement is at will and '. $merchant .'\'s may terminate this Agreement upon days notice through email/mail. SnakTap Inc. can terminate this Agreement upon days notice through email/mail. '. $merchant .' would be responsible for any monies owed as a result of the Services up to and including the last day on which the Services are provided. There are no additional fees associated with terminating the Agreement.</p>

            <p><strong>3. Intellectual Property:</strong> '. $merchant .' agrees and acknowledges that SnakTap, Inc. owns and retains ownership of all the proprietary computer code and programs (“Intellectual Property”) created and maintained by SnakTap, Inc. that powers the SnakTap platform (Mobile App, Merchant Dashboard). This includes all SnakTap related copyright, trademark, and SnakTap specific content. Under the terms of this agreement, upon termination of the Agreement, SnakTap, Inc. would not be able to transfer the '. $merchant .' Mobile App to '. $merchant .' for the purpose of continued use by '. $merchant .'. SnakTap, Inc. will remove the app from the Apple Appstore and Google Play Store within 7 or fewer days upon the termination of the Agreement.</p>

            <p><strong>4. Merchant Tablet:</strong> You authorize SnakTap to send orders to an installed device with the SnakTap Merchant app on it. Orders would need to be accepted/rejected from the Merchant App.</p>

            <p><strong>5. E-Commerce Transactions:</strong></p>
            <ul>
                <li>You shall not use the SnakTap platform for transactions in connection with any illegal activity, in violation of any federal, state, or local law, or in connection with any lottery or gambling activity. Tobacco and alcoholic products cannot be sold in the SnakTap platform. The SnakTap mobile app will promote your products at their price as determined from information available from your POS.</li>
                <li>No Charge-Back Guarantee; Limitations. To the extent that '. $merchant .' receives a chargeback from a user, '. $merchant .' will not seek reimbursement from SnakTap Inc. SnakTap will assist '. $merchant .' with obtaining any relevant information that can assist in fighting chargebacks from authorized transactions that a customer deems to be unauthorized.</li>
            </ul>

            <p><strong>6. SnakTap Licensing Fees, Payment Terms, and Taxes:</strong></p>
            <ul>
                <li><strong>SnakTap will not charge any fees for design, development, or ongoing management of the '. $merchant .' Mobile App.</strong></li>
                <li><strong>Licensing Fees:</strong>
                    <ul style="margin-top:5px; margin-bottom:5px;">
                        <li>'. $cap .', payable monthly.</li>
                        <li>Invoicing from SnakTap, Inc. to '. $merchant .': Invoicing cycle: Monthly If there is an outstanding balance.</li>
                    </ul>
                </li>
                <li>You agree that you are registered for sales and use tax collection purposes in all jurisdictions in which your goods and services will be provided in connection with your use of the SnakTap platform. Collected sales tax will pass through to your corporate bank account and '. $merchant .' will be responsible to pay all collected sales taxes (local and state). '. $merchant .' is responsible for all taxes related to purchases.</li>
                <li>You may opt to have SnakTap activate tipping through the app. 100% of tips would be sent to '. $merchant .' if turned on. In app tipping will be turned off on the '. $merchant .' Mobile App until '. $merchant .' informs SnakTap, Inc. of a change.</li>
                <li>SnakTap will send summary accounting reports in accordance with a schedule that follows the standard invoice cycle that is selected in this Agreement.</li>
            </ul>

            <p><strong>7. Data Reporting and Analytics:</strong> Certain data is collected on the SnakTap platform and will be provided to you upon request via electronic transmission during the term of this agreement. Data can include app download numbers, customer ordering statistics, promotion and loyalty program usage, and other sales and marketing metrics. Subject to the Merchant Terms and SnakTap’s Privacy Policy for user protection, SnakTap grants you a revocable license during the Term of this Agreement to export any user data provided to or made available to you as long as its use or transfer is within the scope of all privacy laws in the USA. Data can be sent as a spreadsheet file.</p>

            <p><strong>8. Granted Licenses:</strong> In order to facilitate marketing initiatives, you are granted, during the Term of this Agreement, a limited, revocable license to use SnakTap promotional materials including product images and trademarks. You also grant SnakTap a revocable license to use, copy, reproduce, modify, license, distribute, and publish any of your “Merchant Content,” that includes trademarks, service marks, logos, photographs, text, images or other content for publication as part of marketing or promotion of your use of the SnakTap platform. All marketing campaigns will need to be approved in advance by '. $merchant .'. '. $merchant .' represents and warrants that they have the right to provide the Merchant Content to us, and that the use, copying, modification, and publication of the Merchant Content by SnakTap and by SnakTap’s affiliates and advertising partners: (a) will not infringe, violate or misappropriate any third party copyright, patent, trade secret or other proprietary rights, (b) will not infringe any rights of publicity or privacy.</p>

            <p><strong>9. Rewards:</strong> All rewards on the '. $merchant .' Mobile App (built and managed by SnakTap, Inc.) are subject to your approval and can be amended at your discretion. At the user’s discretion, they can opt to utilize the rewards point function that will subtract rewards points on a 1-point equaling one-cent ($.01) basis from the pre-tax gross sales receipt of their transaction (100 points = $1).</p>
            <ul>
                <li>Initial “starter” reward: Promotion of $ dollars as as credit to be used towards the purchase of items in the Mobile App</li>
                <li>Ongoing: Reward dollars are added to a user account in the ratio of % per transaction, before taxes, excluding tips (if any)..</li>
            </ul>

            <p><strong>10. Merchant Representations and Warranties:</strong> You represent and warrant to SnakTap that you have the right, power and authority to enter into this agreement to be bound by these Merchant Terms. You agree that the terms will not conflict with or violate any agreements or instruments by which '. $merchant .' is bound, any applicable law, or any rights of any third party;</p>

            <p><strong>11. Disclaimer of Warranties and Indemnification.</strong> SnakTap will make reasonable efforts to provide the SnakTap platform and other services described in these Merchant Terms to '. $merchant .' in a workmanlike manner and in compliance with the applicable descriptions herein. Your sole and exclusive remedy, and SnakTap’s sole and exclusive liabilities, for maintaining the system shall be as follows: (a) if you notify SnakTap of any such issue with the Mobile App, we will take a best effort approach to resolving the issue within (7) days of its occurrence. We cannot warranty that the Mobile App will be bug free. SnakTap shall promptly remedy the error in accordance with the requirements of these Merchant Terms. SnakTap does not warrant or guarantee that the '. $merchant .' mobile application or related services will always operate error-free.<br>
            <br>Hold Harmless Agreement: '. $merchant .' shall hold harmless SnakTap, Inc, its’ officers and employees from any and all liabilities, claims, losses, costs, or expenses to the person or property of another, lawsuits, judgments, and/or expenses, including attorney fees, arising either directly or indirectly from any act or failure to act by or any of its officers or employees, which may occur during or which may arise out of the performance of this Agreement.</p>

            <p><strong>12.</strong> The laws of the Commonwealth of Massachusetts shall govern this Agreement.</p>
            
            <hr style="margin:30px 0; border:0; border-top:1px solid #eee;">
            <p style="font-size:12px; color:#666;">
                <strong>Deal Structure:</strong> '. $deal .' '. ($comm_rate ? "($comm_rate)" : "") .' '. ($order_rate ? "($order_rate/order)" : "") .' '. ($flat_fee ? "($$flat_fee/mo)" : "") .'<br>
                <strong>Location:</strong> '. $loc_type .' '. ($pos ? "($pos)" : "") .' <br>
                <strong>Date Generated:</strong> '. date('F j, Y') .' by '. $creator .'
            </p>
        </div>
        ';

        // D. Insert Post
        $post_id = wp_insert_post(array(
            'post_title'    => $post_title,
            'post_content'  => $html_content,
            'post_status'   => 'publish',
            'post_type'     => 'agreement',
            'post_password' => $password
        ));

        if ($post_id) {
            update_post_meta($post_id, 'merchant_email', $email);
            update_post_meta($post_id, 'agreement_status', $status);
            update_post_meta($post_id, 'created_by', $creator);

            // E. Send Emails
            $link = get_permalink($post_id);
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // Add Admins to CC
            $headers[] = 'Cc: aryan@SnakTap.com';
            $headers[] = 'Cc: amit.kumar@fenebrisindia.com';

            // Email to Merchant
            $msg  = "Hello $merchant,<br><br>";
            $msg .= "Please find a link to the SnakTap agreement. Let us know if you have any questions.<br>";
            $msg .= "We appreciate the opportunity to help and look forward to making this amazing for you and your brand.<br><br>";
            $msg .= "Upon clicking on the agreement link, you will be prompted to enter a password:<br><br>";
            $msg .= "<strong>Password:</strong> <span style='background:#eee; padding:5px; font-weight:bold;'>$password</span><br><br>";
            $msg .= "Click to view the AGREEMENT: <a href='$link'>View</a><br><br>";
            
            // New Footer
            $msg .= "Thank you. Be well<br><br>";
            $msg .= "<br><br>Sincerely,<br>";
            $msg .= "<strong>Aryan Mamtora</strong><br>";
            $msg .= "<strong>Co-Founder</strong><br><br>";
            $msg .= "SnakTap<br>";
            $msg .= "📞 +1 559 238 1999<br>";
            $msg .= "✉️ Aryan@SnakTap.com<br>";
            $msg .= "🌐 www.SnakTap.com<br><br>";
            $msg .= "<a href='https://outlook.office.com/bookwithme/user/37ff8aac496d4317a930771549d28f0e@SnakTap.com?anonymous&ep=signature'>Schedule A Call</a>";
            
            wp_mail($email, "Agreement: $merchant", $msg, $headers);

            // Email to Admin
            $admin_email = get_option('admin_email');
            $admin_msg = "New Agreement created by $creator.<br>Merchant: $merchant<br>Link: $link<br>Pass: $password";
            // Note: Sending this without the CC headers to avoid double emailing admins if they are also the main admin
            $admin_headers = array('Content-Type: text/html; charset=UTF-8'); 
            wp_mail($admin_email, "New Agreement Generated", $admin_msg, $admin_headers);

            $message = '<div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:5px; text-align:center; font-family:\'Inter\';">✅ Agreement Created & Email Sent!</div>';
        }
    }

    ob_start();
    echo $message;
    ?>
    <style>
        .crm-wrap { font-family:'Inter', sans-serif; background:#fff; max-width:850px; margin:40px auto; padding:50px; border:1px solid #e0e0e0; box-shadow:0 5px 20px rgba(0,0,0,0.03); }
        .crm-title { font-size:26px; font-weight:700; margin-bottom:10px; color:#111; }
        .crm-link-box { margin-bottom: 40px; }
        .crm-link-box a { color: #007cba; text-decoration: none; font-weight: 500; font-size: 14px; }
        .crm-link-box a:hover { text-decoration: underline; }
        .crm-row { display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-bottom:25px; }
        .crm-full { grid-column:1 / -1; margin-bottom:25px; }
        .crm-label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .crm-input, .crm-select { width:100%; padding:12px; border:1px solid #ccc; border-radius:4px; font-size:14px; box-sizing:border-box; background: #fff; height: 45px; }
        .crm-input:focus, .crm-select:focus { border-color:#000; outline:none; }
        .conditional-box { background:#f8f9fa; border-left:4px solid #000; padding:20px; margin-top:15px; margin-bottom:20px; display:none; }
        .sub-btn { background:#000; color:#fff; width:100%; padding:16px; border:none; font-size:16px; font-weight:700; cursor:pointer; margin-top:30px; border-radius:4px; transition: background 0.2s; }
        .sub-btn:hover { background:#333; }
        .radio-group label { margin-right: 20px; font-weight: 500; cursor: pointer; display:inline-block; margin-bottom:5px; }
    </style>

    <div class="crm-wrap">
        <h2 class="crm-title">Create Agreement</h2>
        <div class="crm-link-box"><a href="/crm/">&larr; Go to CRM</a></div>
        
        <form method="post" id="agreementForm">
            <div class="crm-full">
                <label class="crm-label">Created By *</label>
                <div class="radio-group" style="margin-top:10px;">
                    <label><input type="radio" name="created_by" value="Aryan" required> Aryan</label>
                    <label><input type="radio" name="created_by" value="Shyam"> Shyam</label>
                    <label><input type="radio" name="created_by" value="Sapna"> Sapna</label>
                    <label><input type="radio" name="created_by" value="Amit"> Amit</label>
                </div>
            </div>
            <div class="crm-full"><label class="crm-label">Merchant Name *</label><input type="text" name="merchant_name" class="crm-input" required></div>
            <div class="crm-full"><label class="crm-label">Merchant Phone *</label><input type="text" name="merchant_phone" class="crm-input" placeholder="(555)-555-5555" required></div>
            <div class="crm-full"><label class="crm-label">Merchant Email *</label><input type="email" name="merchant_email" class="crm-input" required></div>

            <div class="crm-full">
                <label class="crm-label">Agreement Status *</label>
                <select name="agreement_status" class="crm-select" required>
                    <option value="Agreement Sent Initially">1. Agreement Sent Initially</option>
                    <option value="Agreement sent/not signed">2. Agreement sent / not signed yet</option>
                    <option value="Commited">3. Commited / Not Signed yet</option>
                </select>
            </div>

            <div style="height:1px; background:#eee; margin:30px 0;"></div>

            <div class="crm-full">
                <label class="crm-label">Deal Type Proposed *</label>
                <div class="radio-group" style="margin-top:10px;">
                    <label><input type="radio" name="deal_type" value="Commission" onclick="toggleDeal(1)" required> Commission</label><br>
                    <label><input type="radio" name="deal_type" value="Daily_1" onclick="toggleDeal(0)"> $4-$10 Pricing (Daily)</label><br>
                    <label><input type="radio" name="deal_type" value="Flat" onclick="toggleDeal(3)"> Flat Monthly</label><br>
                    <label><input type="radio" name="deal_type" value="Daily_2" onclick="toggleDeal(2)"> Per Order</label>
                </div>
                
                <div id="box_comm" class="conditional-box"><label class="crm-label">Commission Rate</label><input type="text" name="commission_rate_val" class="crm-input" value="6%"></div>
                <div id="box_daily" class="conditional-box"><label class="crm-label">Per Order Fee</label><input type="text" name="proposed_rate_val" class="crm-input" value=".25"></div>
                <div id="box_flat" class="conditional-box"><label class="crm-label">Monthly Fee</label>
                    <select name="monthly_fee_val" class="crm-select">
                        <option value="299.99">$299.99</option><option value="399.99">$399.99</option><option value="499.99">$499.99</option>
                    </select>
                </div>
            </div>

            <div class="crm-full">
                <label class="crm-label">Monthly Cap *</label>
                <select name="monthly_cap" class="crm-select" required>
                    <option value="">Select Cap Option</option>
                    <?php 
                    $rates = [5, 6, 7]; $prices = [249.99, 299.99, 349.99];
                    foreach($rates as $r) { foreach($prices as $p) { echo "<option value='{$r}%, capped at \${$p}/month'>{$r}%, capped at \${$p}/month, per location</option>"; } }
                    ?>
                </select>
            </div>

            <div class="crm-full">
                <label class="crm-label">Location Type *</label>
                <select name="location_type" id="locType" class="crm-select" onchange="togglePos()" required>
                    <option value="Freestanding">Freestanding</option>
                    <option value="POS Integrated">POS Integrated</option>
                </select>
                <div id="posBox" class="conditional-box">
                    <label class="crm-label">POS System *</label>
                    <select name="pos_system" class="crm-select">
                        <option value="Square">Square</option><option value="Toast">Toast</option><option value="Clover">Clover</option><option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="crm-full" style="margin-top:20px;">
                <label style="font-weight:700; cursor:pointer; color:#007cba; display:flex; align-items:center;">
                    <input type="checkbox" name="send_sms" value="yes" style="width:20px; height:20px; margin-right:10px;">
                    Send Confirmation Via Text Message Also
                </label>
            </div>

            <button type="submit" name="submit_agreement_bttn" class="sub-btn">Submit</button>
        </form>
    </div>

    <script>
    function toggleDeal(type) {
        document.getElementById('box_comm').style.display = (type===1) ? 'block' : 'none';
        document.getElementById('box_daily').style.display = (type===2) ? 'block' : 'none';
        document.getElementById('box_flat').style.display = (type===3) ? 'block' : 'none';
    }
    function togglePos() {
        var loc = document.getElementById('locType').value;
        document.getElementById('posBox').style.display = (loc === 'POS Integrated') ? 'block' : 'none';
    }
    
    // Auto-format Phone Number
    document.querySelector('input[name="merchant_phone"]').addEventListener('input', function (e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ')-' + x[2] + (x[3] ? '-' + x[3] : '');
    });
    </script>
    <?php
    return ob_get_clean();
}

// 3. CUSTOMIZE PASSWORD FORM (Hides Theme Header & Matches Screenshot)
add_filter( 'the_password_form', 'SnakTap_custom_password_form' );
function SnakTap_custom_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    
    $output = '
    <style>
        /* 1. HIDE THEME HEADER (Title & Meta) on Agreement Pages */
        .single-agreement .entry-header,
        .single-agreement .post-meta, 
        .single-agreement .ast-single-post-order {
            display: none !important;
        }

        /* 2. STYLE THE PASSWORD FORM (Matches Screenshot) */
        .pw-form-container { 
            font-family: sans-serif; 
            max-width: 600px; 
            margin: 80px auto; /* More top margin since header is gone */
            padding: 20px; 
            text-align: left; 
        }
        .pw-form-title { 
            font-size: 24px; 
            margin-bottom: 20px; 
            font-weight: 400; 
            color: #333; 
        }
        .pw-text { 
            margin-bottom: 20px; 
            font-size: 14px; 
            color: #333; 
        }
        .pw-label {
            margin-right: 5px;
            font-size: 14px;
            color: #333;
        }
        .pw-input { 
            border: 1px solid #7e8993; 
            padding: 2px 5px; 
            width: 180px; 
            height: 30px; 
            margin-right: 5px; 
            border-radius: 0;
        }
        .pw-submit { 
            background: grey; 
            color: #000000;
			margin-top: 15px;
        }
        .pw-submit:hover { 
            background: #eee; 
            border-color: #999;
        }
        .pass-input-pass {
            width: 200px !important;
        }
    </style>
    
    <div class="sign-wrapper-outer">
        <h2 class="pw-form-title">Protected: Agreement Form</h2>
        <p class="pw-text">This content is password-protected. To view it, please enter the password below.</p>
        
        <form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
            <span class="pw-label">Password:</span>
            <input name="post_password" id="' . $label . '" type="password" class="pw-input pass-input-pass" />
            <input type="submit" name="Submit" class="pw-submit" value="Enter" />
        </form>
    </div>
    ';
    return $output;
}


// 4. SIGNATURE LOGIC (Inject form into Content)
add_filter('the_content', 'append_signature_section');
function append_signature_section($content) {
    // Only show on Agreement posts, only if password entered
    if (!is_singular('agreement') || post_password_required()) {
        return $content;
    }

    // Check if already signed
    $signed_date = get_post_meta(get_the_ID(), 'signed_date', true);
    if($signed_date) {
        $sig_img = get_post_meta(get_the_ID(), 'signature_image', true);
        $sig_html = '';
        if($sig_img && strpos($sig_img, 'data:image') === 0) {
            $sig_html = '<div style="margin-top:10px;"><img src="'.$sig_img.'" style="max-height:80px; border-bottom:1px solid #ccc;"></div>';
        } else {
            $sig_html = '<div style="font-family:\'Dancing Script\', cursive; font-size:24px; border-bottom:1px solid #ccc; display:inline-block; padding:0 20px;">'.$sig_img.'</div>';
        }

        $box = '
        <div style="background:#f0f9eb; border:1px solid #c3e6cb; color:#155724; padding:20px; margin-top:40px; border-radius:4px; font-family:sans-serif;">
            <h3 style="margin:0 0 10px 0;">✅ Agreement Signed</h3>
            <p style="margin:0;">This document was signed on <strong>'.$signed_date.'</strong>.</p>
            '.$sig_html.'
        </div>';
        return $content . $box;
    }

    // Load Font for "Type" Signature
    $font_import = '<style>@import url("https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500&display=swap"); 
    .sig-btn { background:none; border:none; cursor:pointer; padding:5px; opacity:0.6; }
    .sig-btn.active { opacity:1; color:#007cba; }
    .sig-canvas { border:1px solid #ccc; width:100%; height:150px; background:#fff; cursor:crosshair; touch-action: none; }
    .sig-input-type { width:100%; border:none; border-bottom:1px solid #ccc; font-family:"Dancing Script", cursive; font-size:32px; padding:10px; outline:none; background:transparent; }
    .sig-label { font-weight:700; display:block; margin-bottom:8px; font-size:14px; color:#444; }
    /* Updated Container Styles */
    .sign-wrapper-outer { max-width:1250px; margin:0 auto; width:100%; padding:0 20px; box-sizing:border-box; }
    .sign-wrap { max-width:600px; margin:50px 0; background:#fafafa; padding:30px; border-radius:8px; border:1px solid #eee; width:100%; box-sizing:border-box; }
    @media (max-width: 480px) { .sign-wrap { padding: 20px; } }
    </style>';

    // Build Form HTML
    ob_start();
    ?>
    <div class="sign-wrapper-outer">
        <div class="sign-wrap" id="signArea">
            <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">Acceptance</h3>
            <p style="font-size:13px; color:#666;">By signing below, you agree to the terms outlined above.</p>
            
            <form method="post" id="signForm">
                <input type="hidden" name="agreement_id" value="<?php echo get_the_ID(); ?>">
                
                <div style="margin-bottom:20px;">
                    <label class="sig-label">Name *</label>
                    <input type="text" name="signer_name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                </div>

                <div style="margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                        <label class="sig-label" style="margin:0;">Signature *</label>
                        <div>
                            <button type="button" class="sig-btn active" id="btnDraw" onclick="setSigMode('draw')">Draw ✏️</button>
                            <button type="button" class="sig-btn" id="btnType" onclick="setSigMode('type')">Type ⌨️</button>
                            <button type="button" style="font-size:11px; color:red; border:none; background:none; cursor:pointer; margin-left:10px;" onclick="clearSig()">Clear</button>
                        </div>
                    </div>

                    <div style="background:#fff; border:1px solid #ddd; padding:10px; position:relative;">
                        <div id="drawContainer">
                            <canvas id="sigCanvas" width="500" height="150" class="sig-canvas"></canvas>
                            <input type="hidden" name="sig_data_draw" id="sigDataDraw">
                        </div>
                        
                        <div id="typeContainer" style="display:none; padding:40px 10px;">
                            <input type="text" name="sig_data_type" class="sig-input-type" placeholder="Type your name here...">
                        </div>
                        
                        <input type="hidden" name="sig_mode" id="sigMode" value="draw">
                    </div>
                </div>

                <div style="margin-bottom:25px;">
                    <label class="sig-label">Date</label>
                    <input type="text" id="dateField" disabled style="background:#f9f9f9; border:1px solid #eee; padding:10px; width:100%; color:#555;">
                    <input type="hidden" name="sign_date" id="dateHidden">
                </div>

                <button type="submit" name="submit_signature" class="sub-btn" onclick="return prepareSubmit()">Submit Agreement</button>
            </form>
        </div>
    </div>

    <script>
    // 1. Auto Date
    document.addEventListener("DOMContentLoaded", function() {
        var d = new Date();
        var dateStr = d.toLocaleDateString();
        document.getElementById('dateField').value = dateStr;
        document.getElementById('dateHidden').value = dateStr;
        initCanvas();
    });

    // 2. Toggle Mode
    function setSigMode(mode) {
        document.getElementById('sigMode').value = mode;
        if(mode === 'draw') {
            document.getElementById('drawContainer').style.display = 'block';
            document.getElementById('typeContainer').style.display = 'none';
            document.getElementById('btnDraw').classList.add('active');
            document.getElementById('btnType').classList.remove('active');
        } else {
            document.getElementById('drawContainer').style.display = 'none';
            document.getElementById('typeContainer').style.display = 'block';
            document.getElementById('btnDraw').classList.remove('active');
            document.getElementById('btnType').classList.add('active');
        }
    }

    // 3. Canvas Logic
    var canvas, ctx, isDrawing = false;
    function initCanvas() {
        canvas = document.getElementById('sigCanvas');
        if(!canvas) return;
        ctx = canvas.getContext('2d');
        ctx.strokeStyle = "#000";
        ctx.lineWidth = 2;

        // Resize
        var rect = canvas.parentNode.getBoundingClientRect();
        canvas.width = rect.width;

        // Mouse Events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseout', stopDraw);

        // Touch Events
        canvas.addEventListener('touchstart', function(e){ startDraw(e.touches[0]); e.preventDefault(); }, {passive: false});
        canvas.addEventListener('touchmove', function(e){ draw(e.touches[0]); e.preventDefault(); }, {passive: false});
        canvas.addEventListener('touchend', stopDraw);
    }

    function startDraw(e) {
        isDrawing = true;
        var rect = canvas.getBoundingClientRect();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }
    function draw(e) {
        if(!isDrawing) return;
        var rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
    }
    function stopDraw() { isDrawing = false; }
    
    function clearSig() {
        if(document.getElementById('sigMode').value === 'draw') {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        } else {
            document.querySelector('.sig-input-type').value = '';
        }
    }

    function prepareSubmit() {
        var mode = document.getElementById('sigMode').value;
        if(mode === 'draw') {
            // Check if canvas is empty (simplified check)
            // Save data
            document.getElementById('sigDataDraw').value = canvas.toDataURL();
        }
        return true;
    }
    </script>
    <?php
    $form_html = ob_get_clean();
    return $content . $font_import . $form_html;
}

// 5. HANDLE SUBMISSION
add_action('init', 'handle_agreement_signature_post');
function handle_agreement_signature_post() {
    if(isset($_POST['submit_signature']) && isset($_POST['agreement_id'])) {
        
        $post_id = intval($_POST['agreement_id']);
        $name    = sanitize_text_field($_POST['signer_name']);
        $date    = sanitize_text_field($_POST['sign_date']);
        $mode    = $_POST['sig_mode'];
        
        $final_sig = '';
        if($mode === 'draw') {
            $final_sig = $_POST['sig_data_draw']; // Data URL
        } else {
            $final_sig = sanitize_text_field($_POST['sig_data_type']); // Text Name
        }

        // Save Meta
        update_post_meta($post_id, 'agreement_signed', true);
        update_post_meta($post_id, 'signed_date', $date);
        update_post_meta($post_id, 'signer_name', $name);
        update_post_meta($post_id, 'signature_image', $final_sig); 
        
        // Update Status to "Signed"
        update_post_meta($post_id, 'agreement_status', 'Signed');

        // --- EMAIL LOGIC UPDATED ---
        
        // 1. Define Recipients (Same as your CRM)
        $admin_emails = array( 
            'aryan@SnakTap.com', 
            'amit.kumar@fenebrisindia.com' 
        );

        // 2. Prepare Subject & Message
        $subject = "Agreement Signed: " . get_the_title($post_id);
        $link = get_permalink($post_id); 
        
        $msg  = "Hello Admin,<br><br>";
        $msg .= "The agreement for <strong>" . get_the_title($post_id) . "</strong> has been signed by the merchant.<br><br>";
        $msg .= "<strong>Signer Name:</strong> $name<br>";
        $msg .= "<strong>Date Signed:</strong> $date<br><br>";
        $msg .= "You can view the signed agreement here (requires password): <br><a href='$link'>View</a>";
        
        // 3. Set Headers (Important for delivery)
        $domain = parse_url(get_site_url(), PHP_URL_HOST);
        $sender_email = 'wordpress@' . $domain; // Default sender
        
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: SnakTap Agreements <' . $sender_email . '>';

        // 4. Send Email
        wp_mail($admin_emails, $subject, $msg, $headers);

        // Redirect to same page to show Success Message
        wp_redirect(get_permalink($post_id));
        exit;
    }
}
?>