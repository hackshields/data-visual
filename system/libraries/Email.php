<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * CodeIgniter Email Class
 *
 * Permits email to be sent using Mail, Sendmail, or SMTP.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/email.html
 */

class CI_Email
{
/**
	 * Used as the User-Agent and X-Mailer headers' value.
	 *
	 * @var	string
	 */
    public $useragent = "CodeIgniter";
/**
	 * Path to the Sendmail binary.
	 *
	 * @var	string
	 */
    public $mailpath = "/usr/sbin/sendmail";
/**
	 * Which method to use for sending e-mails.
	 *
	 * @var	string	'mail', 'sendmail' or 'smtp'
	 */
    public $protocol = "mail";
/**
	 * STMP Server host
	 *
	 * @var	string
	 */
    public $smtp_host = "";
/**
	 * SMTP Username
	 *
	 * @var	string
	 */
    public $smtp_user = "";
/**
	 * SMTP Password
	 *
	 * @var	string
	 */
    public $smtp_pass = "";
/**
	 * SMTP Server port
	 *
	 * @var	int
	 */
    public $smtp_port = 25;
/**
	 * SMTP connection timeout in seconds
	 *
	 * @var	int
	 */
    public $smtp_timeout = 5;
/**
	 * SMTP persistent connection
	 *
	 * @var	bool
	 */
    public $smtp_keepalive = false;
/**
	 * SMTP Encryption
	 *
	 * @var	string	empty, 'tls' or 'ssl'
	 */
    public $smtp_crypto = "";
/**
	 * Whether to apply word-wrapping to the message body.
	 *
	 * @var	bool
	 */
    public $wordwrap = true;
/**
	 * Number of characters to wrap at.
	 *
	 * @see	CI_Email::$wordwrap
	 * @var	int
	 */
    public $wrapchars = 76;
/**
	 * Message format.
	 *
	 * @var	string	'text' or 'html'
	 */
    public $mailtype = "text";
/**
	 * Character set (default: utf-8)
	 *
	 * @var	string
	 */
    public $charset = "utf-8";
/**
	 * Alternative message (for HTML messages only)
	 *
	 * @var	string
	 */
    public $alt_message = "";
/**
	 * Whether to validate e-mail addresses.
	 *
	 * @var	bool
	 */
    public $validate = true;
/**
	 * X-Priority header value.
	 *
	 * @var	int	1-5
	 */
    public $priority = 3;
/**
	 * Newline character sequence.
	 * Use "\r\n" to comply with RFC 822.
	 *
	 * @link	http://www.ietf.org/rfc/rfc822.txt
	 * @var	string	"\r\n" or "\n"
	 */
    public $newline = "\n";
/**
	 * CRLF character sequence
	 *
	 * RFC 2045 specifies that for 'quoted-printable' encoding,
	 * "\r\n" must be used. However, it appears that some servers
	 * (even on the receiving end) don't handle it properly and
	 * switching to "\n", while improper, is the only solution
	 * that seems to work for all environments.
	 *
	 * @link	http://www.ietf.org/rfc/rfc822.txt
	 * @var	string
	 */
    public $crlf = "\n";
/**
	 * Whether to use Delivery Status Notification.
	 *
	 * @var	bool
	 */
    public $dsn = false;
/**
	 * Whether to send multipart alternatives.
	 * Yahoo! doesn't seem to like these.
	 *
	 * @var	bool
	 */
    public $send_multipart = true;
/**
	 * Whether to send messages to BCC recipients in batches.
	 *
	 * @var	bool
	 */
    public $bcc_batch_mode = false;
/**
	 * BCC Batch max number size.
	 *
	 * @see	CI_Email::$bcc_batch_mode
	 * @var	int
	 */
    public $bcc_batch_size = 200;
/**
	 * Subject header
	 *
	 * @var	string
	 */
    protected $_subject = "";
/**
	 * Message body
	 *
	 * @var	string
	 */
    protected $_body = "";
/**
	 * Final message body to be sent.
	 *
	 * @var	string
	 */
    protected $_finalbody = "";
/**
	 * Final headers to send
	 *
	 * @var	string
	 */
    protected $_header_str = "";
/**
	 * SMTP Connection socket placeholder
	 *
	 * @var	resource
	 */
    protected $_smtp_connect = "";
/**
	 * Mail encoding
	 *
	 * @var	string	'8bit' or '7bit'
	 */
    protected $_encoding = "8bit";
/**
	 * Whether to perform SMTP authentication
	 *
	 * @var	bool
	 */
    protected $_smtp_auth = false;
/**
	 * Whether to send a Reply-To header
	 *
	 * @var	bool
	 */
    protected $_replyto_flag = false;
/**
	 * Debug messages
	 *
	 * @see	CI_Email::print_debugger()
	 * @var	string
	 */
    protected $_debug_msg = array(  );
/**
	 * Recipients
	 *
	 * @var	string[]
	 */
    protected $_recipients = array(  );
/**
	 * CC Recipients
	 *
	 * @var	string[]
	 */
    protected $_cc_array = array(  );
/**
	 * BCC Recipients
	 *
	 * @var	string[]
	 */
    protected $_bcc_array = array(  );
/**
	 * Message headers
	 *
	 * @var	string[]
	 */
    protected $_headers = array(  );
/**
	 * Attachment data
	 *
	 * @var	array
	 */
    protected $_attachments = array(  );
/**
	 * Valid $protocol values
	 *
	 * @see	CI_Email::$protocol
	 * @var	string[]
	 */
    protected $_protocols = array( "mail", "sendmail", "smtp" );
/**
	 * Base charsets
	 *
	 * Character sets valid for 7-bit encoding,
	 * excluding language suffix.
	 *
	 * @var	string[]
	 */
    protected $_base_charsets = array( "us-ascii", "iso-2022-" );
/**
	 * Bit depths
	 *
	 * Valid mail encodings
	 *
	 * @see	CI_Email::$_encoding
	 * @var	string[]
	 */
    protected $_bit_depths = array( "7bit", "8bit" );
/**
	 * $priority translations
	 *
	 * Actual values to send with the X-Priority header
	 *
	 * @var	string[]
	 */
    protected $_priorities = array( "1" => "1 (Highest)", "2" => "2 (High)", "3" => "3 (Normal)", "4" => "4 (Low)", "5" => "5 (Lowest)" );
/**
	 * mbstring.func_overload flag
	 *
	 * @var	bool
	 */
    protected static $func_overload = NULL;

    /**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 *
	 * @param	array	$config = array()
	 * @return	void
	 */

    public function __construct(array $config = array(  ))
    {
        $this->charset = config_item("charset");
        $this->initialize($config);
        isset($func_overload) or ini_get("mbstring.func_overload");
        log_message("info", "Email Class Initialized");
    }

    /**
	 * Initialize preferences
	 *
	 * @param	array	$config
	 * @return	CI_Email
	 */

    public function initialize(array $config = array(  ))
    {
        $this->clear();
        foreach( $config as $key => $val ) 
        {
            if( isset($this->$key) ) 
            {
                $method = "set_" . $key;
                if( method_exists($this, $method) ) 
                {
                    $this->$method($val);
                }
                else
                {
                    $this->$key = $val;
                }

            }

        }
        $this->charset = strtoupper($this->charset);
        $this->_smtp_auth = isset($this->smtp_user[0]) && isset($this->smtp_pass[0]);
        return $this;
    }

    /**
	 * Initialize the Email Data
	 *
	 * @param	bool
	 * @return	CI_Email
	 */

    public function clear($clear_attachments = false)
    {
        $this->_subject = "";
        $this->_body = "";
        $this->_finalbody = "";
        $this->_header_str = "";
        $this->_replyto_flag = false;
        $this->_recipients = array(  );
        $this->_cc_array = array(  );
        $this->_bcc_array = array(  );
        $this->_headers = array(  );
        $this->_debug_msg = array(  );
        $this->set_header("Date", $this->_set_date());
        if( $clear_attachments !== false ) 
        {
            $this->_attachments = array(  );
        }

        return $this;
    }

    /**
	 * Set FROM
	 *
	 * @param	string	$from
	 * @param	string	$name
	 * @param	string	$return_path = NULL	Return-Path
	 * @return	CI_Email
	 */

    public function from($from, $name = "", $return_path = NULL)
    {
        if( preg_match("/\\<(.*)\\>/", $from, $match) ) 
        {
            $from = $match[1];
        }

        if( $this->validate ) 
        {
            $this->validate_email($this->_str_to_array($from));
            if( $return_path ) 
            {
                $this->validate_email($this->_str_to_array($return_path));
            }

        }

        if( $name !== "" ) 
        {
            if( !preg_match("/[\\200-\\377]/", $name) ) 
            {
                $name = "\"" . addcslashes($name, "") . "\"";
            }
            else
            {
                $name = $this->_prep_q_encoding($name);
            }

        }

        $this->set_header("From", $name . " <" . $from . ">");
        isset($return_path) or $this->set_header("Return-Path", "<" . $return_path . ">");
        return $this;
    }

    /**
	 * Set Reply-to
	 *
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */

    public function reply_to($replyto, $name = "")
    {
        if( preg_match("/\\<(.*)\\>/", $replyto, $match) ) 
        {
            $replyto = $match[1];
        }

        if( $this->validate ) 
        {
            $this->validate_email($this->_str_to_array($replyto));
        }

        if( $name !== "" ) 
        {
            if( !preg_match("/[\\200-\\377]/", $name) ) 
            {
                $name = "\"" . addcslashes($name, "") . "\"";
            }
            else
            {
                $name = $this->_prep_q_encoding($name);
            }

        }

        $this->set_header("Reply-To", $name . " <" . $replyto . ">");
        $this->_replyto_flag = true;
        return $this;
    }

    /**
	 * Set Recipients
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function to($to)
    {
        $to = $this->_str_to_array($to);
        $to = $this->clean_email($to);
        if( $this->validate ) 
        {
            $this->validate_email($to);
        }

        if( $this->_get_protocol() !== "mail" ) 
        {
            $this->set_header("To", implode(", ", $to));
        }

        $this->_recipients = $to;
        return $this;
    }

    /**
	 * Set CC
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function cc($cc)
    {
        $cc = $this->clean_email($this->_str_to_array($cc));
        if( $this->validate ) 
        {
            $this->validate_email($cc);
        }

        $this->set_header("Cc", implode(", ", $cc));
        if( $this->_get_protocol() === "smtp" ) 
        {
            $this->_cc_array = $cc;
        }

        return $this;
    }

    /**
	 * Set BCC
	 *
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */

    public function bcc($bcc, $limit = "")
    {
        if( $limit !== "" && is_numeric($limit) ) 
        {
            $this->bcc_batch_mode = true;
            $this->bcc_batch_size = $limit;
        }

        $bcc = $this->clean_email($this->_str_to_array($bcc));
        if( $this->validate ) 
        {
            $this->validate_email($bcc);
        }

        if( $this->_get_protocol() === "smtp" || $this->bcc_batch_mode && $this->bcc_batch_size < count($bcc) ) 
        {
            $this->_bcc_array = $bcc;
        }
        else
        {
            $this->set_header("Bcc", implode(", ", $bcc));
        }

        return $this;
    }

    /**
	 * Set Email Subject
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function subject($subject)
    {
        $subject = $this->_prep_q_encoding($subject);
        $this->set_header("Subject", $subject);
        return $this;
    }

    /**
	 * Set Body
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function message($body)
    {
        $this->_body = rtrim(str_replace("\r", "", $body));
        return $this;
    }

    /**
	 * Assign file attachments
	 *
	 * @param	string	$file	Can be local path, URL or buffered content
	 * @param	string	$disposition = 'attachment'
	 * @param	string	$newname = NULL
	 * @param	string	$mime = ''
	 * @return	CI_Email
	 */

    public function attach($file, $disposition = "", $newname = NULL, $mime = "")
    {
        if( $mime === "" ) 
        {
            if( strpos($file, "://") === false && !file_exists($file) ) 
            {
                $this->_set_error_message("lang:email_attachment_missing", $file);
                return false;
            }

            if( !($fp = @fopen($file, "rb")) ) 
            {
                $this->_set_error_message("lang:email_attachment_unreadable", $file);
                return false;
            }

            $file_content = stream_get_contents($fp);
            $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
            fclose($fp);
        }
        else
        {
            $file_content =& $file;
        }

        $this->_attachments[] = array( "name" => array( $file, $newname ), "disposition" => (empty($disposition) ? "attachment" : $disposition), "type" => $mime, "content" => chunk_split(base64_encode($file_content)), "multipart" => "mixed" );
        return $this;
    }

    /**
	 * Set and return attachment Content-ID
	 *
	 * Useful for attached inline pictures
	 *
	 * @param	string	$filename
	 * @return	string
	 */

    public function attachment_cid($filename)
    {
        $i = 0;
        for( $c = count($this->_attachments); $i < $c; $i++ ) 
        {
            if( $this->_attachments[$i]["name"][0] === $filename ) 
            {
                $this->_attachments[$i]["multipart"] = "related";
                $this->_attachments[$i]["cid"] = uniqid(basename($this->_attachments[$i]["name"][0]) . "@");
                return $this->_attachments[$i]["cid"];
            }

        }
        return false;
    }

    /**
	 * Add a Header Item
	 *
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_header($header, $value)
    {
        $this->_headers[$header] = str_replace(array( "\n", "\r" ), "", $value);
        return $this;
    }

    /**
	 * Convert a String to an Array
	 *
	 * @param	string
	 * @return	array
	 */

    protected function _str_to_array($email)
    {
        if( !is_array($email) ) 
        {
            return (strpos($email, ",") !== false ? preg_split("/[\\s,]/", $email, -1, PREG_SPLIT_NO_EMPTY) : (array) trim($email));
        }

        return $email;
    }

    /**
	 * Set Multipart Value
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_alt_message($str)
    {
        $this->alt_message = (string) $str;
        return $this;
    }

    /**
	 * Set Mailtype
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_mailtype($type = "text")
    {
        $this->mailtype = ($type === "html" ? "html" : "text");
        return $this;
    }

    /**
	 * Set Wordwrap
	 *
	 * @param	bool
	 * @return	CI_Email
	 */

    public function set_wordwrap($wordwrap = true)
    {
        $this->wordwrap = (bool) $wordwrap;
        return $this;
    }

    /**
	 * Set Protocol
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_protocol($protocol = "mail")
    {
        $this->protocol = (in_array($protocol, $this->_protocols, true) ? strtolower($protocol) : "mail");
        return $this;
    }

    /**
	 * Set Priority
	 *
	 * @param	int
	 * @return	CI_Email
	 */

    public function set_priority($n = 3)
    {
        $this->priority = (preg_match("/^[1-5]\$/", $n) ? (int) $n : 3);
        return $this;
    }

    /**
	 * Set Newline Character
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_newline($newline = "\n")
    {
        $this->newline = (in_array($newline, array( "\n", "\r\n", "\r" )) ? $newline : "\n");
        return $this;
    }

    /**
	 * Set CRLF
	 *
	 * @param	string
	 * @return	CI_Email
	 */

    public function set_crlf($crlf = "\n")
    {
        $this->crlf = ($crlf !== "\n" && $crlf !== "\r\n" && $crlf !== "\r" ? "\n" : $crlf);
        return $this;
    }

    /**
	 * Get the Message ID
	 *
	 * @return	string
	 */

    protected function _get_message_id()
    {
        $from = str_replace(array( ">", "<" ), "", $this->_headers["Return-Path"]);
        return "<" . uniqid("") . strstr($from, "@") . ">";
    }

    /**
	 * Get Mail Protocol
	 *
	 * @return	mixed
	 */

    protected function _get_protocol()
    {
        $this->protocol = strtolower($this->protocol);
        in_array($this->protocol, $this->_protocols, true) or return $this->protocol;
    }

    /**
	 * Get Mail Encoding
	 *
	 * @return	string
	 */

    protected function _get_encoding()
    {
        in_array($this->_encoding, $this->_bit_depths) or foreach( $this->_base_charsets as $charset ) 
{
    if( strpos($this->charset, $charset) === 0 ) 
    {
        $this->_encoding = "7bit";
    }

}
        return $this->_encoding;
    }

    /**
	 * Get content type (text/html/attachment)
	 *
	 * @return	string
	 */

    protected function _get_content_type()
    {
        if( $this->mailtype === "html" ) 
        {
            return (empty($this->_attachments) ? "html" : "html-attach");
        }

        if( $this->mailtype === "text" && !empty($this->_attachments) ) 
        {
            return "plain-attach";
        }

        return "plain";
    }

    /**
	 * Set RFC 822 Date
	 *
	 * @return	string
	 */

    protected function _set_date()
    {
        $timezone = date("Z");
        $operator = ($timezone[0] === "-" ? "-" : "+");
        $timezone = abs($timezone);
        $timezone = floor($timezone / 3600) * 100 + $timezone % 3600 / 60;
        return sprintf("%s %s%04d", date("D, j M Y H:i:s"), $operator, $timezone);
    }

    /**
	 * Mime message
	 *
	 * @return	string
	 */

    protected function _get_mime_message()
    {
        return "This is a multi-part message in MIME format." . $this->newline . "Your email application may not support this format.";
    }

    /**
	 * Validate Email Address
	 *
	 * @param	string
	 * @return	bool
	 */

    public function validate_email($email)
    {
        if( !is_array($email) ) 
        {
            $this->_set_error_message("lang:email_must_be_array");
            return false;
        }

        foreach( $email as $val ) 
        {
            if( !$this->valid_email($val) ) 
            {
                $this->_set_error_message("lang:email_invalid_address", $val);
                return false;
            }

        }
        return true;
    }

    /**
	 * Email Validation
	 *
	 * @param	string
	 * @return	bool
	 */

    public function valid_email($email)
    {
        if( function_exists("idn_to_ascii") && preg_match("#\\A([^@]+)@(.+)\\z#", $email, $matches) ) 
        {
            $domain = (defined("INTL_IDNA_VARIANT_UTS46") ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46) : idn_to_ascii($matches[2]));
            $email = $matches[1] . "@" . $domain;
        }

        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
	 * Clean Extended Email Address: Joe Smith <joe@smith.com>
	 *
	 * @param	string
	 * @return	string
	 */

    public function clean_email($email)
    {
        if( !is_array($email) ) 
        {
            return (preg_match("/\\<(.*)\\>/", $email, $match) ? $match[1] : $email);
        }

        $clean_email = array(  );
        foreach( $email as $addy ) 
        {
            $clean_email[] = (preg_match("/\\<(.*)\\>/", $addy, $match) ? $match[1] : $addy);
        }
        return $clean_email;
    }

    /**
	 * Build alternative plain text message
	 *
	 * Provides the raw message for use in plain-text headers of
	 * HTML-formatted emails.
	 * If the user hasn't specified his own alternative message
	 * it creates one by stripping the HTML
	 *
	 * @return	string
	 */

    protected function _get_alt_message()
    {
        if( !empty($this->alt_message) ) 
        {
            return ($this->wordwrap ? $this->word_wrap($this->alt_message, 76) : $this->alt_message);
        }

        $body = (preg_match("/\\<body.*?\\>(.*)\\<\\/body\\>/si", $this->_body, $match) ? $match[1] : $this->_body);
        $body = str_replace("\t", "", preg_replace("#<!--(.*)--\\>#", "", trim(strip_tags($body))));
        for( $i = 20; 3 <= $i; $i-- ) 
        {
            $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
        }
        $body = preg_replace("| +|", " ", $body);
        return ($this->wordwrap ? $this->word_wrap($body, 76) : $body);
    }

    /**
	 * Word Wrap
	 *
	 * @param	string
	 * @param	int	line-length limit
	 * @return	string
	 */

    public function word_wrap($str, $charlim = NULL)
    {
        if( empty($charlim) ) 
        {
            $charlim = (empty($this->wrapchars) ? 76 : $this->wrapchars);
        }

        if( strpos($str, "\r") !== false ) 
        {
            $str = str_replace(array( "\r\n", "\r" ), "\n", $str);
        }

        $str = preg_replace("| +\\n|", "\n", $str);
        $unwrap = array(  );
        if( preg_match_all("|\\{unwrap\\}(.+?)\\{/unwrap\\}|s", $str, $matches) ) 
        {
            $i = 0;
            for( $c = count($matches[0]); $i < $c; $i++ ) 
            {
                $unwrap[] = $matches[1][$i];
                $str = str_replace($matches[0][$i], "{{unwrapped" . $i . "}}", $str);
            }
        }

        $str = wordwrap($str, $charlim, "\n", false);
        $output = "";
        foreach( explode("\n", $str) as $line ) 
        {
            if( self::strlen($line) <= $charlim ) 
            {
                $output .= $line . $this->newline;
                continue;
            }

            $temp = "";
            while( preg_match("!\\[url.+\\]|://|www\\.!", $line) ) 
            {
                break;
            }
            $temp .= self::substr($line, 0, $charlim - 1);
            $line = self::substr($line, $charlim - 1);
            if( $charlim >= self::strlen($line) ) 
            {
                if( $temp !== "" ) 
                {
                    $output .= $temp . $this->newline;
                }

                $output .= $line . $this->newline;
            }

        }
        if( 0 < count($unwrap) ) 
        {
            foreach( $unwrap as $key => $val ) 
            {
                $output = str_replace("{{unwrapped" . $key . "}}", $val, $output);
            }
        }

        return $output;
    }

    /**
	 * Build final headers
	 *
	 * @return	void
	 */

    protected function _build_headers()
    {
        $this->set_header("User-Agent", $this->useragent);
        $this->set_header("X-Sender", $this->clean_email($this->_headers["From"]));
        $this->set_header("X-Mailer", $this->useragent);
        $this->set_header("X-Priority", $this->_priorities[$this->priority]);
        $this->set_header("Message-ID", $this->_get_message_id());
        $this->set_header("Mime-Version", "1.0");
    }

    /**
	 * Write Headers as a string
	 *
	 * @return	void
	 */

    protected function _write_headers()
    {
        if( $this->protocol === "mail" && isset($this->_headers["Subject"]) ) 
        {
            $this->_subject = $this->_headers["Subject"];
            unset($this->_headers["Subject"]);
        }

        reset($this->_headers);
        $this->_header_str = "";
        foreach( $this->_headers as $key => $val ) 
        {
            $val = trim($val);
            if( $val !== "" ) 
            {
                $this->_header_str .= $key . ": " . $val . $this->newline;
            }

        }
        if( $this->_get_protocol() === "mail" ) 
        {
            $this->_header_str = rtrim($this->_header_str);
        }

    }

    /**
	 * Build Final Body and attachments
	 *
	 * @return	void
	 */

    protected function _build_message()
    {
        if( $this->wordwrap === true && $this->mailtype !== "html" ) 
        {
            $this->_body = $this->word_wrap($this->_body);
        }

        $this->_write_headers();
        $hdr = ($this->_get_protocol() === "mail" ? $this->newline : "");
        $body = "";
        switch( $this->_get_content_type() ) 
        {
            case "plain":
                $hdr .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: " . $this->_get_encoding();
                if( $this->_get_protocol() === "mail" ) 
                {
                    $this->_header_str .= $hdr;
                    $this->_finalbody = $this->_body;
                }
                else
                {
                    $this->_finalbody = $hdr . $this->newline . $this->newline . $this->_body;
                }

                return NULL;
            case "html":
                if( $this->send_multipart === false ) 
                {
                    $hdr .= "Content-Type: text/html; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: quoted-printable";
                }
                else
                {
                    $boundary = uniqid("B_ALT_");
                    $hdr .= "Content-Type: multipart/alternative; boundary=\"" . $boundary . "\"";
                    $body .= $this->_get_mime_message() . $this->newline . $this->newline . "--" . $boundary . $this->newline . "Content-Type: text/plain; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline . $this->_get_alt_message() . $this->newline . $this->newline . "--" . $boundary . $this->newline . "Content-Type: text/html; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;
                }

                $this->_finalbody = $body . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;
                if( $this->_get_protocol() === "mail" ) 
                {
                    $this->_header_str .= $hdr;
                }
                else
                {
                    $this->_finalbody = $hdr . $this->newline . $this->newline . $this->_finalbody;
                }

                if( $this->send_multipart !== false ) 
                {
                    $this->_finalbody .= "--" . $boundary . "--";
                }

                return NULL;
            case "plain-attach":
                $boundary = uniqid("B_ATC_");
                $hdr .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"";
                if( $this->_get_protocol() === "mail" ) 
                {
                    $this->_header_str .= $hdr;
                }

                $body .= $this->_get_mime_message() . $this->newline . $this->newline . "--" . $boundary . $this->newline . "Content-Type: text/plain; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline . $this->_body . $this->newline . $this->newline;
                $this->_append_attachments($body, $boundary);
                break;
            case "html-attach":
                $alt_boundary = uniqid("B_ALT_");
                $last_boundary = NULL;
                if( $this->_attachments_have_multipart("mixed") ) 
                {
                    $atc_boundary = uniqid("B_ATC_");
                    $hdr .= "Content-Type: multipart/mixed; boundary=\"" . $atc_boundary . "\"";
                    $last_boundary = $atc_boundary;
                }

                if( $this->_attachments_have_multipart("related") ) 
                {
                    $rel_boundary = uniqid("B_REL_");
                    $rel_boundary_header = "Content-Type: multipart/related; boundary=\"" . $rel_boundary . "\"";
                    if( isset($last_boundary) ) 
                    {
                        $body .= "--" . $last_boundary . $this->newline . $rel_boundary_header;
                    }
                    else
                    {
                        $hdr .= $rel_boundary_header;
                    }

                    $last_boundary = $rel_boundary;
                }

                if( $this->_get_protocol() === "mail" ) 
                {
                    $this->_header_str .= $hdr;
                }

                self::strlen($body) and $body .= $this->_get_mime_message() . $this->newline . $this->newline . "--" . $last_boundary . $this->newline . "Content-Type: multipart/alternative; boundary=\"" . $alt_boundary . "\"" . $this->newline . $this->newline . "--" . $alt_boundary . $this->newline . "Content-Type: text/plain; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline . $this->_get_alt_message() . $this->newline . $this->newline . "--" . $alt_boundary . $this->newline . "Content-Type: text/html; charset=" . $this->charset . $this->newline . "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline . "--" . $alt_boundary . "--" . $this->newline . $this->newline;
                if( !empty($rel_boundary) ) 
                {
                    $body .= $this->newline . $this->newline;
                    $this->_append_attachments($body, $rel_boundary, "related");
                }

                if( !empty($atc_boundary) ) 
                {
                    $body .= $this->newline . $this->newline;
                    $this->_append_attachments($body, $atc_boundary, "mixed");
                }

                break;
        }
        $this->_finalbody = ($this->_get_protocol() === "mail" ? $body : $hdr . $this->newline . $this->newline . $body);
    }

    protected function _attachments_have_multipart($type)
    {
        foreach( $this->_attachments as &$attachment ) 
        {
            if( $attachment["multipart"] === $type ) 
            {
                return true;
            }

        }
        return false;
    }

    /**
	 * Prepares attachment string
	 *
	 * @param	string	$body		Message body to append to
	 * @param	string	$boundary	Multipart boundary
	 * @param	string	$multipart	When provided, only attachments of this type will be processed
	 * @return	string
	 */

    protected function _append_attachments(&$body, $boundary, $multipart = NULL)
    {
        $i = 0;
        for( $c = count($this->_attachments); $i < $c; $i++ ) 
        {
            if( isset($multipart) && $this->_attachments[$i]["multipart"] !== $multipart ) 
            {
                continue;
            }

            $name = (isset($this->_attachments[$i]["name"][1]) ? $this->_attachments[$i]["name"][1] : basename($this->_attachments[$i]["name"][0]));
            $body .= "--" . $boundary . $this->newline . "Content-Type: " . $this->_attachments[$i]["type"] . "; name=\"" . $name . "\"" . $this->newline . "Content-Disposition: " . $this->_attachments[$i]["disposition"] . ";" . $this->newline . "Content-Transfer-Encoding: base64" . $this->newline . ((empty($this->_attachments[$i]["cid"]) ? "" : "Content-ID: <" . $this->_attachments[$i]["cid"] . ">" . $this->newline)) . $this->newline . $this->_attachments[$i]["content"] . $this->newline;
        }
        empty($name) or     }

    /**
	 * Prep Quoted Printable
	 *
	 * Prepares string for Quoted-Printable Content-Transfer-Encoding
	 * Refer to RFC 2045 http://www.ietf.org/rfc/rfc2045.txt
	 *
	 * @param	string
	 * @return	string
	 */

    protected function _prep_quoted_printable($str)
    {
        static $ascii_safe_chars = array( 39, 40, 41, 43, 44, 45, 46, 47, 58, 61, 63, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122 );
        $str = str_replace(array( "{unwrap}", "{/unwrap}" ), "", $str);
        if( $this->crlf === "\r\n" ) 
        {
            return quoted_printable_encode($str);
        }

        $str = preg_replace(array( "| +|", "/\\x00+/" ), array( " ", "" ), $str);
        if( strpos($str, "\r") !== false ) 
        {
            $str = str_replace(array( "\r\n", "\r" ), "\n", $str);
        }

        $escape = "=";
        $output = "";
        foreach( explode("\n", $str) as $line ) 
        {
            $length = self::strlen($line);
            $temp = "";
            for( $i = 0; $i < $length; $i++ ) 
            {
                $char = $line[$i];
                $ascii = ord($char);
                if( $ascii === 32 || $ascii === 9 ) 
                {
                    if( $i === $length - 1 ) 
                    {
                        $char = $escape . sprintf("%02s", dechex($ascii));
                    }

                }
                else
                {
                    if( $ascii === 61 ) 
                    {
                        $char = $escape . strtoupper(sprintf("%02s", dechex($ascii)));
                    }
                    else
                    {
                        if( !in_array($ascii, $ascii_safe_chars, true) ) 
                        {
                            $char = $escape . strtoupper(sprintf("%02s", dechex($ascii)));
                        }

                    }

                }

                if( 76 <= self::strlen($temp) + self::strlen($char) ) 
                {
                    $output .= $temp . $escape . $this->crlf;
                    $temp = "";
                }

                $temp .= $char;
            }
            $output .= $temp . $this->crlf;
        }
        return self::substr($output, 0, self::strlen($this->crlf) * -1);
    }

    /**
	 * Prep Q Encoding
	 *
	 * Performs "Q Encoding" on a string for use in email headers.
	 * It's related but not identical to quoted-printable, so it has its
	 * own method.
	 *
	 * @param	string
	 * @return	string
	 */

    protected function _prep_q_encoding($str)
    {
        $str = str_replace(array( "\r", "\n" ), "", $str);
        if( $this->charset === "UTF-8" ) 
        {
            if( ICONV_ENABLED === true ) 
            {
                $output = @iconv_mime_encode("", $str, array( "scheme" => "Q", "line-length" => 76, "input-charset" => $this->charset, "output-charset" => $this->charset, "line-break-chars" => $this->crlf ));
                if( $output !== false ) 
                {
                    return self::substr($output, 2);
                }

                $chars = iconv_strlen($str, "UTF-8");
            }
            else
            {
                if( MB_ENABLED === true ) 
                {
                    $chars = mb_strlen($str, "UTF-8");
                }

            }

        }

        isset($chars) or self::strlen($str);
        $output = "=?" . $this->charset . "?Q?";
        $i = 0;
        for( $length = self::strlen($output); $i < $chars; $i++ ) 
        {
            $chr = ($this->charset === "UTF-8" && ICONV_ENABLED === true ? "=" . implode("=", str_split(strtoupper(bin2hex(iconv_substr($str, $i, 1, $this->charset))), 2)) : "=" . strtoupper(bin2hex($str[$i])));
            if( 74 < $length + ($l = self::strlen($chr)) ) 
            {
                $output .= "?=" . $this->crlf . " =?" . $this->charset . "?Q?" . $chr;
                $length = 6 + self::strlen($this->charset) + $l;
            }
            else
            {
                $output .= $chr;
                $length += $l;
            }

        }
        return $output . "?=";
    }

    /**
	 * Send Email
	 *
	 * @param	bool	$auto_clear = TRUE
	 * @return	bool
	 */

    public function send($auto_clear = true)
    {
        if( !isset($this->_headers["From"]) ) 
        {
            $this->_set_error_message("lang:email_no_from");
            return false;
        }

        if( $this->_replyto_flag === false ) 
        {
            $this->reply_to($this->_headers["From"]);
        }

        if( empty($this->_recipients) && !isset($this->_headers["To"]) && empty($this->_bcc_array) && !isset($this->_headers["Bcc"]) && !isset($this->_headers["Cc"]) ) 
        {
            $this->_set_error_message("lang:email_no_recipients");
            return false;
        }

        $this->_build_headers();
        if( $this->bcc_batch_mode && $this->bcc_batch_size < count($this->_bcc_array) ) 
        {
            $this->batch_bcc_send();
            if( $auto_clear ) 
            {
                $this->clear();
            }

            return true;
        }

        $this->_build_message();
        $result = $this->_spool_email();
        if( $result && $auto_clear ) 
        {
            $this->clear();
        }

        return $result;
    }

    /**
	 * Batch Bcc Send. Sends groups of BCCs in batches
	 *
	 * @return	void
	 */

    public function batch_bcc_send()
    {
        $float = $this->bcc_batch_size - 1;
        $set = "";
        $chunk = array(  );
        $i = 0;
        for( $c = count($this->_bcc_array); $i < $c; $i++ ) 
        {
            if( isset($this->_bcc_array[$i]) ) 
            {
                $set .= ", " . $this->_bcc_array[$i];
            }

            if( $i === $float ) 
            {
                $chunk[] = self::substr($set, 1);
                $float += $this->bcc_batch_size;
                $set = "";
            }

            if( $i === $c - 1 ) 
            {
                $chunk[] = self::substr($set, 1);
            }

        }
        $i = 0;
        for( $c = count($chunk); $i < $c; $i++ ) 
        {
            unset($this->_headers["Bcc"]);
            $bcc = $this->clean_email($this->_str_to_array($chunk[$i]));
            if( $this->protocol !== "smtp" ) 
            {
                $this->set_header("Bcc", implode(", ", $bcc));
            }
            else
            {
                $this->_bcc_array = $bcc;
            }

            $this->_build_message();
            $this->_spool_email();
        }
    }

    /**
	 * Unwrap special elements
	 *
	 * @return	void
	 */

    protected function _unwrap_specials()
    {
        $this->_finalbody = preg_replace_callback("/\\{unwrap\\}(.*?)\\{\\/unwrap\\}/si", array( $this, "_remove_nl_callback" ), $this->_finalbody);
    }

    /**
	 * Strip line-breaks via callback
	 *
	 * @param	string	$matches
	 * @return	string
	 */

    protected function _remove_nl_callback($matches)
    {
        if( strpos($matches[1], "\r") !== false || strpos($matches[1], "\n") !== false ) 
        {
            $matches[1] = str_replace(array( "\r\n", "\r", "\n" ), "", $matches[1]);
        }

        return $matches[1];
    }

    /**
	 * Spool mail to the mail server
	 *
	 * @return	bool
	 */

    protected function _spool_email()
    {
        $this->_unwrap_specials();
        $protocol = $this->_get_protocol();
        $method = "_send_with_" . $protocol;
        if( !$this->$method() ) 
        {
            $this->_set_error_message("lang:email_send_failure_" . (($protocol === "mail" ? "phpmail" : $protocol)));
            return false;
        }

        $this->_set_error_message("lang:email_sent", $protocol);
        return true;
    }

    /**
	 * Validate email for shell
	 *
	 * Applies stricter, shell-safe validation to email addresses.
	 * Introduced to prevent RCE via sendmail's -f option.
	 *
	 * @see	https://github.com/bcit-ci/CodeIgniter/issues/4963
	 * @see	https://gist.github.com/Zenexer/40d02da5e07f151adeaeeaa11af9ab36
	 * @license	https://creativecommons.org/publicdomain/zero/1.0/	CC0 1.0, Public Domain
	 *
	 * Credits for the base concept go to Paul Buonopane <paul@namepros.com>
	 *
	 * @param	string	$email
	 * @return	bool
	 */

    protected function _validate_email_for_shell(&$email)
    {
        if( function_exists("idn_to_ascii") && ($atpos = strpos($email, "@")) ) 
        {
            $email = self::substr($email, 0, ++$atpos) . idn_to_ascii(self::substr($email, $atpos), 0, INTL_IDNA_VARIANT_UTS46);
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) === $email && preg_match("#\\A[a-z0-9._+-]+@[a-z0-9.-]{1,253}\\z#i", $email);
    }

    /**
	 * Send using mail()
	 *
	 * @return	bool
	 */

    protected function _send_with_mail()
    {
        if( is_array($this->_recipients) ) 
        {
            $this->_recipients = implode(", ", $this->_recipients);
        }

        $from = $this->clean_email($this->_headers["Return-Path"]);
        if( !$this->_validate_email_for_shell($from) ) 
        {
            return mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str);
        }

        return mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str, "-f " . $from);
    }

    /**
	 * Send using Sendmail
	 *
	 * @return	bool
	 */

    protected function _send_with_sendmail()
    {
        $from = $this->clean_email($this->_headers["From"]);
        if( $this->_validate_email_for_shell($from) ) 
        {
            $from = "-f " . $from;
        }
        else
        {
            $from = "";
        }

        if( !function_usable("popen") || false === ($fp = @popen($this->mailpath . " -oi " . $from . " -t", "w")) ) 
        {
            return false;
        }

        fputs($fp, $this->_header_str);
        fputs($fp, $this->_finalbody);
        $status = pclose($fp);
        if( $status !== 0 ) 
        {
            $this->_set_error_message("lang:email_exit_status", $status);
            $this->_set_error_message("lang:email_no_socket");
            return false;
        }

        return true;
    }

    /**
	 * Send using SMTP
	 *
	 * @return	bool
	 */

    protected function _send_with_smtp()
    {
        if( $this->smtp_host === "" ) 
        {
            $this->_set_error_message("lang:email_no_hostname");
            return false;
        }

        if( !$this->_smtp_connect() || !$this->_smtp_authenticate() ) 
        {
            return false;
        }

        if( !$this->_send_command("from", $this->clean_email($this->_headers["From"])) ) 
        {
            $this->_smtp_end();
            return false;
        }

        foreach( $this->_recipients as $val ) 
        {
            if( !$this->_send_command("to", $val) ) 
            {
                $this->_smtp_end();
                return false;
            }

        }
        foreach( $this->_cc_array as $val ) 
        {
            if( $val !== "" && !$this->_send_command("to", $val) ) 
            {
                $this->_smtp_end();
                return false;
            }

        }
        foreach( $this->_bcc_array as $val ) 
        {
            if( $val !== "" && !$this->_send_command("to", $val) ) 
            {
                $this->_smtp_end();
                return false;
            }

        }
        if( !$this->_send_command("data") ) 
        {
            $this->_smtp_end();
            return false;
        }

        $this->_send_data($this->_header_str . preg_replace("/^\\./m", "..\$1", $this->_finalbody));
        $this->_send_data(".");
        $reply = $this->_get_smtp_data();
        $this->_set_error_message($reply);
        $this->_smtp_end();
        if( strpos($reply, "250") !== 0 ) 
        {
            $this->_set_error_message("lang:email_smtp_error", $reply);
            return false;
        }

        return true;
    }

    /**
	 * SMTP End
	 *
	 * Shortcut to send RSET or QUIT depending on keep-alive
	 *
	 * @return	void
	 */

    protected function _smtp_end()
    {
        $this->_send_command(($this->smtp_keepalive ? "reset" : "quit"));
    }

    /**
	 * SMTP Connect
	 *
	 * @return	string
	 */

    protected function _smtp_connect()
    {
        if( is_resource($this->_smtp_connect) ) 
        {
            return true;
        }

        $ssl = ($this->smtp_crypto === "ssl" ? "ssl://" : "");
        $this->_smtp_connect = fsockopen($ssl . $this->smtp_host, $this->smtp_port, $errno, $errstr, $this->smtp_timeout);
        if( !is_resource($this->_smtp_connect) ) 
        {
            $this->_set_error_message("lang:email_smtp_error", $errno . " " . $errstr);
            return false;
        }

        stream_set_timeout($this->_smtp_connect, $this->smtp_timeout);
        $this->_set_error_message($this->_get_smtp_data());
        if( $this->smtp_crypto === "tls" ) 
        {
            $this->_send_command("hello");
            $this->_send_command("starttls");
            $method = (is_php("5.6") ? STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT : STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $crypto = stream_socket_enable_crypto($this->_smtp_connect, true, $method);
            if( $crypto !== true ) 
            {
                $this->_set_error_message("lang:email_smtp_error", $this->_get_smtp_data());
                return false;
            }

        }

        return $this->_send_command("hello");
    }

    /**
	 * Send SMTP command
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */

    protected function _send_command($cmd, $data = "")
    {
        switch( $cmd ) 
        {
            case "hello":
                if( $this->_smtp_auth || $this->_get_encoding() === "8bit" ) 
                {
                    $this->_send_data("EHLO " . $this->_get_hostname());
                }
                else
                {
                    $this->_send_data("HELO " . $this->_get_hostname());
                }

                $resp = 250;
                break;
            case "starttls":
                $this->_send_data("STARTTLS");
                $resp = 220;
                break;
            case "from":
                $this->_send_data("MAIL FROM:<" . $data . ">");
                $resp = 250;
                break;
            case "to":
                if( $this->dsn ) 
                {
                    $this->_send_data("RCPT TO:<" . $data . "> NOTIFY=SUCCESS,DELAY,FAILURE ORCPT=rfc822;" . $data);
                }
                else
                {
                    $this->_send_data("RCPT TO:<" . $data . ">");
                }

                $resp = 250;
                break;
            case "data":
                $this->_send_data("DATA");
                $resp = 354;
                break;
            case "reset":
                $this->_send_data("RSET");
                $resp = 250;
                break;
            case "quit":
                $this->_send_data("QUIT");
                $resp = 221;
                break;
        }
        $reply = $this->_get_smtp_data();
        $this->_debug_msg[] = "<pre>" . $cmd . ": " . $reply . "</pre>";
        if( (int) self::substr($reply, 0, 3) !== $resp ) 
        {
            $this->_set_error_message("lang:email_smtp_error", $reply);
            return false;
        }

        if( $cmd === "quit" ) 
        {
            fclose($this->_smtp_connect);
        }

        return true;
    }

    /**
	 * SMTP Authenticate
	 *
	 * @return	bool
	 */

    protected function _smtp_authenticate()
    {
        if( !$this->_smtp_auth ) 
        {
            return true;
        }

        if( $this->smtp_user === "" && $this->smtp_pass === "" ) 
        {
            $this->_set_error_message("lang:email_no_smtp_unpw");
            return false;
        }

        $this->_send_data("AUTH LOGIN");
        $reply = $this->_get_smtp_data();
        if( strpos($reply, "503") === 0 ) 
        {
            return true;
        }

        if( strpos($reply, "334") !== 0 ) 
        {
            $this->_set_error_message("lang:email_failed_smtp_login", $reply);
            return false;
        }

        $this->_send_data(base64_encode($this->smtp_user));
        $reply = $this->_get_smtp_data();
        if( strpos($reply, "334") !== 0 ) 
        {
            $this->_set_error_message("lang:email_smtp_auth_un", $reply);
            return false;
        }

        $this->_send_data(base64_encode($this->smtp_pass));
        $reply = $this->_get_smtp_data();
        if( strpos($reply, "235") !== 0 ) 
        {
            $this->_set_error_message("lang:email_smtp_auth_pw", $reply);
            return false;
        }

        if( $this->smtp_keepalive ) 
        {
            $this->_smtp_auth = false;
        }

        return true;
    }

    /**
	 * Send SMTP data
	 *
	 * @param	string	$data
	 * @return	bool
	 */

    protected function _send_data($data)
    {
        $data .= $this->newline;
        $written = $timestamp = 0;
        $length = self::strlen($data);
        while( $written < $length ) 
        {
            if( ($result = fwrite($this->_smtp_connect, self::substr($data, $written))) === false ) 
            {
                break;
            }

            if( $result === 0 ) 
            {
                if( $timestamp === 0 ) 
                {
                    $timestamp = time();
                }
                else
                {
                    if( $timestamp < time() - $this->smtp_timeout ) 
                    {
                        $result = false;
                        break;
                    }

                }

                usleep(250000);
                continue;
            }

            $timestamp = 0;
            $written += $result;
        }
        if( $result === false ) 
        {
            $this->_set_error_message("lang:email_smtp_data_failure", $data);
            return false;
        }

        return true;
    }

    /**
	 * Get SMTP data
	 *
	 * @return	string
	 */

    protected function _get_smtp_data()
    {
        $data = "";
        while( $str = fgets($this->_smtp_connect, 512) ) 
        {
            $data .= $str;
            if( $str[3] === " " ) 
            {
                break;
            }

        }
        return $data;
    }

    /**
	 * Get Hostname
	 *
	 * There are only two legal types of hostname - either a fully
	 * qualified domain name (eg: "mail.example.com") or an IP literal
	 * (eg: "[1.2.3.4]").
	 *
	 * @link	https://tools.ietf.org/html/rfc5321#section-2.3.5
	 * @link	http://cbl.abuseat.org/namingproblems.html
	 * @return	string
	 */

    protected function _get_hostname()
    {
        if( isset($_SERVER["SERVER_NAME"]) ) 
        {
            return $_SERVER["SERVER_NAME"];
        }

        return (isset($_SERVER["SERVER_ADDR"]) ? "[" . $_SERVER["SERVER_ADDR"] . "]" : "[127.0.0.1]");
    }

    /**
	 * Get Debug Message
	 *
	 * @param	array	$include	List of raw data chunks to include in the output
	 *					Valid options are: 'headers', 'subject', 'body'
	 * @return	string
	 */

    public function print_debugger($include = array(  ))
    {
        $msg = implode("", $this->_debug_msg);
        $raw_data = "";
        is_array($include) or in_array("headers", $include, true) and htmlspecialchars($this->_header_str);
        in_array("subject", $include, true) and htmlspecialchars($this->_subject);
        in_array("body", $include, true) and htmlspecialchars($this->_finalbody);
        return $msg . (($raw_data === "" ? "" : "<pre>" . $raw_data . "</pre>"));
    }

    /**
	 * Set Message
	 *
	 * @param	string	$msg
	 * @param	string	$val = ''
	 * @return	void
	 */

    protected function _set_error_message($msg, $val = "")
    {
        $CI =& get_instance();
        $CI->lang->load("email");
        if( sscanf($msg, "lang:%s", $line) !== 1 || false === ($line = $CI->lang->line($line)) ) 
        {
            $this->_debug_msg[] = str_replace("%s", $val, $msg) . "<br />";
        }
        else
        {
            $this->_debug_msg[] = str_replace("%s", $val, $line) . "<br />";
        }

    }

    /**
	 * Mime Types
	 *
	 * @param	string
	 * @return	string
	 */

    protected function _mime_types($ext = "")
    {
        $ext = strtolower($ext);
        $mimes =& get_mimes();
        if( isset($mimes[$ext]) ) 
        {
            return (is_array($mimes[$ext]) ? current($mimes[$ext]) : $mimes[$ext]);
        }

        return "application/x-unknown-content-type";
    }

    /**
	 * Destructor
	 *
	 * @return	void
	 */

    public function __destruct()
    {
        is_resource($this->_smtp_connect) and $this->_send_command("quit");
    }

    /**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */

    protected static function strlen($str)
    {
        return (self::$func_overload ? mb_strlen($str, "8bit") : strlen($str));
    }

    /**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */

    protected static function substr($str, $start, $length = NULL)
    {
        if( self::$func_overload ) 
        {
            return mb_substr($str, $start, $length, "8bit");
        }

        return (isset($length) ? substr($str, $start, $length) : substr($str, $start));
    }

}


