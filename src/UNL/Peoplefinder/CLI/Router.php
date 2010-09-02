<?php
// This is used to parse the CLI options and set up the search parameters
class UNL_Peoplefinder_CLI_Router
{
    public static function route($options = array())
    {
        if (!isset($_SERVER['argv'],$_SERVER['argv'][1])
            || $_SERVER['argv'][1] == '--help' || $_SERVER['argc'] != 2) {
            
        } else {
            $options['q'] = $_SERVER['argv'][1];
            $options['view'] = 'search';
        }
        return $options;
    }
}