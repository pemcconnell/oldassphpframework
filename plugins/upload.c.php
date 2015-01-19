<?php

class Upload
{
	static private	$FILE;
	
	static public function uploadFile($inputname, $sFolder = 'cms_image')
	{
		if(isset($_FILES[$inputname]) && ($_FILES[$inputname]['error'] != 4))
		{
			self::$FILE = $_FILES[$inputname];
			
			$ext = HTML::getFileExt($_FILES[$inputname]['name']);
			
			$newfilename = time() . '-' . rand(0,100) . '.' .  $ext;
			
			global $SETTINGS;
			$path = $SETTINGS['uploadpath'] . $sFolder . DS . $newfilename;
			
			if(self::actualUpload($path)) return $newfilename; 
		}
		return false;
	}
	
	static public function actualUpload($path)
	{
		global $CONSOLE;
		$folder = dirname($path) . DS;
		if(!is_dir($folder))
		{
			$CONSOLE->exception('The desired path (folder ' . $folder. ') does not exist');
			return false;
		}
		if(!is_writable($folder))
		{
			$CONSOLE->exception('The desired path (folder ' . $folder . ') is not writable');
			return false;
		}
		if(move_uploaded_file(self::$FILE['tmp_name'], $path))
		{
			return true;
		}
		global $CONSOLE;
		$CONSOLE->exception('Could not upload file ' . self::$FILE['name'] . '(' . self::$FILE['tmp_name'] . ') to ' . $path);
		return false;
	}
}