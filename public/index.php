<?php
require '../vendor/autoload.php';
require '../app/models/Files.php';
new \Classes\Model\Connection();
$mapper = new \Classes\Model\FilesMapper();

$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig()
	));
$app->get('/hello/:world', function($world) use ($app) {
	echo "Hello, $world";
});

$app->get('/', function() use ($app) {
	echo $app->render('file_load.html');
});

$app->post('/', function() use ($app,$mapper) {
	if (isset($_POST['load'])) {
		$pictures = array('jpg', 'jpeg','gif','png');
		$file = $mapper->createFile();
		$file->name = $_FILES['load']['name'];
		$file->size = $_FILES['load']['size'];
		$file->comment = $_POST['comment'];
		$file->set_expr('uploaded', 'NOW()'); 
		$filename = $_FILES['load']['name'];
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$file->extension = $ext;
		$file->save();
		$id = $file->id;
		$tmpFile = $_FILES['load']['tmp_name'];
		$newFile = __DIR__."/upload/".$id."-".$_FILES['load']['name']."-txt";
		$result = move_uploaded_file($tmpFile, $newFile);
		if (in_array($ext, $pictures)) {
			$resize = new \Classes\Model\Resize($newFile);
			$resize->resizeImage(150,150);
			$path = __DIR__."/upload/resize/resize-".$_FILES['load']['name'];
			$resize = $resize->saveImage($path,80);
		}
		if ($result) {
			$message = 'File was successfully uploaded';
		} else {
			$message = 'File failed to upload';
		}
		$app->redirect("/view/$id");
	}
});

$app->get('/view/:id', function($id) use ($app, $mapper) {
	$file = $mapper->getFile($id);
	$pictures = array('jpg','jpeg','gif','png');
	if (in_array($file->extension, $pictures)){
	echo $app->render('view_image.html', array('file' => $file));
} else {
	echo $app->render('view.html', array('file' => $file));
}
});

$app->get('/download/:id', function($id) use ($app, $mapper) {
	$file = $mapper->getFile($id);
	$name = "upload/".$id."-".$file->name."-txt";
	if (file_exists($name)) {
		header("X-Sendfile:".realpath(dirname(__FILE__)).'/'.$name);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$file->name);
		exit;
	} else {
		echo "Not found";
	}
});

$app->get('/list', function() use ($app, $mapper) {

	$files = $mapper->getAllFiles();
	echo $app->render('list.html', array('files' => $files));
	
});
$app->run();