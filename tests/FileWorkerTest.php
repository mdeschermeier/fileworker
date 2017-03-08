<?php

require_once "../../FileWorker.php";
require_once "../vendor/autoload.php";

class FileWorkerTest extends PHPUnit_framework_TestCase{
	public function testWriteCSVDataWithValidRequiredParams(){

		$assert_data	= array(1, 2, 3);
		$test_data		= array(array(1, 2, 3));

		$this->assertTrue(FileWorker::writeCSVData($test_data, 'testFile.csv'));
		$handle = fopen('testFile.csv', 'r');
		$file_data = fgetcsv($handle, 1000, ',');
		$this->assertEquals($assert_data, $file_data);
	}

	public function testWriteCSVDataWithHeaderData(){
		$assert_headers = array('Test1', 'Test2', 'Test3');
		$assert_data	= array(1, 2, 3);

		$test_headers	= array('Test1', 'Test2', 'Test3');
		$test_data		= array(array(1, 2, 3));

		$this->assertTrue(FileWorker::writeCSVData($test_data, 'testFile.csv', $test_headers));
		$handle = fopen('testFile.csv', 'r');

		$file_header = fgetcsv($handle, 1000, ',');
		$this->assertEquals($assert_headers, $file_header);
		$file_data = fgetcsv($handle, 1000, ',');
		$this->assertEquals($assert_data, $file_data);
	}

	public function testWriteCSVDataModeParameter(){
		$assert_headers 		= array('Test1', 'Test2', 'Test3');
		$assert_data_existing	= array(1, 2, 3);
		$assert_data_new		= array(4, 5, 6);

		$test_data_new			= array(array(4, 5, 6));

		$this->assertTrue(FileWorker::writeCSVData($test_data_new, 'testFile.csv', false, 'a'));
		$handle = fopen('testFile.csv', 'r');
		$read_headers 		= fgetcsv($handle, 1000, ',');
		$read_data_existing = fgetcsv($handle, 1000, ',');
		$read_data_new 		= fgetcsv($handle, 1000, ',');

		$this->assertEquals($assert_headers, $read_headers);
		$this->assertEquals($assert_data_existing, $read_data_existing);
		$this->assertEquals($assert_data_new, $read_data_new);

	}

	public function testGetCSVDataWithValidFullParams(){
		$assert_array = array('header' => array('Test1', 'Test2', 'Test3'),
							  'data' => array(array(1,2,3), array(4,5,6)));

		$res = FileWorker::getCSVData('testFile.csv', 1000, ',');
		$this->assertEquals($assert_array, $res);
	}

	public function testGetCSVDataWithValidRequiredParams(){
		$assert_array = array('header' => array('Test1', 'Test2', 'Test3'),
							  'data' => array(array(1,2,3), array(4,5,6)));

		$res = FileWorker::getCSVData('testFile.csv');
		$this->assertEquals($assert_array, $res);
	}

	public function testGetCSVDataWithValidPartialParams(){
		$assert_array = array('header' => array('Test1', 'Test2', 'Test3'),
							  'data' => array(array(1,2,3), array(4,5,6)));

		$res = FileWorker::getCSVData('testFile.csv', null, ',');
		$res2 = FileWorker::getCSVData('testFile.csv', 5000);
		$this->assertEquals($assert_array, $res);
		$this->assertEquals($assert_array, $res2);
	}

	public function testGetCSVDataWithInvalidParamsFails(){
		$res = FileWorker::getCSVData('notafile.csv');
		$this->assertFalse($res);
	}

	public function testDeleteFileWhenFileExists(){
		$filename = 'testFile.csv';

		$this->assertTrue(FileWorker::deleteFile($filename));
		$this->assertFileNotExists($filename);
	}

	public function testDeleteFileWhenFileNotExists(){
		$filename = 'NONEXISTANT.file';

		$this->assertFileNotExists($filename);
		$this->assertFalse(FileWorker::deleteFile($filename));
	}

	public function testFindAllFilesByExtInGivenDirectory(){
		$assert_files = array('Test1.csv', 'Test2.doc', 'Test3.csv', 'Test4.txt', 'Test5.php');
		$dir = 'FileWorkerDummyFiles/FindFilesExt';

		$found_files = FileWorker::findFilesByExt($dir);
		$this->assertEquals($assert_files, $found_files);
	}

	public function testFindFilesOfSpecificExtension(){
		$dir = 'FileWorkerDummyFiles/FindFilesExt';

		$assert_files_1 = array('Test1.csv', 'Test3.csv');
		$assert_files_2 = array('Test5.php');
		$assert_files_3 = array('Test1.csv', 'Test3.csv', 'Test4.txt');
		$assert_files_4 = array('Test2.doc');

		$files_1 = FileWorker::findFilesByExt($dir, 'csv');
		$files_2 = FileWorker::findFilesByExt($dir, array('php'));
		$files_3 = FileWorker::findFilesByExt($dir, array('csv', 'txt'));
		$files_4 = FileWorker::findFilesByExt($dir, '/doc/', true);

		$this->assertEquals($assert_files_1, $files_1);
		$this->assertEquals($assert_files_2, $files_2);
		$this->assertEquals($assert_files_3, $files_3);
		$this->assertEquals($assert_files_4, $files_4);
	}

	public function testFindFilesByNameWithOnlyDir(){
		$dir = 'FileWorkerDummyFiles/FindFilesName';

		$assert_files_1 = array('Apple.txt', 'Banana.csv', 'Carrot.doc', 'Carrot_Cake.php', 'DragonFruit.jpg', 'First.txt');

		$files_1 = FileWorker::findFilesByName($dir);
		$this->assertEquals($assert_files_1, $files_1);
	}

	public function testFindFilesByNameWithSearchTerms(){
		$dir = 'FileWorkerDummyFiles/FindFilesName';

		$assert_files_1 = array('Apple.txt'); // specifically find 'Apple.*'
		$assert_files_2 = array('Banana.csv', 'Carrot.doc', 'Carrot_Cake.php', 'DragonFruit.jpg'); // search for the letter 'a' in basename
		$assert_files_3 = array('Carrot.doc', 'Carrot_Cake.php', 'First.txt'); //Search for files with 'Carrot' or 'First' in name
		$assert_files_4 = array('Carrot.doc'); //Search for 'Carrot.doc' only, using custom Regex

		$files_1 = FileWorker::findFilesByName($dir, 'Apple');
		$files_2 = FileWorker::findFilesByName($dir, 'a');
		$files_3 = FileWorker::findFilesByName($dir, array('Carrot', 'First'));
		$files_4 = FileWorker::findFilesByName($dir, '/Carrot[^_]/', true);

		$this->assertEquals($assert_files_1, $files_1);
		$this->assertEquals($assert_files_2, $files_2);
		$this->assertEquals($assert_files_3, $files_3);
		$this->assertEquals($assert_files_4, $files_4);
	}

	public function testRenameFile(){
		$dir = 'FileWorkerDummyFiles/Rename/';

		$assert_rename_1 = 'Renamed_1.csv';
		$assert_rename_2 = 'Test1.csv';

		$filename = FileWorker::renameFile('Test1.csv', 'Renamed_1.csv', $dir);
		$this->assertEquals($assert_rename_1, $filename);

		$filename = FileWorker::renameFile('Renamed_1.csv', 'Test1.csv', $dir);
		$this->assertEquals($assert_rename_2, $filename);
	}
}
