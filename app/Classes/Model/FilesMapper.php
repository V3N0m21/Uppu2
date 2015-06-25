<?php namespace Classes\Model;
use Model;

class FilesMapper extends Model {

	public function getAllFiles() {
		$files = $this->factory('Files')->order_by_desc('uploaded')->limit(100)->offset(0)->find_many();
		return $files;
	}

	public function getFile($id) {
		$file = $this->factory('Files')->find_one($id);
		return $file;
	}

	public function insertFile($data) {
		$file = $this->factory('Files')->create();
		$file->name = $data->name;
		$file->size = $data->size;
		$file->comment = $data->comment;
		$file->extension = $data->extension;
		
		$file->save();

	}

	public function createFile() {
		$file = $this->factory('Files')->create();
		return $file;
	}
}