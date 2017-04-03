<?php
	$configs = include_once('config.php');
	date_default_timezone_set('UTC');
	class gitSendler
	{
		private $fileTypes;
		private $nameBdFile;
		private $flagDir;
		private $scandir;
		private $checkdir;
		private $farray;
		private $putflag = false;
		
		public function setParam ($params)
		{
			$this->fileTypes = $params->type;
			$this->nameBdFile = $params->timefile;
			$this->flagDir = $params->mainDir;
			$this->scandir = $params->scandir;
		}
		
		public function checkDirectory ()
		{
			if ($this->flagDir == true)
			{
				$this->checkdir = dirname($_SERVER['SCRIPT_FILENAME']);
			} else {
				$this->checkdir = $this->scandir;
			}
		}
		
		public function getAllfiles ()
		{
			$directory = $this->checkdir;
			$type = $this->fileTypes;
			$files = preg_grep("~\.({$type})$~", scandir($directory));
			$i = 0;
			foreach ($files as $filename)
			{
				$modtime[$i]['time'] =  date ("F d Y H:i:s.", filemtime($filename));
				$modtime[$i]['name'] = $filename;
				$i++;
			}
			$this->farray = $modtime;
			return $modtime;
		}
		
		public function getUpdatedFiles ($file)
		{
			$namefile = $this->nameBdFile;
			if (file_exists($namefile)) {
				$content = file_get_contents($namefile);
				foreach ($file as $key)
				{
					if ($key['time']>$content)
					{
						$filetogit[] = $key['name'];
						$putflag = true;
					}
				}
				if ($putflag == true)
				{
					file_put_contents($namefile, print_r(date ("F d Y H:i:s."), true));
				}
			} else {
				$myfile = fopen($namefile, "w");
				file_put_contents($namefile, print_r(date ("F d Y H:i:s."), true));
				$filetogit = $file;
			}
			$this->putflag = $putflag;
			fclose($myfile);
			return $filetogit;
		}
		
		public function sendGit ($FilesSend)
		{
			$putflag = $this->putflag;
			if ($putflag == true)
			{
				return 'sendGit';
			} else {
				return 'notSend';
			}
		}
	}
	$git = new gitSendler();
	$git->setParam ($configs);
	$git->checkDirectory();
	$files = $git->getAllfiles();
	$sendFilesNames = $git->getUpdatedFiles($files);
	$git->sendGit($sendFilesNames);
?>