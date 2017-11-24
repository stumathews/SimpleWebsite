<?php
require('settings.php');
require($root_path.'/classes/WebsitePage.php');

function PrintTitle()
{
	echo "Nine years ago";
}

function PrintSlogan()
{
	echo "We are all at various stages of the same development.";
}

function PrintFooter()
{
	echo 'Copyright &copy; Stuart Mathews 2011';
}

function PrintIpsum()
{
echo "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).";
}


/* Returns an array of the latest 4 articles in the pages folder */
function ListMostRecent($absolute_folder_path)
{
	$files = array();
	if ($handle = opendir($absolute_folder_path)) 
	{
	 while (false !== ($file = readdir($handle))) {
       if ($file != "." && $file != "..") {
          $files[filemtime($absolute_folder_path.'/'.$file)] = $file;
       }
   }
   closedir($handle);

	ksort($files);
	$reallyLastModified = end($files);

	$recent_pages = array();
	$count_files = 0;
	foreach($files as $file) {
 		if( $count_files < 4 ){
   	 $lastModified = date('F d Y, H:i:s',filemtime($absolute_folder_path.'/'.$file));
				
				if( endsWith($file,'.page'))
				{
					$thePage = GetPage($file);
   	     if ($file == $reallyLastModified) {
   	         // do stuff for the real last modified file
					
   	     }
					$recent_pages[$count_files] = $thePage;
					$count_files = $count_files + 1;
				}
		}
	}
	return $recent_pages;
	}
}

/* Returns true if needle is a sub string of haystack */
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

/* Returns if haystack string starts with string needle  */
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

/* Produces a HTML list of links to pages in the pages/ folder  */
function ListContents($folder_path)
{
	$dir = $folder_path; //You could add a $_GET to change the directory
	$files = scandir($dir);
	echo '<ul>';
	foreach($files as $key => $value)
	{
		if (endsWith($value,'.page')) 
		{
			// We found a string inside string
			$page = GetPage($value);
			$link_text = $page->Title;
			$link = '<a href={URL}>'.$link_text.'</a>';
			$link = str_replace( '{URL}','?Page='.$value,$link);
			echo '<li>'.$link.'</li>';
		}
	}
	echo '</ul>';
}

/* Reads .page xml file and converts into a WebSitePage object */
function GetPage($PAGE)
{
$filePath = realpath(dirname(__FILE__));
$rootPath = realpath($_SERVER['DOCUMENT_ROOT']);
$htmlPath = str_replace($root, '', $filePath);
$TITLE=null;
$CONTENT=null;
$PAGE_BASE=$rootPath.'/pages';
if( file_exists($PAGE_BASE.'/'.$PAGE) == true )
{
  $xml = simplexml_load_file("pages/$PAGE");
  if( $xml != false )
  {
    foreach($xml->children() as $child)
    {
      $CHILD=$child->getName();
      switch($CHILD)
      {
        case 'title':
          $TITLE=$child;
          break;
        case 'content':
          $CONTENT=$child;
        break;
      }
    }
	$returned_page = new WebsitePage();
	$returned_page->Title = $TITLE;
	$returned_page->Content = $CONTENT;
	return $returned_page;
  }
}
else
{
	$error_page_not_found = 'page_not_found.error';
	if(file_exists( $PAGE_BASE.'/'.$error_page_not_found))
	{
		return GetPage($error_page_not_found);
	}
	else
  {
		exit('Document Doesnt exist');
	}
}

}
?>
