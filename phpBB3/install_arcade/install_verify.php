<?php
/**
*
* @package install
* @version $Id: install_verify.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/
if (!defined('IN_INSTALL') || !defined('IN_PHPBB'))
{
	exit;
}

if (!empty($setmodules))
{
	if (!$this->installed_version || $this->installed_version != $arcade_mod_config['version']['current'])
	{
		return;
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'VERIFY',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 40,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'VERIFY'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_verify extends module
{
	function install_verify(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $user, $template, $phpbb_root_path;

		switch ($sub)
		{
			case 'intro':
				$this->page_title = $user->lang['SUB_INTRO'];

				$template->assign_vars(array(
					'TITLE'		=> $user->lang['VERIFY_INTRO'],
					'BODY'		=> $user->lang['VERIFY_INTRO_BODY'],
					'L_SUBMIT'	=> $user->lang['NEXT_STEP'],
					'U_ACTION'	=> $this->p_master->module_url . "?mode=$mode&amp;sub=verify",
				));

			break;

			case 'verify':
				$this->verify($mode, $sub);

			break;
		}

		$this->tpl_name = 'install_install';
	}

	/**
	* Checks that the server we are installing on meets the requirements for running phpBB
	*/
	function verify($mode, $sub)
	{
		global $user, $config, $arcade_mod_config, $template, $phpbb_root_path, $phpEx, $db;

		$this->page_title = $user->lang['STAGE_VERIFY'];

		$passed = $this->p_master->requirements($mode, $sub);

		$template->assign_block_vars('checks', array(
			'S_LEGEND'		=> true,
			'LEGEND'		=> $user->lang['VERIFY_ARCADE_INSTALLATION'],
			'LEGEND_EXPLAIN'=> $user->lang['VERIFY_ARCADE_INSTALLATION_EXPLAIN'],
		));

		// Check files exist
		$error = array();
		foreach ($arcade_mod_config['verify']['files']['core'] as $file)
		{
			if (!file_exists($phpbb_root_path . $file))
			{
				$error[] = 'phpbb_root_path/' . $file;
			}
		}

		// We will only check for files from the languages that are installed
		$sql = 'SELECT lang_id, lang_dir FROM ' . LANG_TABLE;
		$result = $db->sql_query($sql);

		$installed_langs = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$installed_langs[$row['lang_id']] = $row['lang_dir'];
		}
		$db->sql_freeresult($result);

		foreach ($installed_langs as $dir)
		{
			if (isset($arcade_mod_config['verify']['files']['langs'][$dir]))
			{
				foreach ($arcade_mod_config['verify']['files']['langs'][$dir] as $file)
				{
					if (!file_exists($phpbb_root_path . $file))
					{
						$error[] = 'phpbb_root_path/' . $file;
					}
				}
			}
		}

		// We will only check for files from the styles that are installed
		$sql = 'SELECT template_id, template_name FROM ' . STYLES_TEMPLATE_TABLE;
		$result = $db->sql_query($sql);

		$installed_templates = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$installed_templates[$row['template_id']] = $row['template_name'];
		}
		$db->sql_freeresult($result);

		foreach ($installed_templates as $name)
		{
			if (isset($arcade_mod_config['verify']['files']['styles'][$name]))
			{
				foreach ($arcade_mod_config['verify']['files']['styles'][$name] as $file)
				{
					if (!file_exists($phpbb_root_path . $file))
					{
						$error[] = 'phpbb_root_path/' . $file;
					}
				}
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_MISSING_FILES'],  '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
		}
		else
		{
			$result = '<strong style="color:green">' . $user->lang['VERIFY_ALL_FILES'] . '</strong>';
		}

		unset($error);

		$template->assign_block_vars('checks', array(
			'TITLE'		=> $user->lang['VERIFY_FILES_EXIST'],
			'RESULT'	=> $result,

			'S_EXPLAIN'	=> false,
			'S_LEGEND'	=> false,
		));

		$error = array();
		foreach ($installed_templates as $name)
		{
			$file_path = $phpbb_root_path . 'styles/' . $name . '/template/arcade/play_body.html';

			if (file_exists($file_path))
			{
				if ($content = @file_get_contents($file_path))
				{
					if (strpos($content, $arcade_mod_config['verify']['files']['new_html_key']) === false)
					{
						$error[] = 'styles/' . $name . '/template/arcade/';
					}
				}
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_FOLDER_NOT_UPDATE'],  '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';

			$template->assign_block_vars('checks', array(
				'TITLE'		=> $user->lang['VERIFY_FOLDER_UPDATE'],
				'RESULT'	=> $result,

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}
		unset($error);

		// Check if old files exist
		$error = array();
		$result = false;
		foreach ($arcade_mod_config['verify']['old_files']['core'] as $file)
		{
			if (file_exists($phpbb_root_path . $file))
			{
				$error[] = 'phpbb_root_path/' . $file;
			}
		}

		foreach ($installed_templates as $name)
		{
			if (isset($arcade_mod_config['verify']['old_files']['styles'][$name]))
			{
				foreach ($arcade_mod_config['verify']['old_files']['styles'][$name] as $file)
				{
					if (file_exists($phpbb_root_path . $file))
					{
						$error[] = 'phpbb_root_path/' . $file;
					}
				}
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_OLD_FILES_PRESENT'],  '<span style="color:red; font-weight:normal;">' . '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>' . '</span>') . '</span>';
		}
		else
		{
			$result = false;
		}
		unset($error);

		if ($result)
		{
			$template->assign_block_vars('checks', array(
				'TITLE'		=> $user->lang['VERIFY_OLD_FILES'],
				'RESULT'	=> $result,

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}

		// Check if tables exist
		$error = array();
		$tables = get_tables($db);
		foreach ($arcade_mod_config['verify']['tables'] as $table_name)
		{
			if (!in_array($table_name, $tables))
			{
				$error[] = $table_name;
			}
		}
		unset($tables);

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_MISSING_TABLES'],  '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
		}
		else
		{
			$result = '<strong style="color:green">' . $user->lang['VERIFY_ALL_TABLES'] . '</strong>';
		}
		unset($error);

		$template->assign_block_vars('checks', array(
			'TITLE'		=> $user->lang['VERIFY_TABLES_EXIST'],
			'RESULT'	=> $result,

			'S_EXPLAIN'	=> false,
			'S_LEGEND'	=> false,
		));

		// Check files edits exist
		$error = array();
		foreach ($arcade_mod_config['verify']['edits']['core'] as $key => $value)
		{
			if ($content = @file_get_contents($phpbb_root_path . $key))
			{
				foreach ($value as $edit)
				{
					if (strpos($content, $edit) === false)
					{
						$error[] = 'phpbb_root_path/' . $key . '<br /><textarea rows="8" cols="40" readonly="readonly" onclick="this.focus(); this.select();">'.htmlspecialchars($edit).'</textarea>';
					}
				}
			}
			else
			{
				$error[] = 'phpbb_root_path/' . $key . ' - <span style="color: black;">' . $user->lang['NOT_FOUND'] . '</span>';
			}
		}

		foreach ($installed_templates as $name)
		{
			if (isset($arcade_mod_config['verify']['edits']['styles'][$name]))
			{
				foreach ($arcade_mod_config['verify']['edits']['styles'][$name] as $key => $value)
				{
					if ($content = @file_get_contents($phpbb_root_path . $key))
					{
						foreach ($value as $edit)
						{
							if (strpos($content, $edit) === false)
							{
								$error[] = 'phpbb_root_path/' . $key . '<br /><textarea onclick="this.focus(); this.select();" rows="8" cols="40">'.htmlspecialchars($edit).'</textarea>';
							}
						}
					}
					else
					{
						$error[] = 'phpbb_root_path/' . $key . ' - <span style="color: black;">' . $user->lang['NOT_FOUND'] . '</span>';
					}
				}
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_MISSING_FILES_EDITED'],  '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
		}
		else
		{
			$result = '<strong style="color:green">' . $user->lang['VERIFY_ALL_FILES_EDITED'] . '</strong>';
		}
		unset($error);

		$template->assign_block_vars('checks', array(
			'TITLE'		=> $user->lang['VERIFY_FILES_EDITED'],
			'RESULT'	=> $result,

			'S_EXPLAIN'	=> false,
			'S_LEGEND'	=> false,
		));

		// Check other db data
		$error = array();
		$result = false;
		foreach ($arcade_mod_config['verify']['alter_db']['current'] as $key => $value)
		{
			$table = $key;
			foreach ($value as $column)
			{
				$sql = 'SELECT ' . $column . '
					FROM ' . $table;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ($db->sql_error_triggered)
				{
					$db->sql_error_triggered = false;
					$error[] = $column;
				}
				unset($row);
			}

			if (sizeof($error))
			{
				$passed['mod'] = false;
				$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_TABLE_NOT_ALTERED'], $table, '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
			}
			else
			{
				$result = '<strong style="color:green">' . sprintf($user->lang['VERIFY_TABLE_ALTERED'], $table) . '</strong>';
			}
			unset($error);
		}

		if ($result)
		{
			$template->assign_block_vars('checks', array(
				'TITLE'		=> $user->lang['VERIFY_OTHER_DB_DATA'],
				'RESULT'	=> $result,

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}

		// Check other db data
		$error = array();
		$result = false;
		foreach ($arcade_mod_config['verify']['alter_db']['old'] as $key => $value)
		{
			$table = $key;
			foreach ($value as $column)
			{
				$sql = 'SELECT ' . $column . '
					FROM ' . $table;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ($db->sql_error_triggered)
				{
					$db->sql_error_triggered = false;
				}
				else
				{
					$error[] = $column;
				}
				unset($row);
			}

			if (sizeof($error))
			{
				$passed['mod'] = false;
				$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_OLD_TABLE_ALTERED'], $table, '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
			}
			else
			{
				$result = false;
			}
			unset($error);
		}

		if ($result)
		{
			$template->assign_block_vars('checks', array(
				'TITLE'		=> $user->lang['VERIFY_OLD_OTHER_DB_DATA'],
				'RESULT'	=> $result,

				'S_EXPLAIN'	=> false,
				'S_LEGEND'	=> false,
			));
		}

		// Check if modules are present
		$error = array();

		foreach ($arcade_mod_config['verify']['modules'] as $title => $modules)
		{
			foreach ($modules as $key => $value)
			{
				$module_basename = $key;
				foreach ($value as $module_mode)
				{
					$sql = 'SELECT parent_id
						FROM ' . MODULES_TABLE . "
						WHERE module_basename = '" . $db->sql_escape($module_basename) . "'
						AND module_class = '" . $db->sql_escape($title) . "'
						AND module_mode  = '" . $db->sql_escape($module_mode) . "'";
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if (!$row)
					{
						$error[] = $user->lang['MODULE_' . strtoupper($title)] . ': module_basename = ' . $module_basename . ', module_mode = ' . $module_mode;
					}
					unset($row);
				}
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_MISSING_MODULES'], '<span style="color:red; font-weight:normal;">' . implode('<br /><br />', $error) . '</span>') . '</span>';
		}
		else
		{
			$result = '<strong style="color:green">' . $user->lang['VERIFY_ALL_MODULES'] . '</strong>';
		}
		unset($error);

		$template->assign_block_vars('checks', array(
			'TITLE'		=> $user->lang['VERIFY_MODULES'],
			'RESULT'	=> $result,

			'S_EXPLAIN'	=> false,
			'S_LEGEND'	=> false,
		));

		// Check if permissions exist
		$error = array();
		foreach ($arcade_mod_config['permission_options']['phpbb'] as $phpbb_auth)
		{
			foreach ($phpbb_auth as $value)
			{
				$sql = 'SELECT auth_option_id
					FROM ' . ACL_OPTIONS_TABLE . "
					WHERE auth_option = '" . $db->sql_escape($value) . "'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					$error[] = $value;
				}
				unset($row);
			}
		}

		foreach ($arcade_mod_config['permission_options']['arcade'] as $arcade_auth)
		{
			foreach ($arcade_auth as $value)
			{
				$sql = 'SELECT auth_option_id
					FROM ' . ACL_ARCADE_OPTIONS_TABLE . "
					WHERE auth_option = '" . $db->sql_escape($value) . "'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row || $db->sql_error_triggered)
				{
					$db->sql_error_triggered = false;
					$error[] = $value;
				}
				unset($row);
			}
		}

		if (sizeof($error))
		{
			$passed['mod'] = false;
			$result = '<span style="color:#000; font-weight:bold;">' . sprintf($user->lang['VERIFY_MISSING_PERMISSIONS'], '<span style="color:red; font-weight:normal;">' . implode('<br />', $error) . '</span>') . '</span>';
		}
		else
		{
			$result = '<strong style="color:green">' . $user->lang['VERIFY_ALL_PERMISSIONS'] . '</strong>';
		}
		unset($error);

		$template->assign_block_vars('checks', array(
			'TITLE'		=> $user->lang['VERIFY_PERMISSIONS'],
			'RESULT'	=> $result,

			'S_EXPLAIN'	=> false,
			'S_LEGEND'	=> false,
		));

		$title = (!in_array(false, $passed)) ? $user->lang['INSTALL_CONGRATS'] : $user->lang['VERIFY_ERRORS'];
		$body = (!in_array(false, $passed)) ? sprintf($user->lang['VERIFY_CONGRATS_EXPLAIN'], $arcade_mod_config['version']['current']) : sprintf($user->lang['VERIFY_ERRORS_EXPLAIN'], $arcade_mod_config['version']['current']);
		$url = (!in_array(false, $passed)) ? append_sid("{$phpbb_root_path}adm/index.$phpEx", 'i=arcade_main', true, $user->session_id) : $this->p_master->module_url . "?mode=$mode&amp;sub=verify";
		$submit = (!in_array(false, $passed)) ? $user->lang['INSTALL_LOGIN'] : $user->lang['INSTALL_TEST'];

		$template->assign_vars(array(
			'TITLE'		=> $title,
			'BODY'		=> $body,
			'L_SUBMIT'	=> $submit,
			'U_ACTION'	=> $url,
		));
	}
}

?>