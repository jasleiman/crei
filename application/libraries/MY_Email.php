<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
 * CodeIgniter compatible email-library powered by PHPMailer.
 * Version: 1.1.27
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2012-2016.
 * @license The MIT License (MIT), http://opensource.org/licenses/MIT
 * @link https://github.com/ivantcholakov/codeigniter-phpmailer
 *
 * This library is intended to be compatible with CI 2.x and CI 3.x.
 *
 * Tested on production sites with CodeIgniter 3.0.4+ (February 14, 2016) and
 * PHPMailer Version 5.2.14+ (February 14, 2016).
 */

class MY_Email extends CI_Email {

    public $phpmailer;  // This property has been made public for testing purposes.

    protected $mailer_engine = 'codeigniter';
    protected $CI;

    protected $_is_ci_3 = NULL;

    protected static $protocols = array('mail', 'sendmail', 'smtp');
    protected static $mailtypes = array('html', 'text');
    protected static $encodings_ci = array('8bit', '7bit');
    protected static $encodings_phpmailer = array('8bit', '7bit', 'binary', 'base64', 'quoted-printable');

    protected $priority_raw = 3;
    protected $_encoding_raw = '8bit';
    protected $smtp_debug_raw = 0;

    public function __construct($config = array()) {

        $this->_is_ci_3 = (bool) ((int) CI_VERSION >= 3);

        $this->CI = get_instance();
        $this->CI->load->helper('email');
        $this->CI->load->helper('html');

        if (!is_array($config)) {
            $config = array();
        }

        $this->_safe_mode = (!is_php('5.4') && ini_get('safe_mode'));

        if (!isset($config['charset'])) {
            $config['charset'] = config_item('charset');
        }

        $this->initialize($config);

        log_message('info', 'MY_Email Class Initialized (Engine: '.$this->mailer_engine.')');
    }


    // Initialization & Clearing -----------------------------------------------

    /**
     * Define these options (if they are to contain non-default values)
     * within the $config array or within the configuration file email.php:
     *
     * useragent
     * protocol
     * mailpath
     * smtp_host
     * smtp_user
     * smtp_pass
     * smtp_port
     * smtp_timeout
     * smtp_crypto
     * wordwrap
     * wrapchars
     * mailtype
     * charset
     * validate
     * priority
     * crlf
     * newline
     * bcc_batch_mode
     * bcc_batch_size
     * encoding
     */
    public function initialize($config = array()) {

        if (!is_array($config)) {
            $config = array();
        }

        if (!isset($config['useragent'])) {
            $config['useragent'] = $this->useragent;
        }

        $this->set_useragent($config['useragent']);
        unset($config['useragent']);

        if (!isset($config['mailer_engine'])) {
            $config['mailer_engine'] = null;
        }

        $this->set_mailer_engine($config['mailer_engine']);
        unset($config['mailer_engine']);

        foreach ($config as $key => $value) {
            $this->_set_config_option($key, $value);
        }

        $this->_set_config_option('_smtp_auth', !($this->smtp_user == '' && $this->smtp_pass == ''));

        $this->clear();

        return $this;
    }

    public function clear($clear_attachments = false) {

        $clear_attachments = !empty($clear_attachments);

        parent::clear($clear_attachments);

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->clearAllRecipients();
            $this->phpmailer->clearReplyTos();
            if ($clear_attachments) {
                $this->phpmailer->clearAttachments();
            }

            $this->phpmailer->clearCustomHeaders();

            $this->phpmailer->Subject = '';
            $this->phpmailer->Body = '';
            $this->phpmailer->AltBody = '';
        }

        return $this;
    }


    // Prepare & Send a Message ------------------------------------------------

    public function from($from, $name = '', $return_path = NULL) {

        $from = (string) $from;
        $name = (string) $name;
        $return_path = (string) $return_path;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $from, $match)) {
                $from = $match['1'];
            }

            if ($this->validate) {

                $this->validate_email($this->_str_to_array($from));

                if ($return_path) {
                    $this->validate_email($this->_str_to_array($return_path));
                }
            }

            $this->phpmailer->setFrom($from, $name, 0);

            if (!$return_path) {
                $return_path = $from;
            }

            $this->phpmailer->Sender = $return_path;

        } else {

            if ($this->_is_ci_3) {
                parent::from($from, $name, $return_path);
            } else {
                parent::from($from, $name);
            }
        }

        return $this;
    }

    public function reply_to($replyto, $name = '') {

        $replyto = (string) $replyto;
        $name = (string) $name;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $replyto, $match)) {
                $replyto = $match['1'];
            }

            if ($this->validate) {
                $this->validate_email($this->_str_to_array($replyto));
            }

            if ($name == '') {
                $name = $replyto;
            }

            $this->phpmailer->addReplyTo($replyto, $name);

            $this->_replyto_flag = TRUE;

        } else {

            parent::reply_to($replyto, $name);
        }

        return $this;
    }

    public function to($to) {

        if ($this->mailer_engine == 'phpmailer') {

            $to = $this->_str_to_array($to);
            $names = $this->_extract_name($to);
            $to = $this->clean_email($to);

            if ($this->validate) {
                $this->validate_email($to);
            }

            reset($names);

            foreach ($to as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addAddress($address, $name);
            }

        } else {

            parent::to($to);
        }

        return $this;
    }

    public function cc($cc) {

        if ($this->mailer_engine == 'phpmailer') {

            $cc = $this->_str_to_array($cc);
            $names = $this->_extract_name($cc);
            $cc = $this->clean_email($cc);

            if ($this->validate) {
                $this->validate_email($cc);
            }

            reset($names);

            foreach ($cc as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addCC($address, $name);
            }

        } else {

            parent::cc($cc);
        }

        return $this;
    }

    public function bcc($bcc, $limit = '') {

        if ($this->mailer_engine == 'phpmailer') {

            $bcc = $this->_str_to_array($bcc);
            $names = $this->_extract_name($bcc);
            $bcc = $this->clean_email($bcc);

            if ($this->validate) {
                $this->validate_email($bcc);
            }

            reset($names);

            foreach ($bcc as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addBCC($address, $name);
            }

        } else {

            parent::bcc($bcc, $limit);
        }

        return $this;
    }

    public function subject($subject) {

        $subject = (string) $subject;

        if ($this->mailer_engine == 'phpmailer') {

            // Modified by Ivan Tcholakov, 01-AUG-2015.
            // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
            // This change probably is not needed, done anyway.
            //$this->phpmailer->Subject = $subject;
            $this->phpmailer->Subject = str_replace(array('{unwrap}', '{/unwrap}'), '', $subject);
            //

        } else {

            parent::subject($subject);
        }

        return $this;
    }

    public function message($body) {

        $body = (string) $body;

        if ($this->mailer_engine == 'phpmailer') {

            // Modified by Ivan Tcholakov, 01-AUG-2015.
            // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
            //$this->phpmailer->Body = $body;
            $this->phpmailer->Body = str_replace(array('{unwrap}', '{/unwrap}'), '', $body);
            //
        }

        parent::message($body);

        return $this;
    }

    // Modified by Ivan Tcholakov, 16-JAN-2014.
    //public function attach($file, $disposition = '', $newname = NULL, $mime = '') {
    public function attach($file, $disposition = '', $newname = NULL, $mime = '', $embedded_image = false) {
    //

        $file = (string) $file;

        $disposition = (string) $disposition;

        if ($disposition == '') {
            $disposition ='attachment';
        }

        $newname = (string) $newname;

        if ($newname == '') {
            // For making strict NULL checks happy.
            $newname = NULL;
        }

        $mime = (string) $mime;

        if ($this->mailer_engine == 'phpmailer') {

            if ($mime == '') {

                if (strpos($file, '://') === FALSE && ! file_exists($file)) {

                    $this->_set_error_message('lang:email_attachment_missing', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                if (!$fp = @fopen($file, FOPEN_READ)) {

                    $this->_set_error_message('lang:email_attachment_unreadable', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                $file_content = stream_get_contents($fp);
                $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
                fclose($fp);

                $this->_attachments[] = array(
                    'name' => array($file, $newname),
                    'disposition' => $disposition,
                    'type' => $mime,
                );

                $newname = $newname === NULL ? basename($file) : $newname;
                $cid = $this->attachment_cid($file);

            } else {

                // A buffered file, in this case make sure that $newname has been set.

                $file_content =& $file;

                $this->_attachments[] = array(
                    'name' => array($newname, $newname),
                    'disposition' => $disposition,
                    'type' => $mime,
                );

                $cid = $this->attachment_cid($newname);
            }

            if (empty($embedded_image)) {
                $this->phpmailer->addStringAttachment($file_content, $newname, 'base64', $mime, $disposition);
            } else {
                $this->phpmailer->addStringEmbeddedImage($file_content, $cid, $newname, 'base64', $mime, $disposition);
            }

        } else {

            if ($this->_is_ci_3) {
                parent::attach($file, $disposition, $newname, $mime);
            } else {
                parent::attach($file, $disposition);
            }
        }

        return $this;
    }

    public function attachment_cid($filename) {

        if ($this->mailer_engine == 'phpmailer') {

            for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

                if ($this->_attachments[$i]['name'][0] === $filename) {

                    $this->_attachments[$i]['cid'] = uniqid(basename($this->_attachments[$i]['name'][0]).'@');
                    return $this->_attachments[$i]['cid'];
                }
            }

        } elseif ($this->_is_ci_3) {

            return parent::attachment_cid($filename);
        }

        return FALSE;
    }

    // Added by Ivan Tcholakov, 16-JAN-2014.
    public function get_attachment_cid($filename) {

        for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

            if ($this->_attachments[$i]['name'][0] === $filename) {
                return empty($this->_attachments[$i]['cid']) ? FALSE : $this->_attachments[$i]['cid'];
            }
        }

        return FALSE;
    }
    //

    public function set_header($header, $value) {

        $header = (string) $header;
        $value = (string) $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->addCustomHeader($header, str_replace(array("\n", "\r"), '', $value));
        }

        parent::set_header($header, $value);

        return $this;
    }

    public function send($auto_clear = true) {

        $auto_clear = !empty($auto_clear);

        if ($this->mailer_engine == 'phpmailer') {

            if ($this->mailtype == 'html') {

                // Modified by Ivan Tcholakov, 01-AUG-2015.
                // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
                //$this->phpmailer->AltBody = $this->_get_alt_message();
                $this->phpmailer->AltBody = str_replace(array('{unwrap}', '{/unwrap}'), '', $this->_get_alt_message());
                //
            }

            $result = (bool) $this->phpmailer->send();

            if ($result) {

                $this->_set_error_message('lang:email_sent', $this->_get_protocol());

                if ($auto_clear) {
                    $this->clear();
                }

            } else {

                $this->_set_error_message($this->phpmailer->ErrorInfo);
            }

        } else {

            if ($this->_is_ci_3) {
                $result = parent::send($auto_clear);
            } else {
                $result = parent::send();
            }
        }

        return $result;
    }


    // Methods for setting configuration options -------------------------------

    // Avoid using the configuration setting methods directly. Use the initialize()
    // method for customizing the configuration options as it is usual for CodeIgniter.
    // Also, use the configuration file email.php for customizing the default
    // configuration options.

    public function set_useragent($useragent) {

        if ($useragent !== null) {

            $this->useragent = $useragent;
            $this->set_mailer_engine($useragent);
        }

        return $this;
    }

    public function set_mailer_engine($mailer_engine) {

        if ($mailer_engine !== null) {

            $mailer_engine = strtolower(trim($mailer_engine));

            if (strpos($mailer_engine, 'phpmailer') !== false) {
                $this->mailer_engine = 'phpmailer';
            } else {
                $this->mailer_engine = 'codeigniter';
            }

            if ($this->mailer_engine == 'phpmailer') {

                if (!is_object($this->phpmailer)) {

                    // If your system uses class autoloading feature,
                    // then the following require statement would not be needed.
                    if (!class_exists('PHPMailer', false)) {
                        require_once APPPATH.'third_party/phpmailer/PHPMailerAutoload.php';
                    }
                    //

                    $this->phpmailer = new PHPMailer();
                    $this->phpmailer->PluginDir = APPPATH.'third_party/phpmailer/';
                }

                // Refresh PHPMailer options.

                $options = array(
                    'charset' => $this->charset,
                    'protocol' => $this->protocol,
                    'mailpath' => $this->mailpath,
                    'smtp_host' => $this->smtp_host,
                    'smtp_user' => $this->smtp_user,
                    'smtp_pass' => $this->smtp_pass,
                    'smtp_port' => $this->smtp_port,
                    'smtp_timeout' => $this->smtp_timeout,
                    'smtp_crypto' => $this->smtp_crypto,
                    'smtp_debug' => $this->smtp_debug_raw,
                    'wordwrap' => $this->wordwrap,
                    'wrapchars' => $this->wrapchars,
                    'mailtype' => $this->mailtype,
                    'priority' => $this->priority_raw,
                    'encoding' => $this->_encoding_raw,
                    '_smtp_auth' => $this->_smtp_auth,
                );

                foreach ($options as $key => $value) {
                    $this->_set_config_option($key, $value);
                }
            }

            $this->clear(true);
        }

        return $this;
    }

    public function set_protocol($protocol = 'mail') {

        $protocol = trim(strtolower($protocol));

        $this->protocol = in_array($protocol, self::$protocols) ? $protocol : 'mail';

        if ($this->mailer_engine == 'phpmailer') {

            switch ($this->protocol) {

                case 'mail':
                    $this->phpmailer->isMail();
                    break;

                case 'sendmail':
                    $this->phpmailer->isSendmail();
                    break;

                case 'smtp':
                    $this->phpmailer->isSMTP();
                    break;
            }
        }

        return $this;
    }

    public function set_smtp_crypto($smtp_crypto = '') {

        $smtp_crypto = trim(strtolower($smtp_crypto));

        if ($smtp_crypto != 'tls' && $smtp_crypto != 'ssl') {
            $smtp_crypto = '';
        }

        $this->smtp_crypto = $smtp_crypto;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPSecure = $smtp_crypto;
        }

        return $this;
    }

    public function set_wordwrap($wordwrap = TRUE) {

        $this->wordwrap = !empty($wordwrap);

        if (!$this->wordwrap) {

            if ($this->mailer_engine == 'phpmailer') {
                $this->phpmailer->WordWrap = 0;
            }
        }

        return $this;
    }

    public function set_wrapchars($wrapchars) {

        if ($this->_is_ci_3) {
            $wrapchars = (int) $wrapchars;
        }

        $this->wrapchars = $wrapchars;

        if ($this->mailer_engine == 'phpmailer') {

            if (!$this->wordwrap) {

                $this->phpmailer->WordWrap = 0;

            } else {

                if (empty($wrapchars)) {
                    $wrapchars = 76;
                }

                $this->phpmailer->WordWrap = $wrapchars;
            }
        }

        return $this;
    }

    public function set_mailtype($type = 'text') {

        $type = trim(strtolower($type));

        $this->mailtype = in_array($type, self::$mailtypes) ? $type : 'text';

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->isHTML($this->mailtype == 'html');
        }

        return $this;
    }

    public function set_charset($charset) {

        if ($charset !== null) {

            $charset = strtoupper($charset);

            $this->charset = $charset;

            if ($this->mailer_engine == 'phpmailer') {
                $this->phpmailer->CharSet = $charset;
            }
        }

        return $this;
    }

    public function set_priority($n = 3) {

	$this->priority_raw = $n;

        if ($this->mailer_engine == 'phpmailer') {

            $this->priority = preg_match('/^[1-5]$/', $n) ? (int) $n : null;
            $this->phpmailer->Priority = $this->priority;

        } else {

            $this->priority = preg_match('/^[1-5]$/', $n) ? (int) $n : 3;
        }

        return $this;
    }

    // Setting explicitly the body encoding.
    // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/3
    public function set_encoding($encoding) {

        $this->_encoding_raw = $encoding;

        if ($this->mailer_engine == 'phpmailer') {

            if (!in_array($encoding, self::$encodings_phpmailer)) {
                $encoding = '8bit';
            }

            $this->phpmailer->Encoding = $encoding;

        } elseif (!in_array($encoding, self::$encodings_ci)) {

            $encoding = '8bit';
        }

        $this->_encoding = $encoding;

        return $this;
    }

    // PHPMailer's SMTP debug info level
    // 0 = off, 1 = commands, 2 = commands and data, 3 = as 2 plus connection status, 4 = low level data output.
    public function set_smtp_debug($level) {

        $this->smtp_debug_raw = $level;

        $level = (int) $level;

        if ($level < 0) {
            $level = 0;
        }

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPDebug = $level;
        }

        return $this;
    }


    // Overridden public methods -----------------------------------------------

    public function valid_email($email) {

        return valid_email($email);
    }


    // Custom public methods ---------------------------------------------------

    public function full_html($subject, $message) {

        $full_html =
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset='.strtolower($this->charset).'" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>'.htmlspecialchars($subject, ENT_QUOTES, $this->charset).'</title>

    <style type="text/css">
		
        /* See http://htmlemailboilerplate.com/ */

        /* Based on The MailChimp Reset INLINE: Yes. */
        /* Client-specific Styles */
        #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
        body {
            width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:40px;
            font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 16px;
        }
        /* End reset */

        /* Some sensible defaults for images
        Bring inline: Yes. */
        img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
        a img {border:none;}

        /* Yahoo paragraph fix
        Bring inline: Yes. */
        p {margin: 1em 0;}

        /* Hotmail header color reset
        Bring inline: Yes. */
        h1, h2, h3, h4, h5, h6 {color: black !important;}

        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        /* Outlook 07, 10 Padding issue fix
        Bring inline: No.*/
        table td {border-collapse: collapse;}

        /* Remove spacing around Outlook 07, 10 tables
        Bring inline: Yes */
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

        /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
        Bring inline: Yes. */
        a {color: blue;}
		table {
  background-color: transparent;
}
th {
  text-align: left;
}
.table {
  width: 100%;
  max-width: 100%;
  margin-bottom: 20px;
}
.table > thead > tr > th,
.table > tbody > tr > th,
.table > tfoot > tr > th,
.table > thead > tr > td,
.table > tbody > tr > td,
.table > tfoot > tr > td {
  padding: 8px;
  line-height: 1.42857143;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
.table > thead > tr > th {
  vertical-align: bottom;
  border-bottom: 2px solid #ddd;
}
.table > caption + thead > tr:first-child > th,
.table > colgroup + thead > tr:first-child > th,
.table > thead:first-child > tr:first-child > th,
.table > caption + thead > tr:first-child > td,
.table > colgroup + thead > tr:first-child > td,
.table > thead:first-child > tr:first-child > td {
  border-top: 0;
}
.table > tbody + tbody {
  border-top: 2px solid #ddd;
}
.table .table {
  background-color: #fff;
}
.table-condensed > thead > tr > th,
.table-condensed > tbody > tr > th,
.table-condensed > tfoot > tr > th,
.table-condensed > thead > tr > td,
.table-condensed > tbody > tr > td,
.table-condensed > tfoot > tr > td {
  padding: 5px;
}
.table-bordered {
  border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > th,
.table-bordered > tfoot > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > td {
  border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td {
  border-bottom-width: 2px;
}
.table-striped > tbody > tr:nth-child(odd) > td,
.table-striped > tbody > tr:nth-child(odd) > th {
  background-color: #f9f9f9;
}
.table-hover > tbody > tr:hover > td,
.table-hover > tbody > tr:hover > th {
  background-color: #f5f5f5;
}
table col[class*="col-"] {
  position: static;
  display: table-column;
  float: none;
}
table td[class*="col-"],
table th[class*="col-"] {
  position: static;
  display: table-cell;
  float: none;
}
.table > thead > tr > td.active,
.table > tbody > tr > td.active,
.table > tfoot > tr > td.active,
.table > thead > tr > th.active,
.table > tbody > tr > th.active,
.table > tfoot > tr > th.active,
.table > thead > tr.active > td,
.table > tbody > tr.active > td,
.table > tfoot > tr.active > td,
.table > thead > tr.active > th,
.table > tbody > tr.active > th,
.table > tfoot > tr.active > th {
  background-color: #f5f5f5;
}
.table-hover > tbody > tr > td.active:hover,
.table-hover > tbody > tr > th.active:hover,
.table-hover > tbody > tr.active:hover > td,
.table-hover > tbody > tr:hover > .active,
.table-hover > tbody > tr.active:hover > th {
  background-color: #e8e8e8;
}
.table > thead > tr > td.success,
.table > tbody > tr > td.success,
.table > tfoot > tr > td.success,
.table > thead > tr > th.success,
.table > tbody > tr > th.success,
.table > tfoot > tr > th.success,
.table > thead > tr.success > td,
.table > tbody > tr.success > td,
.table > tfoot > tr.success > td,
.table > thead > tr.success > th,
.table > tbody > tr.success > th,
.table > tfoot > tr.success > th {
  background-color: #dff0d8;
}
.table-hover > tbody > tr > td.success:hover,
.table-hover > tbody > tr > th.success:hover,
.table-hover > tbody > tr.success:hover > td,
.table-hover > tbody > tr:hover > .success,
.table-hover > tbody > tr.success:hover > th {
  background-color: #d0e9c6;
}
.table > thead > tr > td.info,
.table > tbody > tr > td.info,
.table > tfoot > tr > td.info,
.table > thead > tr > th.info,
.table > tbody > tr > th.info,
.table > tfoot > tr > th.info,
.table > thead > tr.info > td,
.table > tbody > tr.info > td,
.table > tfoot > tr.info > td,
.table > thead > tr.info > th,
.table > tbody > tr.info > th,
.table > tfoot > tr.info > th {
  background-color: #d9edf7;
}
.table-hover > tbody > tr > td.info:hover,
.table-hover > tbody > tr > th.info:hover,
.table-hover > tbody > tr.info:hover > td,
.table-hover > tbody > tr:hover > .info,
.table-hover > tbody > tr.info:hover > th {
  background-color: #c4e3f3;
}
.table > thead > tr > td.warning,
.table > tbody > tr > td.warning,
.table > tfoot > tr > td.warning,
.table > thead > tr > th.warning,
.table > tbody > tr > th.warning,
.table > tfoot > tr > th.warning,
.table > thead > tr.warning > td,
.table > tbody > tr.warning > td,
.table > tfoot > tr.warning > td,
.table > thead > tr.warning > th,
.table > tbody > tr.warning > th,
.table > tfoot > tr.warning > th {
  background-color: #fcf8e3;
}
.table-hover > tbody > tr > td.warning:hover,
.table-hover > tbody > tr > th.warning:hover,
.table-hover > tbody > tr.warning:hover > td,
.table-hover > tbody > tr:hover > .warning,
.table-hover > tbody > tr.warning:hover > th {
  background-color: #faf2cc;
}
.table > thead > tr > td.danger,
.table > tbody > tr > td.danger,
.table > tfoot > tr > td.danger,
.table > thead > tr > th.danger,
.table > tbody > tr > th.danger,
.table > tfoot > tr > th.danger,
.table > thead > tr.danger > td,
.table > tbody > tr.danger > td,
.table > tfoot > tr.danger > td,
.table > thead > tr.danger > th,
.table > tbody > tr.danger > th,
.table > tfoot > tr.danger > th {
  background-color: #f2dede;
}
.table-hover > tbody > tr > td.danger:hover,
.table-hover > tbody > tr > th.danger:hover,
.table-hover > tbody > tr.danger:hover > td,
.table-hover > tbody > tr:hover > .danger,
.table-hover > tbody > tr.danger:hover > th {
  background-color: #ebcccc;
}
		body {
font-family:"Roboto Condensed", sans-serif;
font-size: 14px;
line-height: 1.42857143;
color: #555555;
background-color: #ffffff;
}

.navbar {
	background: transparent url("../img/header-new.jpg") repeat-x scroll 0px 0px;
}
.navbar-inner, .encabezado {

	display: block;
	height: 85px;
	
}
.logo-empresa{
	margin:35px 0 0 30px;
}

.navbar-inner .btn-group {
margin: -35px 50px 0 5px;
}

.navbar-toggle {
	margin-top:25px;
}
.navbar-inner .btn-group .pull-right {
margin: -50px 5px 0 5px;
}

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
font-family:"Roboto Condensed", sans-serif;
font-weight: 500;
line-height: 1.1;
color: #003366;
}

.nic {
	width:auto;
	}
.tabs-detalle {
	margin-left:5%;
	width:90%;
}

a {
	color: #003366;
}

.nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
	color: #fff;
	background-color: #003366;
}

.pagination>.active>a, .pagination>.active>span, .pagination>.active>a:hover, .pagination>.active>span:hover, .pagination>.active>a:focus, .pagination>.active>span:focus {
z-index: 2;
color: #fff;
cursor: default;
background-color: #003366;
border-color: #003366;
}

.pagination>li>a, .pagination>li>span {
	color: #003366;
}
@media (max-width:767px){
.logo-escondido{
	margin:35px 0 0 30px;
	border:none;
	background-color:none;
}

.header-escondido {
	
	height: 100px;
	border:none;
	background:none;
	float:left;
}
.header-escondido:hover {
	background:none;
}

.navbar-default .navbar-toggle:hover, .navbar-default .navbar-toggle:focus {
	background:none;
}
.navbar-inner .btn-group {
	margin: -65px 5px 0 0;
}
.navbar-toggle {
	margin-top:10px;
}

.menu-celular {
	margin-top: 25px;
}
}
@media (min-width:768px){
.header-escondido {
	display: none;

}
}

@media (max-width:400px){
.logo-escondido{
	margin:50px 0 0 30px;
	border:none;
	background-color:none;
	max-width:150px;
}
}

.logo {
    background: transparent url("../img/logo.png") no-repeat scroll 0px 0px;
    height: 20px;
    margin-top: 32px;
    text-indent: -3000px;
    width: 105px;
    margin-left:90px;
}

.group {
	background-color:#036 !important;
	color:#fff;
}

		
    </style>

</head>

<body>

'.$message.'

</body>
</html>';

        return $full_html;
    }


    // Protected methods -------------------------------------------------------

    protected function _get_alt_message() {

        $alt_message = (string) $this->alt_message;

        if ($alt_message == '') {
            $alt_message = $this->_plain_text($this->_body);
        }

        if ($this->mailer_engine == 'phpmailer') {
            // PHPMailer would do the word wrapping.
            return $alt_message;
        }

        return ($this->wordwrap)
            ? $this->word_wrap($alt_message, 76)
            : $alt_message;
    }

    protected function _plain_text($html) {

        if (!function_exists('html_to_text')) {

            $body = @ html_entity_decode($html, ENT_QUOTES, $this->charset); // Added by Ivan Tcholakov, 28-JUL-2013.

            $body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $body, $match) ? $match[1] : $body;
            $body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

            for ($i = 20; $i >= 3; $i--)
            {
                $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
            }

            // Reduce multiple spaces
            $body = preg_replace('| +|', ' ', $body);

            return $body;
        }

        // Also, a special helper function based on Markdown or Textile libraries may be used.
        //
        // An example of Markdown-based implementation, see http://milianw.de/projects/markdownify/
        //
        // Make sure the class Markdownify_Extra is autoloaded (or simply loaded somehow).
        // Place in MY_html_helper.php the following function.
        //
        // function html_to_text($html) {
        //     static $parser;
        //     if (!isset($parser)) {
        //         $parser = new Markdownify_Extra();
        //         $parser->keepHTML = false;
        //     }
        //     return @ $parser->parseString($html);
        // }
        //

        return html_to_text($html);
    }

    protected function _set_config_option($key, $value) {

        $method = 'set_'.$key;

        if (method_exists($this, $method)) {

            $this->$method($value);

        } elseif (isset($this->$key)) {

            $this->$key = $value;

            if ($this->mailer_engine == 'phpmailer') {
                $this->_copy_property_to_phpmailer($key);
            }
        }
    }

    protected function _copy_property_to_phpmailer($key) {

        static $properties = array(
            'mailpath' => 'Sendmail',
            'smtp_host' => 'Host',
            'smtp_user' => 'Username',
            'smtp_pass' => 'Password',
            'smtp_port' => 'Port',
            'smtp_timeout' => 'Timeout',
            '_smtp_auth' => 'SMTPAuth',
        );

        if (isset($properties[$key])) {
            $this->phpmailer->{$properties[$key]} = $this->$key;
        }
    }

    protected function _extract_name($address) {

        if (!is_array($address)) {

            $address = trim($address);

            if (preg_match('/(.*)\<(.*)\>/', $address, $match)) {
                return trim($match['1']);
            } else {
                return '';
            }
        }

        $result = array();

        foreach ($address as $addr) {

            $addr = trim($addr);

            if (preg_match('/(.*)\<(.*)\>/', $addr, $match)) {
                $result[] = trim($match['1']);
            } else {
                $result[] = '';
            }
        }

        return $result;
    }

}
