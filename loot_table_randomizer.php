<?php

// #######################################
// ## Minecraft Loottable Randomizer    ##
// #######################################
// ## Created by: SethBling             ##
// ## Ported to PHP by: Fasguy          ##
// #######################################
// ## Version 1.1.3                     ##
// #######################################
// ## External Sources:
// ## PclZip created by Vincent (http://phpconcept.net/pclzip/)
// ## Minecraft Webfont by South-Paw (https://github.com/South-Paw/Minecraft-Webfont-and-Colors)
// #######################################

session_start();                        //Start a session to store download parameters

define('PCLZIP_TEMPORARY_DIR', realpath(dirname(__FILE__)) . '/tmp/');
require_once("lib/pclzip.lib.php");     //Import PCLZIP (ZipArchive produces unusable zip files (Deflate64 doesn't seem to work correctly in Minecraft))
require_once("lib/functions.php");      //External function file

?>

<html>
	<head>
		<title>SethBling's Loot-Table Randomizer.</title>
	</head>
	<body>
		<link rel="shortcut icon" href="style/favicon.png" type="image/png" />
		<link rel="icon" href="style/favicon.png" type="image/png" />
		<link rel="stylesheet" href="style/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="style/minecraft-webfont.css" />
		<div id="content">
			<h1 id="title">Generating datapack...</h1>
			<h3 id="progress">Please wait...</h3>
			<noscript><h4>Progress can only be shown with JavaScript enabled.</h4></noscript>
			<p id="text">
				How to use:
				1. Let your loot table generate.
				2. Go into <a href="https://help.mojang.com/customer/portal/articles/1480874">your '.minecraft' directory</a>.
				3. Go into 'saves' and then into the world's folder, where you want the drops to be randomized.
				4. Go into 'datapacks' and place the generated zip file in this folder.
				5. Load the world in Minecraft and go nuts!
			</p>
		</div>
	</body>
</html>

<?php

Generate();

function Generate() {
    $seed;                              //Variable to store the seed
    
    $tempFile;                          //File path of the generated zip file
    
    $datapack_name;                     //Variable to store the name of the datapack
    $datapack_desc;                     //Variable to store the description of the datapack
    $datapack_filename;                 //Variable to store the filename of the datapack
    
	$randomLootTable = isset($_POST['randomLootTable']) ? $_POST['randomLootTable'] : array();  //If "randomLootTable" is set, then it assigns the selected objects to $randomLootTable
	if(count($randomLootTable) === 0) {
		echo '<meta http-equiv="refresh" content="0;url=index.php">';
		exit();
	}
    
    ini_set('max_execution_time', 0);   //Pretty much only used for debugging at home

    ignore_user_abort(true);            //Ensure the script keeps running even if the user leaves the page. Failsafe to remove unnecessary files.

    //tempnam should be used here, but apparently isn't supported by my webhoster :/
    while (true) {
        $tempFile = "./tmp/" . uniqid('ltr') . '.tmp';  //Create a unique filename for the temporary file
        if (!file_exists($tempFile)) break;             //*
    }

    Progress(0, "Grabbing seed...");
    if (isset($_POST['seed'])) {                                                    //This entire section is used to make sure the seed is valid.
        if(!empty($_POST['seed'])) {                                                //*
            $seed = (int)$_POST['seed'];                                            //*
            srand($seed);                                                           //*
            $datapack_name = 'random_loot_' . $seed;                                //*
            $datapack_desc = '(Web Version) Loot Table Randomizer, Seed: ' . $seed; //*
        } else {                                                                    //*
            $datapack_name = 'random_loot';                                         //*
	        $datapack_desc = '(Web Version) Loot Table Randomizer';                 //*
        }                                                                           //*
        $datapack_filename = $datapack_name . '.zip';                               //*
    }                                                                               //*

    $_SESSION["file"] = $tempFile;          //Attach tempfile path to the current session for use in 'download.php'
    $_SESSION["name"] = $datapack_filename; //Attach desired filename to the current session for use in 'download.php'
    
    Progress(5, "Grabbing files...");
    $file_list = array();               //Arrays to store the paths of all files
    $remaining = array();               //*

    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('loot_tables/'));   //Recursive iterator to go through all directories in 'loot_tables'
    foreach($dir as $file) {                                                                //For each file/directory in dir
        if ($file->isDir()) continue;                                                       //If current object is a directory, skip
        array_push($file_list, $file->getPathname());                                       //Push filepath into arrays
        array_push($remaining, $file->getPathname());                                       //*
    }

    Progress(10, "Shuffling files...");
    $file_dict = array();               //Array to store the real path and the switching path of files

    foreach($file_list as $file) {              //For each file in file_list
        $i = rand(0, count($remaining) - 1);    //Get random index for switching path of file
        $file_dict[$file] = $remaining[$i];     //Set current file's switching path
        unset($remaining[$i]);                  //Remove switching path from array
        $remaining = array_values($remaining);  //Re-index remaining switching paths (PHP doesn't automatically shift items back)
    }

    Progress(20, "Creating archive...");
    $archive = new PclZip($tempFile);   //Initialize a new zip archive
    $list = $archive->create(array(array(PCLZIP_ATT_FILE_NAME => "pack.mcmeta", PCLZIP_ATT_FILE_CONTENT => json_encode(array("pack" => array("pack_format" => 1, "description" => $datapack_desc)), JSON_PRETTY_PRINT))));              //Add 'pack.mcmeta' to archive
    if ($list == 0) die("ERROR: '" . $archive->errorInfo(true) . "'");                                                                                                                                                                  //If error occured, display it
    $list = $archive->add(array(array(PCLZIP_ATT_FILE_NAME => "IMPORTANT.txt", PCLZIP_ATT_FILE_CONTENT => "DO NOT EXTRACT THE FILES IN THIS .ZIP FILE\nJUST PUT THE ENTIRE .ZIP FILE IN THE 'DATAPACKS' FOLDER")));                 //Add 'HOW TO USE.txt' to archive
    if ($list == 0) die("ERROR: '" . $archive->errorInfo(true) . "'");                                                                                                                                                                  //If error occured, display it
    $list = $archive->add(array(array(PCLZIP_ATT_FILE_NAME => "data/minecraft/tags/functions/load.json", PCLZIP_ATT_FILE_CONTENT => json_encode(array("values" => array($datapack_name . ":reset"))))));                                //Add 'load.json' to archive
    if ($list == 0) die("ERROR: '" . $archive->errorInfo(true) . "'");                                                                                                                                                                  //If error occured, display it
    $list = $archive->add(array(array(PCLZIP_ATT_FILE_NAME => "data/" . $datapack_name . "/functions/reset.mcfunction", PCLZIP_ATT_FILE_CONTENT => 'tellraw @a ["",{"text":"Loot table randomizer by SethBling","color":"green"}]')));  //Add 'reset.mcfunction' to archive
    if ($list == 0) die("ERROR: '" . $archive->errorInfo(true) . "'");                                                                                                                                                                  //If error occured, display it

    $fileCounter = 0;                   //Variable to store current file index (Used to show progress, only visible on pretty slow servers)
    $fileMax = count($file_dict);       //Variable to store the amount of available files
    foreach($file_dict as $file) {      //For each file in file_dict
        Progress(20 + (80 / $fileMax) * $fileCounter, "Adding files to archive...");
        if(in_array(explode(DIRECTORY_SEPARATOR, $file_dict[$file])[1], $randomLootTable)) {                                                            //If the current loot table type should be randomized
            $contents = file_get_contents($file);                                                                                                       //Grab contents of switching file
            $list = $archive->add(array(array(PCLZIP_ATT_FILE_NAME => "data/minecraft/" . $file_dict[$file], PCLZIP_ATT_FILE_CONTENT => $contents)));   //Create real file with switched path file's contents
            if ($list == 0) die("ERROR: '" . $archive->errorInfo(true) . "'");                                                                          //If error occured, display it
        }
        $fileCounter++;                 //Add 1 to the current index
        if (connection_aborted()) {     //If user disconnected...
            Kill();                     //Kill the file and session
            exit();                     //Stop the PHP script
        }
    }

    Progress(100, "Loot table successfully randomized!");

    echo '<meta http-equiv="refresh" content="0;url=download.php">';    //Switch to 'download.php' (Yes, this is an ugly way to do it)

    if (connection_aborted()) Kill();   //Failsafe to make sure no orphaned files and sessions are being left behind
}

function Progress($percentage, $reportText) {
    $percentage = number_format((float)$percentage, 2, '.', '');
    echo "<script id=\"updater\">parent.document.getElementById(\"progress\").innerHTML=\"$percentage% $reportText\";
    parent.document.getElementById(\"updater\").remove();</script>";    //Update progress report
    ob_flush();                                                         //Refresh user interface
    flush();                                                            //*
}

?>