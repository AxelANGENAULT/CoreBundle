<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UploadController extends Controller
{
    public function receiveAction()
    {
        // print_r($_POST);
    if (!isset($_FILES['file']['name']))
        exit();
    
$uploadfile = 'c:\\temp\\' . basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
#$content = file_get_contents($uploadfile);
$zip = zip_open($uploadfile);
$entry = zip_read($zip);
print zip_entry_name($entry);
zip_entry_open($zip, $entry, "r");
$entry_content = zip_entry_read($entry, zip_entry_filesize($entry));
print $entry_content;
 zip_close($zip);
#print "content";
#print gzinflate($content);
        exit();
    }
}
