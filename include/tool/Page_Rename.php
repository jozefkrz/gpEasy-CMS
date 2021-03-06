<?php
defined('is_running') or die('Not an entry point...');


class gp_rename{

	/**
	 * Display form in popup for renaming page given by $index
	 *
	 */
	public static function RenameForm( $index, $action ){
		global $langmessage, $page, $gp_index, $gp_titles, $config;


		$label			= common::GetLabelIndex($index);
		$title			= common::IndexToTitle($index);
		$title_info		= $gp_titles[$index];

		if( empty($_REQUEST['new_title']) ){
			$new_title = common::LabelSpecialChars($label);
		}else{
			$new_title = htmlspecialchars($_REQUEST['new_title']);
		}
		$new_title = str_replace('_',' ',$new_title);


		//show more options?
		$hidden_rows = false;

		ob_start();
		echo '<div class="inline_box">';
		echo '<form action="'.$action.'" method="post" id="gp_rename_form">';

		echo '<input type="hidden" name="title" id="old_title" value="'.htmlspecialchars($title).'" />';
		echo '<input type="hidden" id="gp_space_char" value="'.htmlspecialchars($config['space_char']).'" />';


		echo '<h2>'.$langmessage['rename/details'].'</h2>';


		echo '<table class="bordered full_width" id="gp_rename_table">';
		echo '<thead>';
		echo '<tr><th colspan="2">';
		echo $langmessage['options'];
		echo '</th></tr>';
		echo '</thead>';

		//label
		echo '<tbody>';
		echo '<tr><td class="formlabel">'.$langmessage['label'].'</td>';
		echo '<td><input type="text" class="title_label gpinput" name="new_label" maxlength="100" size="50" value="'.$new_title.'" />';
		echo '</td></tr>';


		//slug (title)
		$attr		= '';
		$class		= 'new_title';

		if( $title == admin_tools::LabelToSlug($label) ){
			$attr = 'disabled="disabled" ';
			$class .= ' sync_label';
		}
		echo '<tr><td class="formlabel">'.$langmessage['Slug/URL'];
		echo '</td><td>';
		echo '<input type="text" class="'.$class.' gpinput" name="new_title" maxlength="100" size="50" value="'.htmlspecialchars($title).'" '.$attr.'/>';
		echo ' <div class="label_synchronize">';
		if( empty( $attr ) ){
			echo '<a data-cmd="ToggleSync">'.$langmessage['sync_with_label'].'</a>';
			echo '<a data-cmd="ToggleSync" class="slug_edit nodisplay">'.$langmessage['edit'].'</a>';
		}else{
			echo '<a data-cmd="ToggleSync" class="nodisplay">'.$langmessage['sync_with_label'].'</a>';
			echo '<a data-cmd="ToggleSync" class="slug_edit">'.$langmessage['edit'].'</a>';
		}
		echo '</div>';
		echo '</td></tr>';



		//browser title defaults to label
		$attr		= '';
		$class		= 'browser_title';
		if( isset($title_info['browser_title']) ){
			echo '<tr>';
			$browser_title = $title_info['browser_title'];
		}else{
			echo '<tr class="nodisplay">';
			$hidden_rows = true;
			$browser_title = $label;
			$attr = 'disabled="disabled" ';
			$class .= ' sync_label';
		}
		echo '<td class="formlabel">';
		echo $langmessage['browser_title'];
		echo '</td><td>';
		echo '<input type="text" class="'.$class.' gpinput" size="50" name="browser_title" value="'.$browser_title.'" '.$attr.'/>';
		echo ' <div class="label_synchronize">';
		if( empty( $attr ) ){
			echo '<a data-cmd="ToggleSync">'.$langmessage['sync_with_label'].'</a>';
			echo '<a data-cmd="ToggleSync" class="slug_edit nodisplay">'.$langmessage['edit'].'</a>';
		}else{
			echo '<a data-cmd="ToggleSync" class="nodisplay">'.$langmessage['sync_with_label'].'</a>';
			echo '<a data-cmd="ToggleSync" class="slug_edit">'.$langmessage['edit'].'</a>';
		}
		echo '</div>';
		echo '</td></tr>';


		//meta keywords
		$keywords = '';
		if( isset($title_info['keywords']) ){
			echo '<tr>';
			$keywords = $title_info['keywords'];
		}else{
			echo '<tr class="nodisplay">';
			$hidden_rows = true;
		}
		echo '<td class="formlabel">';
		echo $langmessage['keywords'];
		echo '</td><td>';
		echo '<input type="text" class="gpinput" size="50" name="keywords" value="'.$keywords.'" />';
		echo '</td></tr>';


		//meta description
		$description = '';
		if( isset($title_info['description']) ){
			echo '<tr>';
			$description = $title_info['description'];
		}else{
			echo '<tr class="nodisplay">';
			$hidden_rows = true;
		}
		echo '<td class="formlabel">';
		echo $langmessage['description'];
		echo '</td><td>';
		echo '<textarea class="gptextarea show_character_count" rows="2" cols="50" name="description">'.$description.'</textarea>';

		$count_label = sprintf($langmessage['_characters'],'<span>'.strlen($description).'</span>');
		echo '<div class="character_count">'.$count_label.'</div>';

		echo '</td></tr>';


		//robots
		$rel = '';
		if( isset($title_info['rel']) ){
			echo '<tr>';
			$rel = $title_info['rel'];
		}else{
			echo '<tr class="nodisplay">';
			$hidden_rows = true;
		}
		echo '<td class="formlabel">';
		echo $langmessage['robots'];
		echo '</td><td>';

		echo '<label>';
		$checked = (strpos($rel,'nofollow') !== false) ? 'checked="checked"' : '';
		echo '<input type="checkbox" name="nofollow" value="nofollow" '.$checked.'/> ';
		echo '  Nofollow ';
		echo '</label>';

		echo '<label>';
		$checked = (strpos($rel,'noindex') !== false) ? 'checked="checked"' : '';
		echo '<input type="checkbox" name="noindex" value="noindex" '.$checked.'/> ';
		echo ' Noindex';
		echo '</label>';

		echo '</td></tr>';

		echo '</tbody>';
		echo '</table>';


		//redirection
		echo '<p id="gp_rename_redirect" class="nodisplay">';
		echo '<label>';
		echo '<input type="checkbox" name="add_redirect" value="add" /> ';
		echo sprintf($langmessage['Auto Redirect'],'"'.$title.'"');
		echo '</label>';
		echo '</p>';

		echo '<p>';
		if( $hidden_rows )  echo ' &nbsp; <a data-cmd="showmore" >+ '.$langmessage['more_options'].'</a>';
		echo '</p>';

		echo '<p>';
		echo '<input type="hidden" name="cmd" value="renameit"/> ';
		echo '<input type="submit" name="" value="'.$langmessage['save_changes'].'...'.'" class="gpsubmit" data-cmd="gppost"/>';
		echo '<input type="button" class="admin_box_close gpcancel" name="" value="'.$langmessage['cancel'].'" />';
		echo '</p>';

		echo '</form>';
		echo '</div>';

		$content = ob_get_clean();

		$page->ajaxReplace = array();


		$array = array();
		$array[0] = 'admin_box_data';
		$array[1] = '';
		$array[2] = $content;
		$page->ajaxReplace[] = $array;



		//call renameprep function after admin_box
		$array = array();
		$array[0] = 'renameprep';
		$array[1] = '';
		$array[2] = '';
		$page->ajaxReplace[] = $array;


	}

	/**
	 * Handle renaming a page based on POSTed data
	 *
	 */
	public static function RenameFile($title){
		global $langmessage, $page, $gp_index, $gp_titles;

		$page->ajaxReplace = array();


		//change the title
		$title = gp_rename::RenameFileWorker($title);
		if( $title === false ){
			return false;
		}


		if( !isset($gp_index[$title]) ){
			msg($langmessage['OOPS']);
			return false;
		}

		$id = $gp_index[$title];
		$title_info = &$gp_titles[$id];

		//change the label
		$title_info['label'] = admin_tools::PostedLabel($_POST['new_label']);
		if( isset($title_info['lang_index']) ){
			unset($title_info['lang_index']);
		}


		//change the browser title
		$auto_browser_title = strip_tags($title_info['label']);
		$custom_browser_title = false;
		if( isset($_POST['browser_title']) ){
			$browser_title = $_POST['browser_title'];
			$browser_title = htmlspecialchars($browser_title);

			if( $browser_title != $auto_browser_title ){
				$title_info['browser_title'] = trim($browser_title);
				$custom_browser_title = true;
			}
		}
		if( !$custom_browser_title ){
			unset($title_info['browser_title']);
		}

		//keywords
		if( isset($_POST['keywords']) ){
			$title_info['keywords'] = htmlspecialchars($_POST['keywords']);
			if( empty($title_info['keywords']) ){
				unset($title_info['keywords']);
			}
		}


		//description
		if( isset($_POST['description']) ){
			$title_info['description'] = htmlspecialchars($_POST['description']);
			if( empty($title_info['description']) ){
				unset($title_info['description']);
			}
		}


		//robots
		$title_info['rel'] = '';
		if( isset($_POST['nofollow']) ){
			$title_info['rel'] = 'nofollow';
		}
		if( isset($_POST['noindex']) ){
			$title_info['rel'] .= ',noindex';
		}
		$title_info['rel'] = trim($title_info['rel'],',');
		if( empty($title_info['rel']) ) unset($title_info['rel']);


		if( !admin_tools::SavePagesPHP() ){
			msg($langmessage['OOPS'].' (R1)');
			return false;
		}

		msg($langmessage['SAVED']);
		return $title;
	}



	private static function RenameFileWorker($title){
		global $langmessage,$dataDir,$gp_index;

		//use new_label or new_title
		if( isset($_POST['new_title']) ){
			$new_title = admin_tools::PostedSlug($_POST['new_title']);
		}else{
			$new_title = admin_tools::LabelToSlug($_POST['new_label']);
		}

		//title unchanged
		if( $new_title == $title ){
			return $title;
		}

		$special_file = false;
		if( common::SpecialOrAdmin($title) !== false ){
			$special_file = true;
		}

		if( !admin_tools::CheckTitle($new_title,$message) ){
			msg($message);
			return false;
		}

		$old_gp_index = $gp_index;

		//re-index: make the new title point to the same data index
		$old_file = gpFiles::PageFile($title);
		$file_index = $gp_index[$title];
		unset($gp_index[$title]);
		$gp_index[$new_title] = $file_index;


		//rename the php file
		if( !$special_file ){
			$new_file = gpFiles::PageFile($new_title);

			//if the file being renamed doesn't use the index naming convention, then we'll still need to rename it
			if( $new_file != $old_file ){
				$new_dir = dirname($new_file);
				$old_dir = dirname($old_file);
				if( !gpFiles::Rename($old_dir,$new_dir) ){
					msg($langmessage['OOPS'].' (N3)');
					$gp_index = $old_gp_index;
					return false;
				}
			}

			//gallery rename
			includeFile('special/special_galleries.php');
			special_galleries::RenamedGallery($title,$new_title);
		}


		//create a 301 redirect
		if( isset($_POST['add_redirect']) && $_POST['add_redirect'] == 'add' ){
			includeFile('admin/admin_missing.php');
			admin_missing::AddRedirect($title,$new_title);
		}


		gpPlugin::Action('RenameFileDone',array($file_index, $title, $new_title));

		return $new_title;
	}

	/**
	 * Rename a page
	 *
	 */
	public static function RenamePage($page){
		global $langmessage, $gp_index;

		includeFile('tool/Page_Rename.php');
		$new_title = gp_rename::RenameFile($page->title);
		if( ($new_title !== false) && $new_title != $page->title ){
			msg(sprintf($langmessage['will_redirect'],common::Link_Page($new_title)));

			$page->head				.= '<meta http-equiv="refresh" content="15;url='.common::GetUrl($new_title).'">';
			$page->ajaxReplace[]	= array('location',common::GetUrl($new_title),15000);
			return true;
		}
		return false;
	}

}
