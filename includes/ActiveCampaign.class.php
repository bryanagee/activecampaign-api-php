<?php

if ( ! defined("ACTIVECAMPAIGN_URL") || ( ! defined("ACTIVECAMPAIGN_API_KEY") && ! defined("ACTIVECAMPAIGN_API_USER") && ! defined("ACTIVECAMPAIGN_API_PASS"))) {
    require_once(dirname(__FILE__) . "/config.php");
}

class ActiveCampaign extends AC_Connector
{
    /**
     *
     * @var string
     */
    public $url_base;
    
    /**
     *
     * @var string
     */
    public $url;
    
    /**
     *
     * @var string
     */
    public $api_key;
    
    /**
     *
     * @todo verify type
     * @var string
     */
    public $track_email;
    
    /**
     * 
     * @todo verify type
     * @var int TODO: verify type
     */
    public $track_actid;
    
    /**
     *
     * @todo verify type
     * @var string
     */
    public $track_key;
    
    
    /**
     *
     * @var int
     */
    public $version = 1;
    
    /**
     *
     * @var bool
     */
    public $debug   = false;

    /**
     * 
     * @param string $url
     * @param string $api_key
     * @param string $api_user
     * @param string $api_pass
     */
    function __construct($url, $api_key, $api_user = "", $api_pass = "")
    {
        $this->url_base = $this->url = $url;
        $this->api_key  = $api_key;
        parent::__construct($url, $api_key, $api_user, $api_pass);
    }

    /**
     * Sets the API version number to use for requests
     * 
     * @param int $version
     */
    function version($version)
    {
        $this->version = (int) $version;
        if ($version == 2) {
            $this->url_base = $this->url_base . "/2";
        }
    }

    /**
     * 
     * @param string $path
     * @param array $postData
     * @return string
     */
    function api($path, $postData = array())
    {
        // eg: "contact/view" 
        $component  = $this->getComponentFromPath($path);

        // If present, sets query string into $params
        $params     = !preg_match("/\?/", $path) ? "" : substr($path, strpos($path, "?") + 1);
        
        // try block preserves original behavior
        try {
            $method = $this->getMethodFromPath($path);
        } catch (Exception $e) {
            return 'Invalid method.';
        }
        
        // eg: "contact" becomes "Contact"
        $classname = 'AC_' . ucwords($component); 

        // eg: new AC_Contact(...);
        $class = new $classname($this->version, $this->url_base, $this->url, $this->api_key);

        if ($classname === "AC_Tracking") {
            $class->track_email = $this->track_email;
            $class->track_actid = $this->track_actid;
            $class->track_key   = $this->track_key;
        }

        $class->debug = $this->debug;

        // eg: $contact->view()
        $response = $class->$method($params, $postData);
        return $response;
    }
    
    /**
     * 
     * @param string $path
     * @return string
     */
    public function getComponentFromPath($path)
    {
        $component = substr($path, 0, strpos($path,"/"));
        
        switch($component) {
            case 'list':
                return 'list_';
                
            case 'branding':
                return 'design';
                
            case 'singlesignon':
                return 'auth';
                
            case 'sync':
                return 'contact';
        }
        
        return $component;
    }
    
    /**
     * 
     * @param string $path
     * @return string
     * @throws Exception if there is no method portion of the path
     */
    public function getMethodFromPath($path)
    {
        // Special case: the only one method available for "sync" alias to contact
        if (substr($path, 0, 4) === "sync") {
            return "sync";
        }
        
        // Get pattern based on whether there is a query string or not
        $methodPattern = strstr($path, "?") ? "/\/(.*)\?/" : "/\/(.*)$/";
        $matches = array();
        
        // Extract method portion of URL
        preg_match($methodPattern, $path, $matches);
        
        if (empty($matches[1])) {
            throw new Exception("No valid method found");
        }
        
        // eg: tag/add -> tag_add
        $method = preg_replace("/\//", "_", $matches[1]);
        
        if ($method == "list") {
            // reserved word
            return "list_";
        }
        
        return $method;
    }

}

require_once("Account.class.php");
require_once("Auth.class.php");
require_once("Automation.class.php");
require_once("Campaign.class.php");
require_once("Contact.class.php");
require_once("Deal.class.php");
require_once("Design.class.php");
require_once("Form.class.php");
require_once("Group.class.php");
require_once("List.class.php");
require_once("Message.class.php");
require_once("Settings.class.php");
require_once("Subscriber.class.php");
require_once("Tracking.class.php");
require_once("User.class.php");
require_once("Webhook.class.php");
