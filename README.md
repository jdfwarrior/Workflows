#Workflows

A PHP utility class for creating workflows with Alfred 2. This class provides functions for working with plist settings files, reading and writing data to files, generating Alfred feedback results, requesting remote data, and more.

##Initialization
To initialize the class object, simply include it, the create the new class item.

	require_once('workflows.php');
	$w = new Workflows();

##Methods
###bundle()
Returns the current workflow bundle id.

###cache()
Returns the path to the cache folder for the workflow.

	ie. /Users/ferg/Library/Caches/com.runningwithcrayons.Alfred-2/Workflow Data/jdfwarrior.Rdio

###data()
Returns the path to the data folder for the workflow.

	ie. /Users/ferg/Library/Application Support/Alfred 2/Workflow Data/jdfwarrior.Rdio

###path()
Returns the current path of the workflow (inside the Alfred.alfredpreferences file).

###home()
Returns the path to the current users home folder.

###toxml( $data = null, $format = 'array' )
Accepts a properly formatted array or json object and converts it into proper XML for returning to workflows as feedback.

If no data is passed in directly, the array of items created from using the result() function will be used instead.

####Parameters
$a - The array or json object that you wish to pass. This is only necessary if you are passing the array or object directly. If items have been created via the result() function, then no parameters are necessary for this function.

$format - Possible values: array, json. Used to indicate the format of the data being passed into the function.

###set( $a = null, $b = null , $c = null )
Save values to the specified plist.

####Parameters
Values can be a field name, or an array. If you pass a field name, the syntax for the function would be $a = field name, $b = value, $c = filename. Otherwise, you can pass an associative array into $a and the syntax would be $a would be the array of fields and values, and $b would be the file to save the data into.

If only a filename is provided, the function will check the data path, then cache path, then local path, or fall back to data path.

###get( $field, $file )
Returns the value of the specified field ($a) from the specified plist ($b).

If only a filename is provided, the function will check the data path, then cache path, then local path. If the file is not found, the function will return false.

###request( $url, $options )
Performs a curl request on the url specified ($url). cURL options can be passed as an associative array in the $options argument. See [here](http://www.php.net/manual/en/function.curl-setopt.php) for a list of available cURL options.


###mdfind( $query )
Executes an mdfind command and returns results as an array of matching files.

###write( $data, $file )
Similar to set() except data is dumped as text to a file. If the passed in data is an array, the data is json_encoded and written to the output file.

###read( $file )
Opposite of write(). This function reads data from the specified file and returns it. If the data read is json, a json object is returned. Otherwise, it will return string data.

###result( $uid, $arg, $title, $subtitle, $icon, $valid = 'yes', $autocomplete = null )
Creates a new result item that is cached within the class object. This set of results is available via the results() functions, or, can be formatted and returned as XML via the toxml() function.

Autocomplete value is optional. If no value is specified, it will take the value of the result title. Possible values for $valid are 'yes' and 'no' to set the validity of the result item.