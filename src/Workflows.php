<?php

namespace AlfredApp;

/**
 * Name:        Workflows
 * Description:    This PHP class object provides several useful functions for retrieving, parsing,
 *                and formatting data to be used with Alfred 2 Workflows.
 * Author:        David Ferguson (@jdfwarrior)
 * Revised:        6/6/2013
 * Version:        0.3.3
 */
class Workflows
{
    const PATH_CACHE = "/Library/Caches/com.runningwithcrayons.Alfred-2/Workflow Data/";
    const PATH_DATA = "/Library/Application Support/Alfred 2/Workflow Data/";
    const INFO_PLIST = "info.plist";

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $dataPath;

    /**
     * @var string
     */
    private $bundleId;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $home;

    /**
     * @var array
     */
    private $results = [];

    /**
     * Description:
     * Class constructor function. Intializes all class variables. Accepts one optional parameter
     * of the workflow bundle id in the case that you want to specify a different bundle id. This
     * would adjust the output directories for storing data.
     *
     * @param string $bundleId - optional bundle id if not found automatically
     */
    public function __construct($bundleId = null)
    {
        $this->path = getcwd();
        $this->home = $_SERVER['HOME'];

        if (file_exists(self::INFO_PLIST)) {
            $this->bundleId = $this->get('bundleid', self::INFO_PLIST);
        }

        if (!is_null($bundleId)) {
            $this->bundleId = $bundleId;
        }

        $this->setupCachePath();
        $this->setupDataPath();

        $this->results = array();
    }

    /**
     * Description:
     * Accepts no parameter and returns the value of the bundle id for the current workflow.
     * If no value is available, then false is returned.
     *
     * @return string|false if not available, bundle id value if available.
     */
    public function bundle()
    {
        return (is_null($this->bundleId) ? false : $this->bundleId);
    }

    /**
     * Description:
     * Accepts no parameter and returns the value of the path to the cache directory for your
     * workflow if it is available. Returns false if the value isn't available.
     *
     * @return string|false if not available, path to the cache directory for your workflow if available.
     */
    public function cache()
    {
        return $this->cachePath ?: false;
    }

    /**
     * Description:
     * Accepts no parameter and returns the value of the path to the storage directory for your
     * workflow if it is available. Returns false if the value isn't available.
     *
     * @return string|false if not available, path to the storage directory for your workflow if available.
     */
    public function data()
    {
        return $this->dataPath ?: false;
    }

    /**
     * Description:
     * Accepts no parameter and returns the value of the path to the current directory for your
     * workflow if it is available. Returns false if the value isn't available.
     *
     * @param none
     * @return string|false if not available, path to the current directory for your workflow if available.
     */
    public function path()
    {
        return $this->path ?: false;
    }

    /**
     * Description:
     * Accepts no parameter and returns the value of the home path for the current user
     * Returns false if the value isn't available.
     *
     * @return string|false if not available, home path for the current user if available.
     */
    public function home()
    {
        return $this->home ?: false;
    }

    /**
     * Description:
     * Returns an array of available result items
     *
     * @return array - list of result items
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * Description:
     * Convert an associative array into XML format
     *
     * @param array $results - An associative array to convert
     * @param string $format - format of data being passed (json or array), defaults to array
     * @return string - XML string representation of the array
     */
    public function toXml($results = null, $format = 'array')
    {
        if ($format == 'json') {
            $results = json_decode($results, true);
        }

        if (is_null($results)) {
            $results = $this->results;
        }

        if (empty($results)) {
            return false;
        }

        $items = new \SimpleXMLElement("<items></items>");    // Create new XML element

        foreach ($results as $result) {                                // Loop through each object in the array
            $c = $items->addChild('item');                // Add a new 'item' element for each object
            $c_keys = array_keys($result);                        // Grab all the keys for that item
            foreach ($c_keys as $key) {                        // For each of those keys

                if ($key == 'uid') {
                    if ($result[$key] === null || $result[$key] === '') {
                        continue;
                    } else {
                        $c->addAttribute('uid', $result[$key]);
                    }
                } elseif ($key == 'arg') {
                    $c->addAttribute('arg', $result[$key]);
                    $c->$key = $result[$key];
                } elseif ($key == 'type') {
                    $c->addAttribute('type', $result[$key]);
                } elseif ($key == 'valid') {
                    if ($result[$key] == 'yes' || $result[$key] == 'no') {
                        $c->addAttribute('valid', $result[$key]);
                    }
                } elseif ($key == 'autocomplete') {
                    if ($result[$key] === null || $result[$key] === '') {
                        continue;
                    } else {
                        $c->addAttribute('autocomplete', $result[$key]);
                    }
                } elseif ($key == 'icon') {
                    if (substr($result[$key], 0, 9) == 'fileicon:') {
                        $val = substr($result[$key], 9);
                        $c->$key = $val;
                        $c->$key->addAttribute('type', 'fileicon');
                    } elseif (substr($result[$key], 0, 9) == 'filetype:') {
                        $val = substr($result[$key], 9);
                        $c->$key = $val;
                        $c->$key->addAttribute('type', 'filetype');
                    } else {
                        $c->$key = $result[$key];
                    }
                } else {
                    $c->$key = $result[$key];
                }
            } // end foreach
        } // end foreach

        return $items->asXML();                                // Return XML string representation of the array

    }

    /**
     * Description:
     * Remove all items from an associative array that do not have a value
     *
     * @param string|null $a - Associative array
     * @return boolean
     */
    private function emptyFilter($a)
    {
        if ($a == '' || $a == null) {                        // if $a is empty or null
            return false;                                    // return false, else, return true
        } else {
            return true;
        }
    }

    /**
     * Description:
     * Save values to a specified plist. If the first parameter is an associative
     * array, then the second parameter becomes the plist file to save to. If the
     * first parameter is string, then it is assumed that the first parameter is
     * the label, the second parameter is the value, and the third parameter is
     * the plist file to save the data to.
     *
     * @param array $a - associative array of values to save
     * @param mixed $b - the value of the setting
     * @param string $c - the plist to save the values into
     * @return string - execution output
     */
    public function set($a = null, $b = null, $c = null)
    {
        if (is_array($a)) {
            if (file_exists($b)) {
                if (file_exists($this->path . '/' . $b)) {
                    $b = $this->path . '/' . $b;
                }
            } elseif (file_exists($this->dataPath . "/" . $b)) {
                $b = $this->dataPath . "/" . $b;
            } elseif (file_exists($this->cachePath . "/" . $b)) {
                $b = $this->cachePath . "/" . $b;
            } else {
                $b = $this->dataPath . "/" . $b;
            }
        } else {
            if (file_exists($c)) {
                if (file_exists($this->path . '/' . $c)) {
                    $c = $this->path . '/' . $c;
                }
            } elseif (file_exists($this->dataPath . "/" . $c)) {
                $c = $this->dataPath . "/" . $c;
            } elseif (file_exists($this->cachePath . "/" . $c)) {
                $c = $this->cachePath . "/" . $c;
            } else {
                $c = $this->dataPath . "/" . $c;
            }
        }

        if (is_array($a)) {
            foreach ($a as $k => $v) {
                exec('defaults write "' . $b . '" ' . $k . ' "' . $v . '"');
            }
        } else {
            exec('defaults write "' . $c . '" ' . $a . ' "' . $b . '"');
        }
    }

    /**
     * Description:
     * Read a value from the specified plist
     *
     * @param $propertyToRead - the value to read
     * @param $filename - plist to read the values from
     * @return boolean|string false if not found, string if found
     * @todo simplify
     */
    public function get($propertyToRead, $filename)
    {
        // This attempts to get the file in either the home, data or cache dir
        if (file_exists($this->path . '/' . $filename)) {
            $filename = $this->path . '/' . $filename;
        } elseif (file_exists($this->dataPath . "/" . $filename)) {
            $filename = $this->dataPath . "/" . $filename;
        } elseif (file_exists($this->cachePath . "/" . $filename)) {
            $filename = $this->cachePath . "/" . $filename;
        } else {
            return false;
        }

        // Execute system call to read plist value
        $output = '';
        exec("defaults read '${filename}' ${propertyToRead}", $output);

        // @todo change this into an exception
        if (empty($output)) {
            return false;
        }

        return $output[0];
    }

    /**
     * Description:
     * Read data from a remote file/url, essentially a shortcut for curl
     *
     * @param string $url - URL to request
     * @param array $options - Array of curl options
     * @return string result from curl_exec
     * @todo move request to a different class
     * @deprecated Look into using Client class
     */
    public function request($url = null, array $options = null)
    {
        if (is_null($url)) {
            return false;
        }

        $defaults = array(                                    // Create a list of default curl options
            CURLOPT_RETURNTRANSFER => true,                    // Returns the result as a string
            CURLOPT_URL => $url,                            // Sets the url to request
            CURLOPT_FRESH_CONNECT => true
        );

        if ($options) {
            foreach ($options as $k => $v) {
                $defaults[$k] = $v;
            }
        }

        array_filter($defaults,                            // Filter out empty options from the array
            array($this, 'emptyFilter'));

        $ch = curl_init();                                    // Init new curl object
        curl_setopt_array($ch, $defaults);                // Set curl options
        $out = curl_exec($ch);                            // Request remote data
        $err = curl_error($ch);
        curl_close($ch);                                    // End curl request

        if ($err) {
            return $err;
        } else {
            return $out;
        }
    }

    /**
     * Description:
     * Allows searching the local hard drive using mdfind
     *
     * @param string $query - search string
     * @return array - array of search results
     */
    public function mdfind($query)
    {
        exec('mdfind "' . $query . '"', $results);
        return $results;
    }

    /**
     * Description:
     * Accepts data and a string file name to store data to local file as cache
     *
     * @param array $a - data to save to file
     * @param string $filename - filename to write the cache data to
     * @return boolean
     */
    public function write(array $a, $filename)
    {
        if (file_exists($filename)) {
            if (file_exists($this->path . '/' . $filename)) {
                $filename = $this->path . '/' . $filename;
            }
        } elseif (file_exists($this->dataPath . "/" . $filename)) {
            $filename = $this->dataPath . "/" . $filename;
        } elseif (file_exists($this->cachePath . "/" . $filename)) {
            $filename = $this->cachePath . "/" . $filename;
        } else {
            $filename = $this->dataPath . "/" . $filename;
        }

        if (is_array($a)) {
            $a = json_encode($a);
            file_put_contents($filename, $a);
            return true;
        } elseif (is_string($a)) {
            file_put_contents($filename, $a);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Description:
     * Returns data from a local cache file
     *
     * @param string $filename filename to read the cache data from
     * @param array|bool $array
     * @return false if the file cannot be found, the file data if found. If the file
     *            format is json encoded, then a json object is returned.
     */
    public function read($filename, $array = false)
    {
        if (file_exists($filename)) {
            if (file_exists($this->path . '/' . $filename)) {
                $filename = $this->path . '/' . $filename;
            }
        } elseif (file_exists($this->dataPath . "/" . $filename)) {
            $filename = $this->dataPath . "/" . $filename;
        } elseif (file_exists($this->cachePath . "/" . $filename)) {
            $filename = $this->cachePath . "/" . $filename;
        } else {
            return false;
        }

        $out = file_get_contents($filename);
        if (!is_null(json_decode($out)) && !$array) {
            $out = json_decode($out);
        } elseif (!is_null(json_decode($out)) && !$array) {
            $out = json_decode($out, true);
        }

        return $out;
    }

    /**
     * Description:
     * Helper function that just makes it easier to pass values into a function
     * and create an array result to be passed back to Alfred
     *
     * @param string $uid - the uid of the result, should be unique
     * @param string $arg - the argument that will be passed on
     * @param string $title - The title of the result item
     * @param string $sub - The subtitle text for the result item
     * @param string $icon - the icon to use for the result item
     * @param boolean $valid - sets whether the result item can be actioned
     * @param string $auto - the autocomplete value for the result item
     * @param null $type
     * @return array - array item to be passed back to Alfred
     */
    public function result($uid, $arg, $title, $sub, $icon, $valid = true, $auto = null, $type = null)
    {
        $temp = array(
            'uid' => $uid,
            'arg' => $arg,
            'title' => $title,
            'subtitle' => $sub,
            'icon' => $icon,
            'valid' => ($valid ? 'yes' : 'no'),
            'autocomplete' => $auto
        );

        if (!is_null($type)) {
            $temp['type'] = $type;
        };

        array_push($this->results, $temp);

        return $temp;
    }

    /**
     * @return boolean
     */
    private function setupCachePath()
    {
        if ($this->bundleId) {
            $this->cachePath = $this->home . self::PATH_CACHE . $this->bundleId;
            if (!file_exists($this->cachePath)) {
                return mkdir($this->cachePath);
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function setupDataPath()
    {
        if ($this->bundleId) {
            $this->dataPath = $this->home . self::PATH_DATA . $this->bundleId;
            if (!file_exists($this->dataPath)) {
                return mkdir($this->dataPath);
            }
        }
        return false;
    }
}