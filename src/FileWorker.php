<?php
/**
 *
 * @author Mike Deschermeier
 * @license MIT
 * @link http://github.com/mdeschermeier/FileWorker
 *
 */

namespace mdeschermeier\FileWorker;

 /**
  * A simple package for searching, renaming, deleting, reading, and writing .csv
  * files.
  *
  * @package FileWorker
  */

class FileWorker{

	/**
	 * Given a csv filename, parses the file and returns associative array of records
	 * with the header in the 'header' key of the returned array and a 2D array
	 * of records in the 'data' key.
	 *
	 * @param string $filename
	 * @param integer $max_line_size
	 * @param string $delim
	 *
	 * @return mixed
	 */
	static public function getCSVData($filename, $max_line_size = 5000, $delim = ','){
		$retArray = array();

		try{
			$handle = fopen($filename, 'r');
			$retArray['header'] = fgetcsv($handle, $max_line_size, $delim);
			while($data = fgetcsv($handle, $max_line_size, $delim)){
				$retArray['data'][] = $data;
			}
			fclose($handle);
		}catch(Exception $e){
			return false;
		}

		return $retArray;
	}

	/**
	 * Given a 2D array of records, a filename (or path), and optionally an array
	 * of header fields, this function will write data to a csv formatted file.
	 * Also optionally takes a file write mode setting.
	 *
	 * @param array $data
	 * @param string $filename
	 * @param array $header
	 * @param string $mode
	 *
	 * @return boolean
	 */
	static public function writeCSVData($data, $filename, $header = false, $mode = 'w'){
		try {
			$handle = fopen($filename, $mode);
			if ($header){
				fputcsv($handle, $header);
			}
			foreach($data as $record){
				fputcsv($handle, $record);
			}
			fclose($handle);
		}catch(Exception $e){
			//Error writing file. If file exists, delete it.
			if (file_exists($filename)){
				self::deleteFile($filename);
			}
			return false;
		}
		return true;
	}

	/**
	 * Simple wrapper for deleting files.
	 *
	 * @param string $filename
	 *
	 * @return boolean
	 */
	static public function deleteFile($filename){
		try{
			unlink($filename);
		}catch(Exception $e){
			return false;
		}
		return true;
	}

	/**
	 * File search utility by file extension. Supports regex matching via passing
	 * the pattern in through the second parameter, just be sure to flip $regex_passed
	 * to true if passing regex. Supplying *just* the first parameter returns all
	 * files found.
	 *
	 * @param string $dir
	 * @param string $filetypes
	 * @param boolean $regex_passed
	 *
	 * @return mixed
	 */
	static public function findFilesByExt($dir, $filetypes = null, $regex_passed = false){
		try{
			$files = array();
			$regex = self::buildRegex($filetypes, $regex_passed);
			foreach (new \DirectoryIterator($dir) as $file){
				if ($file->isFile()){
					$filename = $file->getFilename();
					$fileparts = explode('.', $filename);
					$extension = end($fileparts);
					if (preg_match($regex, $extension)){
						array_push($files, $file->getFilename());
					}
				}
			}
			return $files;
		}catch(Exception $e){
			return false;
		}
	}

	/**
	 * File search utility by filename. Supports regex matching via passing
	 * the pattern in through the second parameter, just be sure to flip $regex_passed
	 * to true if passing regex. Supplying *just* the first parameter returns all
	 * files found.
	 *
	 * @param string $dir
	 * @param string $term
	 * @param boolean $regex_passed
	 *
	 * @return mixed
	 */
	public static function findFilesByName($dir, $term = null, $regex_passed = false){
		try{
			$ret_files = array();
			$regex = self::buildRegex($term, $regex_passed);
			foreach (new \DirectoryIterator($dir) as $file){
				if ($file->isFile()){
					$basename = $file->getBasename();
					if (preg_match($regex, $basename)){
						array_push($ret_files, $file->getFilename());
					}
				}
			}
			return $ret_files;
		}catch(Exception $e){
			return false;
		}
	}

	/**
	 * Simple wrapper for renaming files. Optional $parent_dir parameter for
	 * renaming files in non-local directories.
	 *
	 * @param string $original_name
	 * @param string $new_name
	 * @param string $parent_dir
	 *
	 * @return mixed
	 */
	public static function renameFile($original_name, $new_name, $parent_dir =''){
		if (rename($parent_dir.$original_name, $parent_dir.$new_name)){
			return $new_name;
		}
		return false;
	}

// PRIVATE //===================================================================

	/**
 	 * Compiles various search terms into regex patterns for use with public
	 * class methods. Returns a pattern that matches everything by default.
 	 *
 	 * @param string $term
 	 * @param boolean $regex_passed
 	 *
	 * @return string
 	 */
	private static function buildRegex($term, $regex_passed){
		$regex = '/.*/';

		if (is_array($term)){
			$regex = '/';
			$len = count($term);
			for($i = 0; $i < $len; $i++){
				$regex .= $term[$i];
				if ($i != $len-1){
					$regex .= '|';
				}
			}
			$regex .= '/';
		}else if (gettype($term) == 'string' && !$regex_passed){
			$regex = '/'.$term.'/';
		}else if(gettype($term) == 'string' && $regex_passed){
			$regex = $term;
		}

		return $regex;
	}
}
?>
