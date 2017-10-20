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
	public function __construct()
	{
	   $pageRequest = 'homepage';
	   if(isset($_REQUEST['page'])) {
	   $pageRequest = $_REQUEST['page'];
	   }
	   $page = new $pageRequest;
           if($_SERVER['REQUEST_METHOD'] =='GET') {
	   $page->get();
	   } else {
       	   $page->post();
	   }
      }
}

abstract class page {
     	protected $html;
     
     	public function __construct()
        {
     	 $this->html .= '<html>';
	 $this->html .= '<link rel="stylesheet" href="styles.css">';
	 $this->html .= '<body>';
        }
     
     	public function __destruct()
        {
     	 $this->html .= '</body></html>';
	 stringFunctions::printThis($this->html);
        }
     
     	public function get() 
	{
     	 echo 'default get message';
        }

	public function post() 
	{
      	 print_r($_POST);
        }
}

class homepage extends page 
{
	public function get()
	{
		$form = '<form action="index.php" method="post" enctype="multipart/form-data">';
		$form .= '<h3><center>Select the CSV File to be uploaded:<br></center></h3>';
		$form .= '<center>';
		$form .= '<input type="file" name="fileToUpload" id="fileToUpload">';
		$form .= '<br>';
		$form .= '<input type="submit" value="submit">';
		$form .= '</center>';
		$form .= '</form> ';
		$this->html .= '<center><h2>Upload Page</h2></center>';
		$this->html .= $form;						
        }
        
	public function post() 
	{
		$sourcefile = $_FILES["fileToUpload"]["name"];
		$tempname = $_FILES["fileToUpload"]["tmp_name"];
		$filename = uploadfile::csvfileupload($sourcefile,$tempname);
		header('Location:?page=table&filename='.$filename);
       	}
}
  
class table extends page
{
	 public function get()
	 {
	    $sourcefile=$_GET['filename'];
	    echo trim($sourcefile,"uploads/"). " is successfully uploaded<br><br> The Table is as shown below<br><br>";
	    $heading = 1;
	    $handle = fopen($sourcefile,"r");
	    $table = '<table border="1">';
	    while(($data = fgetcsv($handle))!=FALSE)
	    {
	       if ($heading == 1)
	       {
	         $table .='<thead><tr>';
		   foreach ($data as $value) 
		   {
		     if (!isset($value))
		       $value = "&nbsp";
		     else
		       $table .= "<th>". $value ."</th>";
		   }
		   $table .= '</tr></thead><tbody>';
		}
                else {
		      $table .='<tr>';
		      foreach ($data as $value)  
		      {
		        if(!isset($value))
			  $value = "&nbsp";
			else
			  $table .= "<td>". $value . "</td>";
		      }
		      $table .='</tr>';
  	       	}
	  	$heading++;
	 }
	 $table .= '</tbody></table>';
	 $this->html .= $table;
	 fclose($handle);
    	}
}
class uploadfile
{
    	public static function csvfileupload($sourcefile,$tmpname) 
	{
    		$tardir = "uploads/";
   		// print_r($_FILES);
    		$tarfile = $tardir. $sourcefile;
    		$fileType = pathinfo($tarfile,PATHINFO_EXTENSION);
     		$tempfile = $tmpname;
     		move_uploaded_file($tempfile,$tarfile);
      		return $tarfile;
       	}
}

class stringFunctions 
{
  public static function printThis($text)  {
  print($text);
  }
}
?>
