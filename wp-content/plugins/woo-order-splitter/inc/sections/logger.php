<?php
	global $wc_os_settings;
	$wc_os_ie = (array_key_exists('wc_os_ie', $wc_os_settings)?$wc_os_settings['wc_os_ie']:'');
	$wc_os_logger = wc_os_logger('email');
	$wc_os_debug_logger = wc_os_logger('debug');
	$wc_os_remove_order_log = wc_os_logger('order');

	
?>
<form class="nav-tab-content wc_os_logger hides tab-logs" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">

    <h3 class="nav-tab-wrapper">

        <a class="nav-tab nav-tab-active" data-selection="email_log"><?php _e("Email Log",'woo-order-splitter'); ?></a>

        <a class="nav-tab" data-selection="order_log"><?php _e("Removed Order Log",'woo-order-splitter'); ?></a>
        
        <a class="nav-tab" data-selection="debug_log"><?php _e("Debug Log",'woo-order-splitter'); ?></a>

        <a title="<?php echo date('d M, Y h:i:s A'); ?>" class="email-log-toggle <?php echo get_option('wc_os_email_log')?'selected':''; ?>" data-status="<?php echo get_option('wc_os_email_log')?'yes':'no'; ?>"><i class="fas fa-circle"></i> <?php _e('Enable/Disable Email Log', 'woo-order-splitter'); ?></a>
        <a title="<?php echo date('d M, Y h:i:s A'); ?>" class="debug-log-toggle <?php echo get_option('wc_os_debug_log')?'selected':''; ?>" data-status="<?php echo get_option('wc_os_debug_log')?'yes':'no'; ?>"><i class="fas fa-circle"></i> <?php _e('Enable/Disable Debug Log', 'woo-order-splitter'); ?></a>
        <a class="order-log-toggle <?php echo get_option('wc_os_order_log')?'selected':''; ?>" data-status="<?php echo get_option('wc_os_order_log')?'yes':'no'; ?>"><i class="fas fa-circle"></i> <?php _e('Enable/Disable Order Log', 'woo-order-splitter'); ?></a>

    </h3>


    <div class="sub-tab-content">

<?php  ?>
        <br /><strong><i class="fas fa-envelope-open-text"></i> <?php _e('Email Log', 'woo-order-splitter'); ?>:</strong>
        <?php if(!empty($wc_os_logger)): ?>
            <div style="float: right"><a class="wc_os_email_clear_log"><?php _e('Clear Email Log', 'woo-order-splitter'); ?> <i class="fas fa-trash"></i></a> </div>
        <?php endif; ?>
        <br />
        <?php
		
		if(function_exists('wc_os_logger_extended')){
			wc_os_logger_extended();
		}
        
        if(!empty($wc_os_logger)){
            krsort($wc_os_logger);
            ?>
            <ul class="email_log">
                <?php
                foreach($wc_os_logger as $log){
                    ?>
                    <li>
					<?php 
					if(is_array($log) || is_object($log)){
						pree($log);
					}else{
						echo $log;
					}
					?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>

    </div>

    <div class="sub-tab-content hides">

        <br /><strong><?php _e('Removed Order Log', 'woo-order-splitter'); ?>:</strong>
        <?php if(!empty($wc_os_remove_order_log)): ?>
            <div style="float: right"><a class="wc_os_clear_order_log"><?php _e('Clear Order Log', 'woo-order-splitter'); ?> <i class="fas fa-trash"></i></a> </div>
        <?php endif; ?>
        <br />
        <?php
        if(!empty($wc_os_remove_order_log)){
            krsort($wc_os_remove_order_log);
            ?>
            <ul class="order_log">
                <?php
				
                foreach($wc_os_remove_order_log as $order_id => $order_log){
                    ?>
                    <li><?php 
						if(is_array($order_log)){
							wc_os_pree($order_log);
						}else{
							echo $order_log; 
						}
					?></li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>


    </div>
    
	<div class="sub-tab-content debug-log hides">
        



		<br /><strong><i class="fas fa-bug"></i> <?php _e('Debug Log', 'woo-order-splitter'); ?>:</strong>
        <?php if(!empty($wc_os_debug_logger)): ?>
            <div style="float: right"><a class="wc_os_debug_clear_log"><?php _e('Clear Debug Log', 'woo-order-splitter'); ?> <i class="fas fa-trash"></i></a> </div>
        <?php endif; ?>
        <br />
        <?php
		
        if(!empty($wc_os_debug_logger)){
            
            ?>
           
                <?php
				$wc_os_debug_method_based = array();
				if(array_key_exists($wc_os_ie, $wc_os_debug_logger)){ $wc_os_debug_method_based = $wc_os_debug_logger[$wc_os_ie]; ksort($wc_os_debug_method_based); }
				if(!empty($wc_os_debug_method_based)){
?>
				
<h2 class="method-based-logger"><?php echo $wc_os_ie; ?></h2>
                 <ul class="debug_log">
<?php					
				
					
				
				
				
				foreach($wc_os_debug_method_based as $method_key=>$log){
                    ?>
                    <li>
                    <?php 
					if(is_array($log) || is_object($log)){
						pree($method_key);
						pree($log);
					}else{
						echo $log;
					}
					?>
                    </li>
                    <?php
				}
?>
				</ul>
<hr />                
<?php					
				}
?>

<?php				
				if(array_key_exists($wc_os_ie, $wc_os_debug_logger)){ unset($wc_os_debug_logger[$wc_os_ie]); }
				krsort($wc_os_debug_logger);
?>
<ul class="debug_log">
<?php				
                foreach($wc_os_debug_logger as $method_key=>$log){
                    ?>
                    
                    <li>
					<?php 
					if(is_array($log) || is_object($log)){
						pree($method_key);
						pree($log);
					}else{
						echo $log;
					}
					?>
                    </li>
                    <?php
                }
                ?>
</ul>            
            <?php
        }
        ?><br /><br />



<?php
//        WP_CONTENT_DIR
//        WP_CONTENT_URL


       $wos_debug_log = WP_CONTENT_DIR.'/debug.log';
	   $wos_debug_log_url = WP_CONTENT_URL.'/debug.log';
	   
	   $wos_error_log = str_replace('/wp-content', '', WP_CONTENT_DIR.'/error_log');
	   $wos_wp_config = str_replace('/wp-content', '', WP_CONTENT_DIR.'/wp-config.php');
	   $wos_error_log_url = str_replace('/wp-content', '', WP_CONTENT_URL.'/error_log');
       
	   

       $custom_log_status = false;
       $default_log_status = false;




       if(is_string(WP_DEBUG_LOG) && file_exists(WP_DEBUG_LOG)){

           $custom_log_path = str_replace( '\\', '/', WP_DEBUG_LOG );
           $home_url = home_url();
           $home_dir = get_home_path();


           $is_valid_debug = strpos($custom_log_path, $home_dir);

           if($is_valid_debug !== false){
               $custom_log = str_replace($home_dir, $home_url.'/', $custom_log_path);
               $file_name = basename($custom_log);
               ?>

               <div>
                   <strong><?php _e('Debug Log File(Custom Path)', 'woo-order-splitter'); ?>:</strong>

<div class="alert alert-secondary" role="alert">
<?php echo "<a href='$custom_log' target='_blank'>$file_name</a>"; ?>
</div>

               </div>

               <?php

               $custom_status = true;

           }

       }
?>
<div>
    <strong><?php _e('Debug Log File (Default)', 'woo-order-splitter'); ?>: <small><?php echo $wos_debug_log; ?></small></strong>
<?php
        if(file_exists($wos_debug_log)){
           
?>
<div class="alert alert-secondary" role="alert">
<?php  echo "<a href='$wos_debug_log_url' class='wc-os-debug-log' target='_blank'><i class='fas fa-file-download'></i> debug.log</a>"; ?>
</div>
<?php	
            $default_log_status = true;

        }else{
		
			if(!$custom_log_status && !$default_log_status){				
?>
<div class="alert alert-secondary" role="alert">
<?php _e('Debug Log file not found.', 'woo-order-splitter'); ?>
</div>
<?php			 
				
			}
			
		}

?>
</div>

<div>
<strong><?php _e('Error Log File (Default)', 'woo-order-splitter'); ?>: <small><?php echo $wos_error_log; ?></small></strong>
<?php
        if(file_exists($wos_error_log)){
?>
<div class="alert alert-secondary" role="alert">
<?php			
            echo "<a href='$wos_error_log_url' class='wc-os-debug-log' target='_blank'><i class='fas fa-file-download'></i> error_log</a>";
?>
</div>
<?php			
		}else{			 
?>
<div class="alert alert-secondary" role="alert">
<?php _e('Error Log file not found.', 'woo-order-splitter'); ?>
</div>
<?php			 
		}
?>
</div>

<div>
    <strong><?php _e('DEBUG CONSTANTS', 'woo-order-splitter'); ?>: <small><?php echo $wos_wp_config; ?></small></strong>
    <div class="alert alert-secondary" role="alert">
            <ul class="debug-constants">
                <li>define('WP_DEBUG', <?php echo WP_DEBUG?'<span class="wc-os-green">true</span>':'<span class="wc-os-red">false</span>'; ?>);</li>
                <li>define('WP_DEBUG_LOG', <?php echo WP_DEBUG_LOG?'<span class="wc-os-green">true</span>':'<span class="wc-os-red">false</span>'; ?>);</li>
                <li>define('WP_DEBUG_DISPLAY', <?php echo WP_DEBUG_DISPLAY?'<span class="wc-os-green">true</span>':'<span class="wc-os-red">false</span>'; ?>);</li>
            </ul>
    </div>        
</div>        

    </div>    

</form>