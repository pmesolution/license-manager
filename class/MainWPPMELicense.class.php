<?php
class MainWPPMELicense
{
  
    public function __construct()
    {
        add_action('init', array(&$this, 'init'));
        add_action('admin_init', array(&$this, 'admin_init'));
    }
	
    public function init()
    {
    }
    /*
    * Create your extension page
    */
    public function renderPage() {


        global $MainWPPMELicenseManager;

        global $wpdb;



        //adding client to database 

        if(isset($_POST['add_client'])){

            if(isset($_POST['modify_id'])){
                $mid=$_POST['modify_id'];
                $name=$_POST['client'];
                $produit = $_POST['produit'];
                $achat= $_POST['date_achat'];
                $expiration = $_POST['expiration'];
                $cle = $_POST['key'];
                $status= $_POST['status'];

                $sql = "UPDATE `pmesolution_licenses` SET `client`=\"$name\", `produit`=\"$produit\", `date_achat`=\"$achat\", `expiration`=\"$expiration\", `key`=\"$cle\", `status`=\"$status\" WHERE `id`=$mid";

            }else {
                $name=$_POST['client'];
                $produit = $_POST['produit'];
                $achat= $_POST['date_achat'];
                $expiration = $_POST['expiration'];
                $cle = $_POST['key'];
                $status= $_POST['status'];
                $siteid = $_POST['siteid'];

                if($siteid == "nosite"){
                    $siteid = NULL;
                }

                $sql  = "INSERT INTO `pmesolution_licenses`  VALUES (NULL, \"$siteid\" ,\"$name\" ,\"$produit\", \"$achat\", \"$expiration\", \"$cle\", \"$status\")";

            }

        	
        	//echo $sql ;

        	$wpdb->query($sql);


        }


        //delete a row 

        if(isset($_POST['delete'])){
        	$id= $_POST['delete'];
        	$wpdb->query("DELETE FROM `pmesolution_licenses` WHERE id=$id");
        }


        //activate 
        if(isset($_POST['activate_id'])){
            $webtoactivatedid = $_POST['activate'];

            $smo = MainWPPMELicense::get_childkey();

            $arraysnippet = array( //this removes the snippet after we're done with it, based on the checkbox in the form.

                                        'action' => 'delete_snippet',
                                        'type' => 'B',
                                        'slug' => 'updateapi',
                                        'code' => "function insertapikey(){
    update_site_option('et_account_status', 'active');

    $updates_options = get_site_option( 'et_automatic_updates_options', array() );

    update_site_option( 'et_automatic_updates_options', array_merge( $updates_options, array('username' =>'pmesolution', 
            'api_key'  => '265d44dd8f77878512586896fa1d727897ead369') ) );
} insertapikey();",
                            
                        );

            $information = apply_filters('mainwp_fetchurlauthed', __FILE__, $smo, $webtoactivatedid, 'code_snippet', $arraysnippet);

            if ($information['status'] == "SUCCESS") {echo "<p>The API key has been activated to your child site!</p>";}
        }

        //deactivate 
        if(isset($_POST['deactivate_id'])){
            $webtoactivatedid = $_POST['deactivate'];

            $smo = MainWPPMELicense::get_childkey();

            $arraysnippet = array( //this removes the snippet after we're done with it, based on the checkbox in the form.

                                        'action' => 'delete_snippet',
                                        'type' => 'B',
                                        'slug' => 'updateapi',
                                        'code' => "function deleteapikey(){
    update_site_option('et_account_status', '');

    $updates_options = get_site_option( 'et_automatic_updates_options', array() );

    update_site_option( 'et_automatic_updates_options','') ) );
} deleteapikey();",
                            
            );

            $information = apply_filters('mainwp_fetchurlauthed', __FILE__, $smo, $webtoactivatedid, 'code_snippet', $arraysnippet);

            if ($information['status'] == "SUCCESS") {echo "<p>The API key has been deactivated to your child site!</p>";}
        }

           /* $smo = MainWPPMELicense::get_childkey();
        var_dump($smo);*/
           $dbsql= MainWP_DB::Instance()->getSQLWebsitesForCurrentUser();
          // var_dump($dbsql);

          $dbres = MainWP_DB::Instance()->query($dbsql);
          //var_dump($dbres);



         ?>
        
        <h1>Clients</h1>
        <?php if(isset($_GET['modify_id'])) {
            $mid = $_GET['modify_id'];
            $item = $wpdb->get_row("SELECT * FROM `pmesolution_licenses` WHERE id=$mid");
            
        ?>


            <div class="client-add-section editbox" style="display:block">
                <form action="<?php echo admin_url( 'admin.php?page=Extensions-Pmesolution'); ?>" method="post" class="editboxform">
                    <input type="hidden" name="modify_id" value="<?php echo $item->id; ?>">
                    <label>Client</label><input type="text" name="client" required value="<?php echo $item->client; ?>">
                    <label>Produit</label>
                    <select name="produit">
                        <option value="divi">Divi</option>
                        <option value="wpml">WPML</option>
                    </select>
                    <label>Date d'achat</label><input required class="datepicker"type="text" name="date_achat" value="<?php echo $item->date_achat; ?>">
                    <label>Expiration</label><input required type="text" class="datepicker" name="expiration" value="<?php echo $item->expiration; ?>" >
                    <label>Clé</label><textarea name="key"><?php echo $item->key; ?></textarea>
                    <label>Status</label><select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <input type="submit"  value="Save" name="add_client" class="button-primary">
                </form>
            </div>
        <?php } else { ?>
             
        <div class="client-add-section">
           
            <form action="<?php echo admin_url( 'admin.php?page=Extensions-Pmesolution'); ?>" method="post" class="dialogbox">
                <label>Client</label><input type="text" name="client" id="client" required>
                <label>Produit</label>
                <select name="produit">
                    <option value="divi">Divi</option>
                    <option value="wpml">WPML</option>
                </select>
                <label>Date d'achat</label><input required class="datepicker"type="text" name="date_achat">
                <label>Expiration</label><input required type="text" class="datepicker" name="expiration">
                <label>Clé</label><textarea name="key"></textarea>
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <label>Site</label>
                    <select name="siteid" id="siteid">
                        <option value="nosite">Pas un Mainwp child</option>
                        <?php foreach($dbres as $webs){ ?>
                        <option value="<?php echo $webs['id']; ?>"><?php echo $webs['url']; ?></option>
                        <?php }  ?>
                    </select>
                <input type="submit"  value="Save" name="add_client" class="button-primary">
            </form>
         </div>
        <button id="adding" class="button-primary">Add new user</button>


       	<?php } ?>

        
        <hr/>
        <table role="table" class="table clientstable wp-list-table widefat fixed striped">
            <thead>
            	<tr><th class="manage-column" >Client</th>
            		<th class="manage-column" >Produit</th>
            		<th class="manage-column" >Date d'achat</th>
            		<th class="manage-column" >Expiration</th>
            		<th class="manage-column" >Clé</th>
            		<th class="manage-column" >Status</th>
            		<th class="manage-column" >Modifier</th>
                    <th class="manage-column" >Action</th>
            		<th class="manage-column" >Supprimer</th>
            	</tr>
            </thead>
            <tbody id="the-list">

        	<?php 
        		$list = $wpdb->get_results("SELECT * FROM `pmesolution_licenses`");

        		foreach ($list as $customer){
        	?>
        	<tr>
        		<td > <?php echo $customer->client; ?> </td>
        		<td ><?php echo $customer->produit; ?></td>
        		<td ><?php echo $customer->date_achat; ?></td>
        		<td ><?php echo $customer->expiration; ?></td>
        		<td ><?php echo $customer->key; ?></td>
        		<td ><?php echo $customer->status; ?></td>
        		<td ><a href="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?><?php echo '&modify_id='.$customer->id;?>" > Edit</a></td>
                <?php 
                    //conditions siteid != null, inactive 
                    



                    if($customer->siteid != NULL && $customer->siteid != "0" ){
                        if($customer->status == "inactive"){ ?>
                        <td><form  action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?><?php echo '&activate_id='.$customer->id;?>" method="post" ><input type="hidden" name="activate" value="<?php echo $customer->siteid; ?> "><input type="submit" value="Activer"></form></td>
                        <?php }else { ?>
                        <td><form  action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?><?php echo '&deactivate_id='.$customer->id;?>" method="post" ><input type="hidden" name="deactivate" value="<?php echo $customer->siteid; ?> "><input type="submit" value="Désactiver"></form></td>

                    <?php    }
                    }else {
                ?>
                <td><form  action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" ><input type="hidden" name="activate" value="<?php echo $customer->siteid; ?>"><input type="submit" disabled value="Activer/Désactiver"></form></td>
                <?php } ?>
        		<td><form  action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" ><input type="hidden" name="delete" value="<?php echo $customer->id; ?>"><input type="submit" value="X"></form></td>
        	</tr>
        	<?php } ?>
        </tbody>
        </table>

        <?php
        
		if (isset($_POST['updateapi'])) { // this is where the magic happens - when we hit the button, it executes on the child sites.
			if (count($_POST['selected_sites'] > 0)) { //makes sure a child site has been selected
				
				echo '<div class="updated">';
				//print_r($_POST);
				
			    $childKey = MainWPPMELicense::get_childkey();                   
			
			     $sites = $_POST['selected_sites'];
			     foreach ($sites as $site) {
				     $websiteId = $site;
				     if(isset($_POST['clearsnippets'])){
				     	$codetoexecute="";

				     	$post_data2 = array( //this removes the snippet after we're done with it, based on the checkbox in the form.

							            'action' => 'delete_snippet',
							            'type' => 'B',
							            'slug' => 'updateapi',
							            'code' => $codetoexecute,
							
						);

						$information = apply_filters('mainwp_fetchurlauthed', __FILE__, $childKey, $websiteId, 'code_snippet', $post_data2);

				     }else{

					     if (isset($_POST['deleteapi'])) {

					     	$codetoexecute="
			
							function func_deleteapi(){
								update_option('et_automatic_updates_options' , '');
							}
							func_deleteapi();
							
							"; // creates the function we need on the child site, including the text variable.    

					     		$post_data1 = array( //this removes the snippet after we're done with it, based on the checkbox in the form.
							            'action' => 'save_snippet',
							            'type' => 'B',
							            'slug' => 'updateapi',
							            'code' => $codetoexecute,
							
							    );

							    

					     	$information = apply_filters('mainwp_fetchurlauthed', __FILE__, $childKey, $websiteId, 'code_snippet', $post_data1);
						    echo "<p>The Snippets has been cleared from your child site!</p>"; 

					     } else {

					     	$codetoexecute='								
                                function func_updateapi(){
                                    update_option(\'pme_date_license_expire\' , \''.$_POST['newdate'].'\');
                                }
                                func_updateapi();
							';

					     	//echo "<br>Executing on Child site ID#".$websiteId.":<br>";
								$post_data1 = array( // this saves the snippet to the database, where the Child plugin can execute it.
						            'action' => 'save_snippet',
						            'type' => 'B',
						            'slug' => 'updateapi',
						            'code' => $codetoexecute,
						
						        );

						        

						 	$information = apply_filters('mainwp_fetchurlauthed', __FILE__, $childKey, $websiteId, 'code_snippet', $post_data1);
						 								 
						 	if ($information['status'] == "SUCCESS") {echo "<p>The API key has been updated to your child site!</p>";}
					     }
				     }
			     }                     
				 
				 echo "</div>";
			 }
		} //if the form was submitted
			?>
		    <div class="wrap">

		    	
		    	<h2>Expiration date and snippets</h2>
		    	<form action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" id="divikeyform">
		    		<label> Change Expiration date (format: Y-m-d like 2019-02-28)</label><br/>
		    		<input type="text" name="newdate"  value=""/><br/>
		    		<hr/>
		    		<label>Delete Api keys</label><input type="checkbox" name="deleteapi"/>
		    		<hr/>
		    		<br/>
		    		<label>Clear Snippets ( Delete snippets stored in databases  of mainwp child site ) </label><input type="checkbox" name="clearsnippets"/>
		    		<br/>
		    		<input type="submit" name="updateapi"/>

		    		<div id="uploader_select_sites_box" class="mainwp_config_box_right">                                            
		        	<?php do_action('mainwp_select_sites_box', __("Select Sites", 'mainwp'), 'checkbox', true, true, 'mainwp_select_sites_box_right', "", array(), array()); ?>
		    		</div>
                    <div class="clear"></div>

		    	</form>

		      <!--<h1>Test com main to child</h1>
			     
		      <form action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
			    Admin Notice:<input type="text" value="<?php echo $textvar; ?>" name="adminnotice" id="adminnotice" placeholder="hello world"><br />
			    <input name="change-clicked" type="hidden" value="1" />
			    Remove Notice: <input name="remove-clicked" type="checkbox" /><br />
		        <input type="submit" id="postbutton" value="Post Notice" />
		        <div id="uploader_select_sites_box" class="mainwp_config_box_right">                                            
                    <?php// do_action('mainwp_select_sites_box', __("Select Sites", 'mainwp'), 'checkbox', true, true, 'mainwp_select_sites_box_right', "", array(), array()); ?>
		    	</div>
		      </form>
		    </div>-->
			<?php

    }
    
    public function admin_init() //this is where we call external files such as javascript and css files
    {
        
    }
    
    public function get_childkey()
    { // retrieves the childkey as a function that we can call, inside of the class
		global $childEnabled;
	    $childEnabled = apply_filters('mainwp-extension-enabled-check', __FILE__);
	    if (!$childEnabled) return;
	    $childKey = $childEnabled['key'];
		return $childKey;
	}
}

?>
