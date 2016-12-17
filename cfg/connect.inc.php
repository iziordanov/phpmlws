<?php 
	//database connection settings

	define('DB_HOST', 'localhost'); // database host
        define('DB_PORT', '3306'); // database port 
	define('DB_USER', 'ml'); // username
	define('DB_PASS', 'ml'); // password)
	define('DB_NAME', 'ml'); // database name
	define('DB_CONV_REV', '0'); //If require DB Convert and Revert text set to 1
        define('ADMIN', '0'); //administrator role ID
	define('REG_USER', '1'); //user role I
        define('GUEST', '2'); //user role ID
        
        /**********************************************************************/
        define('ERROR_INCORECT_ACCESS', 'Incorect access registered.');
        
        global $themeroll;
        $themeroll = "pepper-grinder";
?>
