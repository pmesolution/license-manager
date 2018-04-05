<?php
/**
 * Plugin Name: License manager
 * Plugin URI: http://pmesolution.ca
 * Description: License manager PME Solution.
 * Version: 0.0
 * Author: PME Solution
 * Author URI: http://pmesolution.ca
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/


/*
* The register_activation_hook function registers a plugin function to be run when the plugin is activated.
*/

register_activation_hook(__FILE__, 'pme_extension_activate');
function pme_extension_activate()
{   

    global $wpdb;


    update_option('pme_extension_activated', 'yes');

    //create database table and fields

    $wpdb->query('CREATE TABLE IF NOT EXISTS `pmesolution_licenses` (`id` INT PRIMARY KEY AUTO_INCREMENT, `siteid` INT,  `client` VARCHAR(255), `produit` VARCHAR(255), `date_achat` VARCHAR(255), `expiration` VARCHAR(255), `key` TEXT , `status` VARCHAR(255))');
}


register_deactivation_hook(__FILE__, 'pme_extension_deactivate');

function pme_extension_deactivate(){
    delete_option('pme_extension_activated');

    global $wpdb;

    $wpdb->query('DROP TABLE `pmesolution_licenses`');
}

/*
* Activator Class is used for extension activation and deactivation
*/

class MainWPPMELicenseManager
{
    protected $mainwpMainActivated = false;
    protected $childEnabled = false;
    protected $childKey = false;
    protected $childFile;
    protected $plugin_handle = "mainwp-extension";
    
    public function __construct()
    {
        $this->childFile = __FILE__;
        add_filter('mainwp-getextensions', array(&$this, 'get_this_extension'));
        
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_slug = plugin_basename(__FILE__);
        
        wp_enqueue_style('mainwp-extension-css', $this->plugin_url . 'css/style.css');
        wp_enqueue_style('mainwp-extension-css', $this->plugin_url . 'css/jquery-ui.min.css');

        wp_enqueue_script('mainwp-extension-js', $this->plugin_url . 'js/default.js');
        wp_enqueue_script('mainwp-extension-js', $this->plugin_url . 'js/jquery-ui.min.js');

        // This filter will return true if the main plugin is activated
        $this->mainwpMainActivated = apply_filters('mainwp-activated-check', false);
        if ($this->mainwpMainActivated !== false)
        {
            $this->activate_this_plugin();
        }
        else
        {
            //Because sometimes our main plugin is activated after the extension plugin is activated we also have a second step, 
            //listening to the 'mainwp-activated' action. This action is triggered by MainWP after initialisation. 
            add_action('mainwp-activated', array(&$this, 'activate_this_plugin'));
        }
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_notices', array(&$this, 'mainwp_error_notice'));
    }
    function admin_init() {
        if (get_option('pme_extension_activated') == 'yes')
        {
            delete_option('pme_extension_activated');
            wp_redirect(admin_url('admin.php?page=Extensions'));
            return;
        }        
    }
    function mainwp_extension_autoload($class_name)
	{
	    $allowedLoadingTypes = array('class', 'page');
	
	    foreach ($allowedLoadingTypes as $allowedLoadingType)
	    {
	        $class_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)) . $allowedLoadingType . DIRECTORY_SEPARATOR . $class_name . '.' . $allowedLoadingType . '.php';
	        if (file_exists($class_file))
	        {
	            require_once($class_file);
	        }
	    }
	}

    function get_this_extension($pArray)
    {
        $pArray[] = array('plugin' => __FILE__,  'api' => 'mainwp-example-extension', 'mainwp' => false, 'callback' => array(&$this, 'settings'));
        return $pArray;
    }
    
    function settings()
    {
        //The "mainwp-pageheader-extensions" action is used to render the tabs on the Extensions screen. 
        //It's used together with mainwp-pagefooter-extensions and mainwp-getextensions
        do_action('mainwp-pageheader-extensions', __FILE__);
        if ($this->childEnabled)
        {
	        $this->mainwp_extension_autoload('MainWPPMELicense');
            MainWPPMELicense::renderPage();
        }
        else
        {
            ?><div class="mainwp_info-box-yellow"><?php _e("The Extension has to be enabled to change the settings."); ?></div><?php
        }
        do_action('mainwp-pagefooter-extensions', __FILE__);
    }
    
    //The function "activate_this_plugin" is called when the main is initialized. 
    function activate_this_plugin()
    {
        //Checking if the MainWP plugin is enabled. This filter will return true if the main plugin is activated.
        $this->mainwpMainActivated = apply_filters('mainwp-activated-check', $this->mainwpMainActivated);
        
        // The 'mainwp-extension-enabled-check' hook. If the plugin is not enabled this will return false, 
        // if the plugin is enabled, an array will be returned containing a key. 
        // This key is used for some data requests to our main
        $this->childEnabled = apply_filters('mainwp-extension-enabled-check', __FILE__);
        
        if (!$this->childEnabled) return;
        $this->childKey = $this->childEnabled['key'];
    }
    function mainwp_error_notice()
    {
        global $current_screen;
        if ($current_screen->parent_base == 'plugins' && $this->mainwpMainActivated == false) // Simple enough - install and activate MainWP or you'll get an error
        {
            echo '<div class="error"><p>MainWP Alerts Extension ' . __('requires '). '<a href="http://mainwp.com/" target="_blank">MainWP</a>'. __(' Plugin to be activated in order to work. Please install and activate') . '<a href="http://mainwp.com/" target="_blank">MainWP</a> '.__('first.') . '</p></div>';
        }
    }
    public function getChildKey()
    {
        return $this->childKey;
    }
    public function getChildFile()
    {
        return $this->childFile;
    }
}
$MainWPPMELicenseManager = new MainWPPMELicenseManager();

?>