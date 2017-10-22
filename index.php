<?php
  ini_set('display_errors', 'On');
  error_reporting(E_ALL);

  class Manage {
     public static function autoload($class){
        include $class . '.php';
     }
  }
  spl_autoload_register(array('Manage' , 'autoload'));
  $obj = new main();

  class main {
     public function __construct() {
        $pageToLoad = Controller::pageLoader();
        $page = new $pageToLoad;
        Controller::methodLoader($page);
     }
  }

  abstract class page {
     protected $html;
     
     public function __construct() {
        $this->html .= html::sTag('html');
        $this->html .= html::sTag('rel="stylesheet" href="styles.css"');
	$this->html .= html::sTag('body');
     }
     
     public function __destruct() {
        $this->html .= html::eTag('body') . html::eTag('html');
        stringFunctions::printThis($this->html);
     }
     
     public function get() {
        echo 'default get message';
     }

     public function post() {
      	print_r($_POST);
     }
  }

  class homepage extends page {
     public function get() {
	$form = html::sTag('form action="index.php" method="post" enctype="multipart/form-data"');
	$form .= html::sTag('center');
	$form .= html::h1('Select the CSV File to be uploaded:') . html::br();
	$form .= html::input('type="file" name="fileToUpload" id="fileToUpload"');
	$form .= html::br();
	$form .= html::input('type="submit" value="submit"');
	$form .= html::eTag('center');
	$form .= html::eTag('form');
	$this->html .= html::sTag('center') . html::h2('Upload Page') . html::eTag('center');
	$this->html .= $form;						
     }
        
     public function post() {
	$sourcefile = $_FILES["fileToUpload"]["name"];
	$tempname = $_FILES["fileToUpload"]["tmp_name"];
	$filename = uploadfile::csvfileupload($sourcefile,$tempname);
	header('Location:?page=table&filename='.$filename);
     }
  }
  
  class table extends page {
     public function get() {
       $sourcefile=$_GET['filename'];
       echo html::sTag('font color= \"#CB4335 \"') . 'Filename:' . html::eTag('font');
       echo trim($sourcefile,"uploads/"). "<br> The above mentioned file is successfully uploaded<br><br> The Table is as shown below<br><br>";
       $heading = 1;
       $handle = fopen($sourcefile,"r");
       $table = html::sTag('table border="1"');
       while(($data = fgetcsv($handle))!=FALSE) {
          if ($heading == 1) {
	     $table .= html::sTag('thead') . html::sTag('tr');
	     foreach ($data as $value) {
	        if (!isset($value))
	           $value = "&nbsp";
	        else
	           $table .= html::th($value);
             }
	     $table .= html::eTag('tr') . html::eTag('thead') . html::sTag('tbody');
	  }
          else {
	     $table .= html::sTag('tr');
	     foreach ($data as $value) {
		if(!isset($value))
		$value = "&nbsp";
		else
	        $table .= html::td($value);
	     }
	     $table .= html::eTag('tr');
  	  }
	  $heading++;
      }
      $table .= html::eTag('tbody') . html::eTag('table');
      $this->html .= $table;
      fclose($handle);
     }
  } 
  
  class Controller {	 
     public static function pageLoader() {
        if (isset($_REQUEST['page']))  {
           return $_REQUEST['page'];
	}
        else {
           $pageToLoad = 'homepage';
           return $pageToLoad;
	}
     }

     public static function methodLoader($page)  {
        if ($_SERVER['REQUEST_METHOD'] == 'GET')  {
           $page->get();
	}
        else {
	   $page->post();
	}
     }
  }

  class uploadfile {
     public static function csvfileupload($sourcefile,$tmpname) {
        $tardir = "uploads/";
   	// print_r($_FILES);
    	$tarfile = $tardir. $sourcefile;
    	$fileType = pathinfo($tarfile,PATHINFO_EXTENSION);
     	$tempfile = $tmpname;
     	move_uploaded_file($tempfile,$tarfile);
      	return $tarfile;
     }
  }

  class stringFunctions {
     public static function printThis($text)  {
        print($text);
     }
  }

  class html {
     public static function sTag($value) {
        $tag = '<'. $value .'>';
	return $tag;
     }

     public static function eTag($value) {
        $tag = '</'. $value .'>';
	return $tag;
     }

     public static function title($value) {
        $tag = '<title>'. $value .'</title>';
	return $tag;
     }

     public static function input($value) {
        $tag = '<input '. $value .'>';
	return $tag;
     }

     public static function br() {
        $tag = '<br>';
	return $tag;
     }

     public static function h1($value) {
        $tag = '<h1>'. $value .'</h1>';
	return $tag;
     }

     public static function h2($value) {
        $tag = '<h2>'. $value .'</h2>';
	return $tag;
     }

     public static function th($value) {
        $tag = '<th>'. $value .'</th>';
	return $tag;
     }

     public static function td($value) {
        $tag = '<td>'. $value .'</td>';
	return $tag;
     }
  }
?>
