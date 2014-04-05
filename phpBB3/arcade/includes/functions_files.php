<?php
/**
*
* @package arcade
* @version $Id: functions_files.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/*
* Some of these function are borrowed from evil<3
* at http://www.phpbbmodders.net from the
* quickinstall mod for phpbb3
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Useful class for directory and file actions
 */
class file_functions
{
	function delete_list($dirs)
	{
		if (!sizeof($dirs))
		{
			return;
		}

		foreach ($dirs as $file)
		{
			if (is_dir($file))
			{
				$this->delete_dir($file);
			}
			else
			{
				if (file_exists($file))
				{
					$this->delete_file($file);
				}
			}
		}
	}

	function copy_file($src_file, $dst_file)
	{
		return copy($src_file, $dst_file);
	}

	function delete_file($file)
	{
		return unlink($file);
	}

	function move_file($src_file, $dst_file)
	{
		$this->copy_file($src_file, $dst_file);
		$this->delete_file($src_file);
	}

	function copy_dir($src_dir, $dst_dir)
	{
		$this->append_slash($src_dir);
		$this->append_slash($dst_dir);

		if (!is_dir($dst_dir))
		{
			mkdir($dst_dir);
		}

		foreach (scandir($src_dir) as $file)
		{
			if (in_array($file, array('.', '..', '.svn'), true))
			{
				continue;
			}

			$src_file = $src_dir . $file;
			$dst_file = $dst_dir . $file;

			if (is_file($src_file))
			{
				if (is_file($dst_file))
				{
					$ow = filemtime($src_file) - filemtime($dst_file);
				}
				else
				{
					$ow = 1;
				}

				if ($ow > 0)
				{
					if (copy($src_file, $dst_file))
					{
						touch($dst_file, filemtime($src_file));
					}
				}
			}
			else if (is_dir($src_file))
			{
				$this->copy_dir($src_file, $dst_file);
			}
		}
	}

	//$empty, $ignore and $ignore_index only apply to the directory passed as $dir
	function delete_dir($dir, $empty = false, $ignore = '', $ignore_index = false)
	{
		$this->append_slash($dir);

		if (!file_exists($dir) || !is_dir($dir) || !is_readable($dir))
		{
			return false;
		}

		foreach (scandir($dir) as $file)
		{

			if ($file == '.' || $file == '..' || $file == '.svn' || preg_match('#\.' . $ignore . '$#i', $file))
			{
				continue;
			}

			if ($ignore_index && ($file == 'index.html' || $file == 'index.htm'))
			{
				continue;
			}

			if (is_dir($dir . $file))
			{
				$this->delete_dir($dir . $file);
			}
			else
			{
				$this->delete_file($dir . $file);
			}
		}

		if (!$empty)
		{
			@rmdir($dir);
		}

		return true;
	}

	function move_dir($src_dir, $dst_dir)
	{
		$this->copy_dir($src_dir, $dst_dir);
		$this->delete_dir($src_dir);
	}

	function delete_files($dir, $files_ary, $recursive = true)
	{
		$this->append_slash($dir);

		foreach (scandir($dir) as $file)
		{
			if (in_array($file, array('.', '..'), true))
			{
				continue;
			}

			if (is_dir($dir . $file))
			{
				if ($recursive)
				{
					$this->delete_files($dir . $file, $files_ary, true);
				}
			}

			if (in_array($file, $files_ary, true))
			{
				if (is_dir($dir . $file))
				{
					$this->delete_dir($dir . $file);
				}
				else
				{
					$this->delete_file($dir . $file);
				}
			}
		}
	}

	function append_slash(&$dir)
	{
		if ($dir != '' && $dir[strlen($dir) - 1] != '/')
		{
			$dir .= '/';
		}
	}

	function remove_extension($file)
	{
		$ext = strrchr($file, '.');
		while ($ext !== false)
		{
			$file = substr($file, 0, -strlen($ext));
			$ext = strrchr($file, '.');
		}

		return $file;
	}

	function filesize($files)
	{
		// Seperate files from directories and calculate the size
		$filesize = 0;
		$files = (!is_array($files)) ? array($files) : $files;

		$dir_list = $file_list = array();
		if (is_array($files))
		{
			foreach ($files as $file)
			{
				if (is_dir($file))
				{
					$dir_list[] = $file;
				}
				else if(file_exists($file))
				{
					$filesize += filesize($file);
				}
			}
		}
		unset($files);

		// If there are directories listed we need to list the files and get the file size
		if (!empty($dir_list))
		{
			foreach ($dir_list as $dir)
			{
				$file_list = array_merge($file_list, $this->filelist('', $dir));
			}
			unset($dir_list);

			foreach ($file_list as $file)
			{
				if (file_exists($file))
				{
					$filesize += filesize($file);
				}
			}
			unset ($file_list);
		}

		return $filesize;
	}

	function filelist($path, $dir = '', $ignore = '', $ignore_index = true, $include_dir = false)
	{
		$list = array();
		$this->append_slash($dir);

		if (!file_exists($path . $dir))
		{
			return $list;
		}

		if ($files = scandir($path . $dir))
		{
			if ($include_dir)
			{
				$list[] = $dir;
			}

			foreach ($files as $file)
			{
				if ($file == '.' || $file == '..' || $file == '.svn' || preg_match('#\.' . $ignore . '$#i', $file))
				{
					continue;
				}

				if ($ignore_index && ($file == 'index.html' || $file == 'index.htm'))
				{
					continue;
				}

				if (is_dir($path . $dir . $file))
				{
					if ($include_dir)
					{
						$this->append_slash($file);
						$list[] = $dir . $file;
					}
					$list = array_merge($list, $this->filelist($path, $dir . $file, $ignore, $ignore_index));
				}
				else
				{
					$list[] = $dir . $file;
				}
			}
		}

		return $list;
	}

	function xml2array($url, $get_attributes = 1, $priority = 'tag')
	{
		$contents = "";

		if (!function_exists('xml_parser_create'))
		{
			return array ();
		}

		$parser = xml_parser_create('');

		if (!($fp = @ fopen($url, 'rb')))
		{
			return array ();
		}

		while (!feof($fp))
		{
			$contents .= fread($fp, 8192);
		}

		fclose($fp);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);

		if (!$xml_values)
		{
			return; //Hmm...
		}

		$xml_array = $parents = $opened_tags = $arr = $repeated_tag_index = array();
		$current = & $xml_array;

		foreach ($xml_values as $data)
		{
			unset ($attributes, $value);
			extract($data);
			$result = array ();
			$attributes_data = array ();

			if (isset ($value))
			{
				if ($priority == 'tag')
				{
					$result = $value;
				}
				else
				{
					$result['value'] = $value;
				}
			}

			if (isset ($attributes) and $get_attributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag')
					{
						$attributes_data[$attr] = $val;
					}
					else
					{
						$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
					}
				}
			}
			if ($type == "open")
			{
				$parent[$level -1] = & $current;
				if (!is_array($current) or (!in_array($tag, array_keys($current))))
				{
					$current[$tag] = $result;
					if ($attributes_data)
					{
						$current[$tag . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag . '_' . $level] = 1;
					$current = & $current[$tag];
				}
				else
				{
					if (isset ($current[$tag][0]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						$current[$tag] = array($current[$tag], $result);
						$repeated_tag_index[$tag . '_' . $level] = 2;
						if (isset ($current[$tag . '_attr']))
						{
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset ($current[$tag . '_attr']);
						}
					}
					$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
					$current = & $current[$tag][$last_item_index];
				}
			}
			elseif ($type == "complete")
			{
				if (!isset ($current[$tag]))
				{
					$current[$tag] = $result;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributes_data)
					{
						$current[$tag . '_attr'] = $attributes_data;
					}
				}
				else
				{
					if (isset ($current[$tag][0]) and is_array($current[$tag]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						if ($priority == 'tag' and $get_attributes and $attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						$current[$tag] = array ($current[$tag], $result);
						$repeated_tag_index[$tag . '_' . $level] = 1;
						if ($priority == 'tag' and $get_attributes)
						{
							if (isset ($current[$tag . '_attr']))
							{
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset ($current[$tag . '_attr']);
							}
							if ($attributes_data)
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				}
			}
			elseif ($type == 'close')
			{
				$current = & $parent[$level -1];
			}
		}
		return ($xml_array);
	}
}

?>