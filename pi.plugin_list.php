<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Copyright (C) 2004 - 2011 EllisLab, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
ELLISLAB, INC. BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name of EllisLab, Inc. shall not be
used in advertising or otherwise to promote the sale, use or other dealings
in this Software without prior written authorization from EllisLab, Inc.
*/

$plugin_info = array(
						'pi_name'			=> 'Plugin List',
						'pi_version'		=> '1.1',
						'pi_author'			=> 'Rick Ellis',
						'pi_author_url'		=> 'http://www.expressionengine.com/',
						'pi_description'	=> 'Allows you to show a simple list your installed plugins.',
						'pi_usage'			=> Plugin_list::usage()
					);


/**
 * Plugin List Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			ExpressionEngine Dev Team
 * @copyright		Copyright (c) 2004 - 2011, EllisLab, Inc.
 * @link			http://expressionengine.com/downloads/details/plugin_list/
 */
class Plugin_list {

	var $return_data;
     
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function Plugin_list()
	{
		$EE =& get_instance();
		
		$EE->load->helper('file');
	
	    $this->return_data = '';
		$which	= ( ! $EE->TMPL->fetch_param('which')) ? 'both' : $EE->TMPL->fetch_param('which');
		$ext_len = strlen(EXT);

		$plugin_files = array();
		
		if ($which == 'both' OR $which == 'main')
		{
			if (($list = get_filenames(PATH_PI)) !== FALSE)
			{
				foreach ($list as $file)
				{
					if (strncasecmp($file, 'pi.', 3) == 0 && substr($file, -$ext_len) == EXT && strlen($file) > strlen('pi.'.EXT))
					{
						$plugin_files[] = $file;
					}
				}
			}
		}
		
		if ($which == 'both' OR $which == 'thirdparty')
		{
			// third party, in packages
			if (($map = directory_map(PATH_THIRD)) !== FALSE)
			{
				foreach ($map as $pkg_name => $files)
				{
					if ( ! is_array($files))
					{
						$files = array($files);
					}

					foreach ($files as $file)
					{
						if (is_array($file))
						{
							// we're only interested in the top level files for the addon
							continue;
						}

						// we gots a plugin?
						if (strncasecmp($file, 'pi.', 3) == 0 && substr($file, -$ext_len) == EXT && strlen($file) > strlen('pi.'.EXT))
						{
							if (substr($file, 3, -$ext_len) == $pkg_name)
							{
								$plugin_files[] = $file;
							}
						}
					}
				}
			}
		}

		$plugins = array();

		foreach($plugin_files as $file)
		{
			if (eregi(EXT, $file) && $file !== '.' && $file !== '..' &&  substr($file, 0, 3) == 'pi.') 
			{
				$name = str_replace('pi.', '', $file);
				$name = str_replace(EXT, '', $name);
				$name = str_replace('_', ' ', $name);

				$plugins[] = ucfirst($name);
			}
		}         

		if (count($plugins) == 0)
		{
			return;
		}
		
		sort($plugins);
			
		$chunk = $EE->TMPL->tagdata;
		
		$str = '';
        
		foreach ($plugins as $val)
		{
			$str .= str_replace("{plugin_name}", $val, $chunk);
		}

		if ($EE->TMPL->fetch_param('backspace'))
		{            
			$str = substr($str, 0, - $EE->TMPL->fetch_param('backspace'));
		}

		$this->return_data = trim($str);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 *
	 * @access	public
	 * @return	string	plugin usage text
	 */
	function usage()
	{
		ob_start(); 
		?>
		The plugin syntax is as follows:

		{exp:plugin_list  backspace="6"}

		{plugin_name}<br />

		{/exp:plugin_list}

		Parameters:
		- backspace
		- which : chooses a plugin folder (main | thirdparty | both)


		Version 1.1
		******************
		- Updated plugin to be 2.0 compatible
		- Added which parameter


		<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.plugin_list.php */
/* Location: ./system/expressionengine/third_party/plugin_list/pi.plugin_list.php */