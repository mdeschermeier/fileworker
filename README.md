mdeschermeier\FileWorker\FileWorker
===============

A simple package for searching, renaming, deleting, reading, and writing .csv
files.




* Class name: FileWorker
* Namespace: mdeschermeier\FileWorker







Methods
-------


### getCSVData

    mixed FileWorker::getCSVData(string $filename, integer $max_line_size, string $delim)

Given a csv filename, parses the file and returns associative array of records
with the header in the 'header' key of the returned array and a 2D array
of records in the 'data' key.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $filename **string**
* $max_line_size **integer**
* $delim **string**



### writeCSVData

    boolean FileWorker::writeCSVData(array $data, string $filename, array $header, string $mode)

Given a 2D array of records, a filename (or path), and optionally an array
of header fields, this function will write data to a csv formatted file.

Also optionally takes a file write mode setting.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $data **array**
* $filename **string**
* $header **array**
* $mode **string**



### deleteFile

    boolean FileWorker::deleteFile(string $filename)

Simple wrapper for deleting files.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $filename **string**



### findFilesByExt

    mixed FileWorker::findFilesByExt(string $dir, string $filetypes, boolean $regex_passed)

File search utility by file extension. Supports regex matching via passing
the pattern in through the second parameter, just be sure to flip $regex_passed
to true if passing regex. Supplying *just* the first parameter returns all
files found.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $dir **string**
* $filetypes **string**
* $regex_passed **boolean**



### findFilesByName

    mixed FileWorker::findFilesByName(string $dir, string $term, boolean $regex_passed)

File search utility by filename. Supports regex matching via passing
the pattern in through the second parameter, just be sure to flip $regex_passed
to true if passing regex. Supplying *just* the first parameter returns all
files found.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $dir **string**
* $term **string**
* $regex_passed **boolean**



### renameFile

    mixed FileWorker::renameFile(string $original_name, string $new_name, string $parent_dir)

Simple wrapper for renaming files. Optional $parent_dir parameter for
renaming files in non-local directories.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $original_name **string**
* $new_name **string**
* $parent_dir **string**



### buildRegex

    string FileWorker::buildRegex(string $term, boolean $regex_passed)

Compiles various search terms into regex patterns for use with public
class methods. Returns a pattern that matches everything by default.



* Visibility: **private**
* This method is **static**.


#### Arguments
* $term **string**
* $regex_passed **boolean**

README.md generated with evert/phpdoc-md.
