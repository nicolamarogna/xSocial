<?php
class Board {

	public function __construct()
	{
		//delete message
		if ((isset($_POST['action'])) && ($_POST['action'] == 'delete_post')) {
			$db = new Db();
			$item = $db->get_by_id('social_status', $_POST['id']);
			$img = $item->img;
			$this->del_msg($_POST['id'], $img);
		}
		
		//share
		if (isset($_GET['action']) && ($_GET['action'] == 'share')) {
			$db = new Db();
			$post = $db->get_by_id('social_status', $_GET['id']);
			if ($post->img) {
				$img_name = Utils::clone_file('thumb_'.$post->img);
				$img_name = Utils::clone_file($post->img);
			} else {
				$img_name = '';
			}
			$array = array(
					'from_user' => $_SESSION['user']->id,
					'to_user' => $_SESSION['user']->id,
					'status' => $post->status,
					'img' => $img_name,
					'youtube' => $post->youtube,
					'share' => $post->from_user
					);
			$db->insert('social_status', $array);
			unset($_POST);
			header('Location: '.$_SERVER['HTTP_REFERER']);
			die;
		}
		
		//delete comment
		if ((isset($_POST['action'])) && ($_POST['action'] == 'delete_comment')) {
		//if (isset($_POST['del_comment_id'])) {
			$this->del_comment($_POST['id']);
			/*
			unset($_POST);
			header('Location: '.$_SERVER['HTTP_REFERER']);
			die;
			*/
		}
		
		//insert comment
		if (isset($_POST['comment']) && !empty($_POST['comment'])) {
			$mod = new Db();
			$this->insert_comment($_POST['id_comment'], $_POST['comment']);
			//notify user if not am I and if already comment by me
			$check = $this->check_if_commented($_POST['id_comment'], $_POST['to_user']);
			if ((isset($_POST['in_board_of'])) && (Utils::isgroup($_POST['in_board_of']))) {
				$group = $mod->get_by_id('social_users', $_POST['in_board_of']);
				$type = 'comment_answer_group';
				$to_user = $group->group_admin;
			} else {
				$type = 'comment_answer';
				$to_user = $_POST['to_user'];
			}
			if ($check) {
				//$type = (isgroup($_GET['userboard'])) ? 'comment_answer_group' : 'comment_answer';
				foreach ($check as $i) {
					$mod->notify($_SESSION['user']->id, $i->id_user, $type, $_POST['id_comment']);
				}
				//notify me too
				$mod->notify($_SESSION['user']->id, $to_user, $type, $_POST['id_comment']);
			} else {
				//$type = (Utils::isgroup($_POST['to_user'])) ? 'comment_group' : 'comment';
				if ($_GET['detail']) { $_POST['in_board_of'] = $_POST['to_user']; } else { $_POST['in_board_of'] = $to_user; }
					//echo '*****'.$_POST['in_board_of'];			die;
					$type = (Utils::isgroup($_POST['in_board_of'])) ? 'comment_group' : 'comment';
				//notify the author of the post
				if (($to_user != $_SESSION['user']->id) && (!Utils::isgroup($_POST['in_board_of']))) {
					$mod->notify($_SESSION['user']->id, $to_user, $type, $_POST['id_comment']);
				}
				//also notify userboard if the post is into another user
				if ((isset($_POST['in_board_of'])) && ($_POST['in_board_of'] != $to_user)) {
					$mod->notify($_SESSION['user']->id, $_POST['in_board_of'], $type, $_POST['id_comment']);
				}
			}
			//header('Location: '.$_SERVER['HTTP_REFERER']);
		}
		
		//store rating
		if ((isset($_POST['action'])) && ($_POST['action'] == 'store_rating')) {
			$db = new Db;

			$already_voted = $db->query('SELECT * FROM social_ratings WHERE id_status = '.$_POST['id_status']);
			$element = $db->get_by_id('social_status', $_POST['id_status']);
			
			//caluculate the average
			if ($already_voted[0]) {
				$average = ($element->rating_average+$_POST['rating'])/2;
			} else {
				$average = $element->rating_average+$_POST['rating'];
			}
			
			$post_update = array(
				'rating_average' => $average,
				'rating_numbers' => $element->rating_numbers + 1
				);
			
			$post_insert = array(
					'id_user' => $_SESSION['user']->id,
					'id_status' => $_POST['id_status'],
					);
			$db->update('social_status', $_POST['id_status'], $post_update);	
			$db->insert('social_ratings', $post_insert);
			
			//$this->ratings($_POST['id_status'], $element->rating_numbers + 1, $average);
		}
	}
	
	//my status
	public function mystatus() {
		$mod = new Db();
		$msg = $mod->query('SELECT * FROM social_status
							WHERE social_status.from_user = '.$_SESSION['user']->id.' AND social_status.to_user = '.$_SESSION['user']->id.'
							ORDER BY updated DESC LIMIT 1');
		
		if (Utils::isgroup($_SESSION['user']->id)) {
			echo '<div id="head_under"><i class="fa fa-copyright fa-rotate-90 fright" aria-hidden="true"></i> '.stripslashes($_SESSION['user']->group);
		} elseif ((isset($_GET['type'])) && ($_GET['type'] == 'all')) {
			echo '<div id="head_under"><i class="fa fa-users fright" aria-hidden="true"></i> Notizie';
		} else {
			echo '<div id="head_under"><i class="fa fa-user fright" aria-hidden="true"></i> '.stripslashes($_SESSION['user']->nome).' '.stripslashes($_SESSION['user']->cognome);
		}
		
		if (isset($_GET['type']) && ($msg && $_GET['type'] != 'all')) {
			echo '<p class="head_under_status">'.stripslashes(nl2br($msg[0]->status)).'</p>
				<div id="foot_status">'.Utils::f_date($msg[0]->updated).'</div>';
		}
		echo '</div>';
	}
	
	//status of any user
	public function userstatus($id) {
		$mod = new Db();		
		$user = $mod->get_by_id('social_users',$id);
		
		//redirect if user doesn't exist
		if (!$user) { header('Location: '.BASE_URL.'?p=msg&msg=not_found'); }
		
		$admin = $mod->get_by_id('social_users', $user->group_admin);
		$friend = Utils::isfriend($id);
		$msg = $mod->query('SELECT * FROM social_status
							WHERE social_status.from_user = '.$id.' AND social_status.to_user = '.$id.'
							ORDER BY updated DESC LIMIT 1');
		
		
		if (Utils::isgroup($id)) {
			echo '<div id="head_under"><i class="fa fa-copyright fa-rotate-90 fright" aria-hidden="true"></i>'.stripslashes($user->group);
			echo '<div id="foot_status">Amministratore: <a class="bold" href="?userboard='.$admin->id.'">'.stripslashes($admin->nome).' '.stripslashes($admin->cognome).'</a></div>';
		} else {
			echo '<div id="head_under"><i class="fa fa-user fright" aria-hidden="true"></i>'.stripslashes($user->nome).' '.stripslashes($user->cognome);
		}
		
		if ($friend == 1) {
			if ($msg) {
				echo '<p class="head_under_status">'.stripslashes(nl2br($msg[0]->status)).'</p>
					<div id="foot_status">'.Utils::f_date($msg[0]->updated).'</div>';
			}
		}
		echo '</div>';
	}
	
	public function statusbox() {
		if ((!isset($_GET['userboard'])) || ($_GET['userboard'] == $_SESSION['user']->id)){
			$placeholder = 'A cosa stai pensando '.$_SESSION['user']->nome.'?';
		} else {
			$placeholder = 'Scrivi qualcosa...';
		}
		
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER['HTTP_REFERER'],
			'name' => 'from'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => (isset($_GET['userboard'])) ? $_GET['userboard'] : '',
			'name' => 'to_user'
			);
		$fields[] = array(
			'label' => NULL,
			'type' => 'textarea_liveurl',
			'value' => '',
			'name' => 'statusbox',
			'extra' => 'class="statusbox checkIfEmpty" placeholder="'.$placeholder.'"',
			);
		$fields[] = array(
			'label' => NULL,
			'type' => 'hidden',
			'value' => '',
			'name' => 'statusbox_extra',
			);
		$fields[] = array(
			'label' => null,
			'type' => 'file_u_pop', 
			'value' => '',
			'name' => 'img',
			'extra' => 'class="buttonGrey openFileDialog" id="img"',
			);
		/*
		$fields[] = array(
			'label' => null,
			'type' => 'youtube_u_pop', 
			'value' => '',
			'name' => 'youtube',
			'extra' => 'class="swing buttonGrey"',
			);
		*/
		//on submit
		if ((isset($_POST['statusbox']) && !empty($_POST['statusbox'])) || (isset($_FILES['img']['name']))) {
		$e = Form::validation($fields);
			if ($e) {
				$this->store_statusbox($_POST);
				header('Location: '.$_SERVER['HTTP_REFERER']);
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		if (isset($_GET['userboard']) && ($_GET['userboard'] != $_SESSION['user']->id)) {
			$friend = Utils::isfriend($_GET['userboard']);
		} else {
			//i'm in my board
			$friend = 1;
		}
		
		if ($friend == 1) {
			//prepare form
			$output = '<div id="status_msg">';
			$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array(NULL,'<i class="fa fa-flag" aria-hidden="true"></i> Pubblica'), 'post', 'enctype="multipart/form-data" ', 'id="publishButton" ');
			$output .= '</div>';
			
			echo $output;
		}
	}
	
	public function store_statusbox() {
		$mod = new Db();
		$to_user = ($_POST['to_user'] == '') ? $_SESSION['user']->id : $_POST['to_user'];
		
		$path = ROOT.'files/img/';
						
		//on file insert, upload file
		if ($_FILES['img']['name']) {
			$filename = Utils::upload('img', ROOT.'files/');
			$thumb = Utils::create_resized($path.$filename, $path.'thumb_'.$filename, array(120,120));
		}
		
		$post[] = array(
					'from_user' => $_SESSION['user']->id,
					'to_user' => $to_user,
					'status' => Utils::textToLink($_POST['statusbox']),
					'status_extra' => $_POST['statusbox_extra'],
					'img' => $filename,
					'youtube' => $_POST['youtube'],
					);
		
		$mod->insert('social_status', $post);
		
		//notify user
		$mod->notify($_SESSION['user']->id, $to_user, 'msg', mysql_insert_id());
	}
	
	#
	#
	# my board
	#
	#
	
	public function myboard() {
		
		$mod = new Db();
		if (isset($_GET['detail']) && ($_GET['detail'] != ''))
		{
			$view = $mod->get_by_id('social_status', $_GET['detail']);
			if ($view) {
				if ((Utils::isfriend($view->from_user) == 1) || (Utils::isfriend($view->to_user) == 1) || ($_GET['userboard'] = $_SESSION['user']->id)) {
				} else {
					header('Location: '.BASE_URL.'?p=msg&msg=permission_denied');
				}
			} else {
				die;
			}
			$where = 'social_users.xon = 1 AND social_status.id = '.$_GET['detail'];
			//notify viewed
			$id_notify = $_GET['id_notify'];
			$post[] = array ('viewed' => 1);
			$mod->update('social_notify', $id_notify, $post);
		} else {
			$where = 'social_users.xon = 1 AND social_status.to_user = '.$_SESSION['user']->id;
		}
		
		$msgs = $mod->query('SELECT
							social_status.status,
							social_status.status_extra,
							social_status.id as id_status,
							social_status.updated as upd,
							social_status.img as upl_img,
							social_status.from_user,
							social_status.to_user,
							social_status.youtube,
							social_status.share,
							social_status.rating_average,
							social_status.rating_numbers,
							social_users.*
							FROM social_status
							INNER JOIN social_users ON social_users.id = social_status.from_user
							WHERE '.
							$where.'
							ORDER BY social_status.id DESC');
							//ORDER BY upd DESC');
		
		$_GET['page'] = (isset($_GET['page'])) ? $_GET['page'] : '0';
		$pager = Pagination::paginate($msgs, $_GET['page'], PP);
		
		$group_admin = $mod->get_by_id("social_users", $_SESSION['user']->group_admin);
		
		//view
		if ($pager[0]) {
			foreach ($pager[0] as $i) {
				echo '<div id="right_content"><div id="post'.$i->id_status.'">';
				if (Utils::isgroup($i->id)) {
					$i->nome = $group_admin->nome;
					$i->cognome = $group_admin->cognome;
				}

			$comments = $mod->query('SELECT social_users.nome as nome,
										social_users.cognome as cognome,
										social_users.img as img,
										social_comments.*
										FROM social_comments
										INNER JOIN social_users ON social_users.id = social_comments.id_user
										WHERE id_comment = '.$i->id_status.'
										AND social_users.xon = 1
										ORDER BY updated ASC');
				
				$the_image = (!$i->img) ? 'img_private/thumb_img_profile_null.jpg' : 'img/crop_'.$i->img;
				
				echo '<table><tr><td width="40">
						<a name="'.$i->id_status.'" href="?userboard='.$i->id.'">
						<img style="width:40px;" src="files/'.$the_image.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
						</a>
						</td><td><form name="view_posts_'.$i->id_status.'" method="post" action="'.$_SERVER['PHP_SELF'].'#'.$i->id_status.'">
						<input type="hidden" name="id_status" value="'.$i->id_status.'">
						<input type="hidden" name="img_status" value="'.$i->upl_img.'">
						<div id="status_msg">
						<span class="bold">
						<a href="?userboard='.$i->id.'">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a>
						</span>';
				
				if ($i->share > 0) {
					$share_user = $mod->get_by_id('social_users', $i->share);
						echo ' tramite <span class="bold">
							<a href="?userboard='.$i->share.'">'.stripslashes($share_user->nome).' '.stripslashes($share_user->cognome).'</a>
							</span>';	
				}
				
				echo '<br>'.stripslashes(nl2br($i->status)).'<br>'.stripslashes(nl2br($i->status_extra));
				
				echo '<ul>';
				if ($i->upl_img) {
					echo '<li><a href="files/img/'.$i->upl_img.'" class="fancybox"><img class="borded pad_all" src="files/img/thumb_'.$i->upl_img.'"></a><br><i class="fa fa-camera fleft pad watermark" aria-hidden="true"></i></li>';
				}
				
				if ($i->youtube) {
					echo '<li>'.Utils::youtube($i->youtube).'</li>';
				}
				echo '</ul>';
				
				echo '<div id="foot_status" class="clear" style="text-align:right;">Creato il '.Utils::f_date($i->upd).'</div>';
				echo '<div id="buttons_actions" class="tbox sbox bold box_up_down">';
								
				//echo Delete button ajaxLink($title, $params, $askConfirm = FALSE)
				if (($i->from_user == $_SESSION['user']->id) || (!isset($_GET['detail']))) {
					Utils::ajaxButton('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_post" id='.$i->id_status, TRUE, 'fa fa-trash-o');
				}
				
				echo '</div>';
				// rating section
				if (ENABLE_RATING == 'YES') {
					echo '<div class="clear"></div>';
					$this->ratings($i->id_status, $i->rating_numbers, $i->rating_average);
				}
				
				echo '</div>
				</form></td></tr></table>';
				
				//comments area
				$row_comments = ($comments) ? 'comments_full' : 'xsmall';
				echo '<div class="ibox"><table><tr>
				<td id="comments_table">';
				$numcomments = ($comments == NULL) ? 0 : sizeof($comments);
				if (isset($_GET['detail'])) {
					echo '<span class="fright '.$row_comments.'">Commenti ('.$numcomments.')</span><br>';
				} else {
					//echo '<a class="innerbox fright '.$row_comments.' swing" href="#" title="" item="comments'.$i->id_status.'">Commenti ('.$numcomments.')</a>';
				}
				echo '<span><table><tr><td width="50"></td>
						<td>
						<div id="comments'.$i->id_status.'">
						<table id="results">';
						echo ($numcomments > 3) ? '<a style="cursor:pointer;" class="loadMore" item="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'">Mostra più vecchi</a>' : '';
				if ($comments) {
					foreach ($comments as $c) {
						echo '<tr id="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'" cid='.$c->id.'><td style="width:32px;">
							<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" name="form_comments_'.$c->id.'">
							<a href="?userboard='.$c->id_user.'">
							<img style="width:32px;" src="files/img/crop_'.$c->img.'">
							</a>
							</td><td>
							<a href="?userboard='.$c->id_user.'"><span class="bold">'.stripslashes($c->nome).' '.stripslashes($c->cognome).'</a></span> <span>'.nl2br(stripslashes($c->comment)).'<br>'.Utils::f_date($c->updated).'</span>
							';

							if (($c->id_user == $_SESSION['user']->id) && ($i->from_user != $_SESSION['user']->id) || (!isset($_GET['detail']))) {
								echo SEP;
								//delete comment
								Utils::ajaxLink('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_comment" id='.$c->id, TRUE);
							}
						echo '</form></td></tr>';
					}
				}
				echo '</table>
						<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" id="submit_comment'.$i->id_status.'">
						<span class="xsmall">Scrivi un commento...<textarea class="textarea_comment" name="comment"></textarea></span>
						<input type="hidden" name="to_user" value="'.$i->id.'">
						<input type="hidden" name="id_comment" value="'.$i->id_status.'">
						<button class="button_comments formsubmit" idform="submit_comment'.$i->id_status.'"><i class="fa fa-comment-o" aria-hidden="true"></i> Commenta</button>
						</div>
						</form>
						</td>
						</tr></table>';
				
				
				echo '</span></td>
						</tr></table></div>';
				// ratings
				echo '</div></div>';
					}
			// pagination view
			echo '<div id="pager">'.Pagination::pager('?page=', $pager[1]).'</div>';
		}
	}
	
	public function board_all() {
		$mod = new Db();
		$msgs = $mod->query('SELECT
							social_status.status,
							social_status.status_extra,
							social_status.id as id_status,
							social_status.updated as upd,
							social_status.img as upl_img,
							social_status.youtube,
							social_status.share,
							social_status.from_user,
							social_status.to_user,
							social_status.rating_average,
							social_status.rating_numbers,
							social_users.*
							FROM social_status
							INNER JOIN social_users ON social_users.id = social_status.from_user
							WHERE social_users.xon = 1
							ORDER BY social_status.id DESC');
							//ORDER BY upd DESC');
		
		if ($msgs) {
			foreach ($msgs as $m) {
				//check if isfriend or am I or is friend of friend
				$friend = ($m->id == $_SESSION['user']->id) ? 1 : Utils::isfriend($m->id);
				//$friend_of_friend = Utils::isfriend_of_friend($m->from_user, $m->to_user);
				if ((($friend == 1) || ($friend_of_friend == 1)) && (!Utils::isgroup($m->to_user))) {
					$new_msgs[] = $m;
				}
			}
		}
		$pager = Pagination::paginate($new_msgs, $_GET['page'], PP);
		
		//view
		if ($pager[0]) {
			foreach ($pager[0] as $i) {
				echo '<div id="right_content"><div id="post'.$i->id_status.'">';
				$comments = $mod->query('SELECT social_users.nome as nome,
										social_users.cognome as cognome,
										social_users.img as img,
										social_comments.*
										FROM social_comments
										INNER JOIN social_users ON social_users.id = social_comments.id_user
										WHERE id_comment = '.$i->id_status.'
										AND social_users.xon = 1
										ORDER BY updated ASC');
				
				$the_image = (!$i->img) ? 'img_private/thumb_img_profile_null.jpg' : 'img/crop_'.$i->img;
				
				echo '<table><tr><td width="40">
						<a name="'.$i->id_status.'" href="?userboard='.$i->id.'">
						<img style="width:40px;" src="files/'.$the_image.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
						</a>
						</td><td><form name="view_posts_'.$i->id_status.'" method="post" action="'.$_SERVER['PHP_SELF'].'?type=all'.'#'.$i->id_status.'">
						<input type="hidden" name="id_status" value="'.$i->id_status.'">
						<input type="hidden" name="img_status" value="'.$i->upl_img.'">
						<div id="status_msg">
						<span class="bold">
						<a href="?userboard='.$i->id.'">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></span>';
						
						if ($i->from_user != $i->to_user) {
							$to = $mod->get_by_id('social_users', $i->to_user);
							echo '<span> >> </span><a class="bold" href="?userboard='.$i->to_user.'">'.stripslashes($to->nome).' '.stripslashes($to->cognome).'</a>';
						}
						
						if ($i->share > 0) {
						$share_user = $mod->get_by_id('social_users', $i->share);
							echo ' tramite <span class="bold">
								<a href="?userboard='.$i->share.'">'.stripslashes($share_user->nome).' '.stripslashes($share_user->cognome).'</a>
								</span>';	
					}
						
						echo '<br>'.stripslashes(nl2br($i->status)).'<br>'.stripslashes(nl2br($i->status_extra));
						
				echo '<ul>';
				if ($i->upl_img) {
					echo '<li><a href="files/img/'.$i->upl_img.'" class="fancybox"><img class="borded pad_all" src="files/img/thumb_'.$i->upl_img.'"></a><br><i class="fa fa-camera fleft pad watermark" aria-hidden="true"></i></li>';
				}
				
				if ($i->youtube) {
					echo '<li>'.Utils::youtube($i->youtube).'</li>';
				}
				echo '</ul>';
				
				echo '<div id="foot_status" class="clear" style="text-align:right;">Creato il '.Utils::f_date($i->upd).'</div>';
				echo '<div id="buttons_actions" class="tbox sbox bold box_up_down">';
				
				if ($i->id != $_SESSION['user']->id) {
					Utils::ajaxButton('Condividi', 'href="?action=share&id='.$i->id_status.'" action="share_post"', TRUE, 'fa fa-share');
				}
				
				//echo Delete button ajaxLink($title, $params, $askConfirm = FALSE)
				if ($i->id == $_SESSION['user']->id) {
					Utils::ajaxButton('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_post" id='.$i->id_status, TRUE, 'fa fa-trash-o');
				}
				
				echo '</div>';
				
				// rating section
				if (ENABLE_RATING == 'YES') {
					echo '<div class="clear"></div>';
					$this->ratings($i->id_status, $i->rating_numbers, $i->rating_average);
				}
				
				echo '</div>
				</form></td></tr></table>';
				
				//comments area
				$row_comments = ($comments) ? 'comments_full' : 'xsmall';
				$numcomments = ($comments == NULL) ? 0 : sizeof($comments);
				echo '<div class="ibox"><table><tr><td id="comments_table">';
				//echo '<a class="innerbox fright '.$row_comments.' swing" href="#" title="" item="comments'.$i->id_status.'">Commenti ('.$numcomments.')</a>';
				
				echo '<span>';
				
				echo '<table><tr><td width="50"></td>
						<td>
						<div id="comments'.$i->id_status.'">
						<table id="results">';
						echo ($numcomments > 3) ? '<a style="cursor:pointer;" class="loadMore" item="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'">Mostra più vecchi</a>' : '';
				if ($comments) {
					foreach ($comments as $c) {
						echo '<tr id="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'" cid='.$c->id.'><td style="width:32px;">
							<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" name="form_comments_'.$c->id.'">
							<a href="?userboard='.$c->id_user.'">
							<img style="width:32px;" src="files/img/crop_'.$c->img.'">
							</a>
							</td><td>
							<a href="?userboard='.$c->id_user.'"><span class="bold">'.stripslashes($c->nome).' '.stripslashes($c->cognome).'</a></span> <span>'.nl2br(stripslashes($c->comment)).'<br>'.Utils::f_date($c->updated).'</span>';
							
						if ($c->id_user == $_SESSION['user']->id) {
							echo SEP;
							//delete comment
							Utils::ajaxLink('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_comment" id='.$c->id, TRUE);
						}
						echo '</td></form></tr>';
					}
				}
				echo '</table>
						<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" id="submit_comment'.$i->id_status.'">
						<span class="xsmall">Scrivi un commento...<textarea class="textarea_comment" name="comment"></textarea></span>
						<input type="hidden" name="to_user" value="'.$i->id.'">
						<input type="hidden" name="id_comment" value="'.$i->id_status.'">
						<button class="button_comments formsubmit" idform="submit_comment'.$i->id_status.'"><i class="fa fa-comment-o" aria-hidden="true"></i> Commenta</button>
						</div>
						</form>
						</td>
						</tr></table>';
						
				echo '</span></td>
						</tr></table></div>';
				// ratings
				echo '</div></div>';	
			}
			//pagination view
			echo '<div id="pager">'.Pagination::pager('?page=', $pager[1]).'</div>';
		
		}
	}
	
	public function userboard($id) {
		$mod = new Db();
	
		if ((($_GET['detail']) && ($_GET['detail'] != '')) || ($_GET['what'] == 'group')) {	
			//notify viewed
			$id_notify = $_GET['id_notify'];
			$post[] = array ('viewed' => 1);
			$mod->update('social_notify', $id_notify, $post);
		}
		
		if (($_GET['detail']) && ($_GET['detail'] != '')) {				
			$view = $mod->get_by_id('social_status', $_GET['detail']);
			if ($view) {			
				if ((Utils::isfriend($view->from_user) == 1) || (Utils::isfriend($view->to_user) == 1)) {
				} else {
					header('Location: '.BASE_URL.'?p=msg&msg=permission_denied');
				}
			} else {
				die;
			}
			$where = 'social_users.xon = 1 AND social_status.id = '.$_GET['detail'];
		} else {
			$where = 'social_users.xon = 1 AND social_status.to_user = '.$id;
		}
		
		//check is isfriend
		$friend = Utils::isfriend($id);

		switch ($friend) {
			//request accepted: view board			
			case 1:
				if (Utils::isgroup($id)) {
					$group_admin = $mod->get_by_id("social_users", $id);
					$is_admin = ($_SESSION['user']->id == $group_admin->group_admin) ? 1 : 0;
				}
				$msgs = $mod->query('SELECT
									social_status.status,
									social_status.status_extra,
									social_status.id as id_status,
									social_status.updated as upd,
									social_status.img as upl_img,
									social_status.youtube,
									social_status.share,
									social_status.rating_average,
									social_status.rating_numbers,
									social_users.* FROM social_status
									INNER JOIN social_users ON social_users.id = social_status.from_user
									WHERE '.
									$where.'
									ORDER BY social_status.id DESC');
									//ORDER BY upd DESC');
				
				$pager = Pagination::paginate($msgs, $_GET['page'], PP);
				
				if ($pager[0]) {
					foreach ($pager[0] as $i) {
						echo '<div id="right_content"><div id="post'.$i->id_status.'">';
						$comments = $mod->query('SELECT social_users.nome as nome,
												social_users.cognome as cognome,
												social_users.img as img,
												social_comments.*
												FROM social_comments
												INNER JOIN social_users ON social_users.id = social_comments.id_user
												WHERE id_comment = '.$i->id_status.'
												AND social_users.xon = 1
												ORDER BY updated ASC');

						$the_image = (!$i->img) ? 'img_private/thumb_img_profile_null.jpg' : 'img/crop_'.$i->img;
						
						echo '<table><tr><td width="40">
								<a name='.$i->id_status.' href="?userboard='.$i->id.'">
								<img style="width:40px;" src="files/'.$the_image.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
								</a>
								</td><td>
								<form name="view_posts_'.$i->id_status.'" method="post" action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'#'.$i->id_status.'">
								<input type="hidden" name="id_status" value="'.$i->id_status.'">
								<input type="hidden" name="img_status" value="'.$i->upl_img.'">
								<div id="status_msg">
								<span class="bold txt_red"><a href="?userboard='.$i->id.'">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></span>';
								
						if ($i->share > 0) {
							$share_user = $mod->get_by_id('social_users', $i->share);
								echo ' tramite <span class="bold">
									<a href="?userboard='.$i->share.'">'.stripslashes($share_user->nome).' '.stripslashes($share_user->cognome).'</a>
									</span>';	
						}
						
						echo '<br>'.stripslashes(nl2br($i->status)).'<br>'.stripslashes(nl2br($i->status_extra));
								
						echo '<ul>';
						if ($i->upl_img) {
							echo '<li><a href="files/img/'.$i->upl_img.'" class="fancybox"><img class="borded pad_all" src="files/img/thumb_'.$i->upl_img.'"></a><br><i class="fa fa-camera fleft pad watermark" aria-hidden="true"></i></li>';
						}
						
						if ($i->youtube) {
							echo '<li>'.Utils::youtube($i->youtube).'</li>';
						}
						echo '</ul>';
						
						echo '<div id="foot_status" class="clear" style="text-align:right;">Creato il '.Utils::f_date($i->upd).'</div>';
						echo '<div id="buttons_actions" class="tbox sbox bold box_up_down">';
		
						if ($i->id != $_SESSION['user']->id) {
							Utils::ajaxButton('Condividi', 'href="?action=share&id='.$i->id_status.'" action="share_post"', TRUE, 'fa fa-share');
						}
						
						//echo Delete button ajaxLink($title, $params, $askConfirm = FALSE)
						if (($_SESSION['user']->id == $i->id) || ($is_admin == 1)) {
							Utils::ajaxButton('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_post" id='.$i->id_status, TRUE, 'fa fa-trash-o');
						}
							
						echo '</div>';
													
						// rating section
						if (ENABLE_RATING == 'YES') {
							echo '<div class="clear"></div>';
							$this->ratings($i->id_status, $i->rating_numbers, $i->rating_average);
						}
						
						echo '</div>
						</form></td></tr></table>';
					
						//comments area
						$row_comments = ($comments) ? 'comments_full' : 'xsmall';
						$numcomments = ($comments == NULL) ? 0 : sizeof($comments);
						echo '<div class="ibox"><table><tr><td id="comments_table">';
						
						//echo '<a class="innerbox fright '.$row_comments.' swing" href="#" title="" item="comments'.$i->id_status.'">Commenti ('.$numcomments.')</a>';
						
						echo '<span>';
						
						echo '<table><tr><td width="50"></td>
								<td>
								<div id="comments'.$i->id_status.'">
								<table id="results">';
								echo ($numcomments > 3) ? '<a style="cursor:pointer;" class="loadMore" item="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'">Mostra più vecchi</a>' : '';
						if ($comments) {
							foreach ($comments as $c) {
						echo '<tr id="post_comment'.$i->id_status.'" numcomments="'.$numcomments.'" cid='.$c->id.'><td style="width:32px;">
								<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" name="form_comments_'.$c->id.'">
								<a href="?userboard='.$c->id_user.'">
								<img style="width:32px;" src="files/img/crop_'.$c->img.'">
								</a>
								<td>
								<a href="?userboard='.$c->id_user.'">
								<span class="bold">'.stripslashes($c->nome).' '.stripslashes($c->cognome).'</a></span> <span>'.nl2br($c->comment).'<br>'.Utils::f_date($c->updated).'</span>
								';
								
								if (($i->id == $_SESSION['user']->id) || ($c->id_user == $_SESSION['user']->id) || ($is_admin == 1)) {
									echo SEP;
									//delete comment
									Utils::ajaxLink('Elimina', 'href="'.$_SERVER['PHP_SELF'].'" action="delete_comment" id='.$c->id, TRUE);
								}
								
								echo '</td></td></tr>
								</form>';
							}
						}
						echo '</table>
								<form action="'.$_SERVER["REQUEST_URI"].'#'.$i->id.'" method="post" id="submit_comment'.$i->id_status.'">
								<span class="xsmall">Scrivi un commento...
								<textarea class="textarea_comment" name="comment"></textarea>
								</span>
								<input type="hidden" name="anchor" value="'.$i->id.'">
								<input type="hidden" name="to_user" value="'.$i->id.'">
								<input type="hidden" name="in_board_of" value="'.$id.'">
								<input type="hidden" name="id_comment" value="'.$i->id_status.'">
								<button class="button_comments formsubmit" idform="submit_comment'.$i->id_status.'"><i class="fa fa-comment-o" aria-hidden="true"></i> Commenta</button>
								</div>
								</form>
								</td>
								</tr></table>';

						echo '</span></td>
						</tr></table></div>';
						
						// ratings
						echo '</div></div>';
					}
					//pagination view
					echo '<div id="pager">'.Pagination::pager('?page=', $pager[1]).'</div>';
				}
				break;
				
			//request not confirmed
			case 0:
				$mod = new Db();
				$user = $mod->get_by_id('social_users', $id);
				
				if (Utils::isgroup($id)) {
					echo 'L\'amministratore del gruppo non ha ancora accettato la tua richiesta.';
				} else {
					echo '<span class="bold">'.stripslashes($user->nome).'</span> non ha ancora accettato la tua amicizia.';
				}
				break;
			//not friends
			case 2:
				$this->request_friend($id);
				break;
		}
	}
	
	public function request_friend($id) {
		$mod = new Db();
		$user = $mod->get_by_id('social_users', $id);
		
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER['HTTP_REFERER'],
			'name' => 'from'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_GET['userboard'],
			'name' => 'id_friend'
			);
		if (Utils::isgroup($id)) {
			$fields[] = array(
				'label' => NULL,
				'type' => 'paragraph',
				'value' => 'Non sei ancora iscritto a questo gruppo.',
				);
		} else {
			$fields[] = array(
				'label' => NULL,
				'type' => 'paragraph',
				'value' => 'Non sei ancora amico di '.stripslashes($user->nome).'.',
				);
		}
		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->requesting_friend($_POST);
				header('Location: index.php');
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
			//prepare form
			$button = Utils::isgroup($_GET['userboard']) ? 'Iscriviti al gruppo' : 'Richiedi amicizia';
			$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array(NULL,$button), 'post', 'enctype="multipart/form-data"');
			echo $output;
	}
	
	public function requesting_friend() {
		$mod = new Db();
		if (Utils::isgroup($_POST['id_friend'])) {
			$group = $mod->get_by_id('social_users', $_POST['id_friend']);
			$post = array(
					'id_user' => $_SESSION['user']->id,
					'id_friend' => $group->group_admin,
					'id_group' => $_POST['id_friend'],
					'confirmed' => 0,
					);
			$mod->insert('social_isfriend', $post);
		}
		$post = array(
				'id_user' => $_SESSION['user']->id,
				'id_friend' => $_POST['id_friend'],
				'confirmed' => 0,
				);
		$mod->insert('social_isfriend', $post);
		header('Location: index.php');
		die;
	}
	
	
	public function insert_comment($id_comment, $comment) {
		$mod = new Db();
		$post[] = array(
					'id_user' => $_SESSION['user']->id,
					'id_comment' => $id_comment,
					'comment' => $comment,
					);
		$mod->insert('social_comments', $post);
	}
	
	public function del_msg($id, $img) {
		$mod = new Db();
		
		//delete img
		$path = ROOT.'files/img/';
		
		Utils::del_file($path, $img);
		Utils::del_file($path, 'thumb_'.$img);
		Utils::del_file($path, 'crop_'.$img);
		//delete msg
		$msgs = $mod->delete('social_status', $id);
		//delete comments
		$comments = $mod->single_exec('DELETE from social_comments WHERE id_comment = '.$id);
		//delete notifies
		$comments = $mod->single_exec('DELETE from social_notify WHERE (what="comment" OR what="comment_answer" OR what="msg") AND id_what = '.$id);
		//delete ratings
		$comments = $mod->single_exec('DELETE from social_ratings WHERE id_status = '.$id);
	}
	
	public function check_if_commented($id_comment, $post_author) {
		$mod = new Db();
		$check = $mod->query('SELECT DISTINCT id_user FROM social_comments
							 WHERE id_user != '.$_SESSION['user']->id.' AND id_user != '.$post_author.' AND id_comment = '.$id_comment);
		return $check;
	}
	
	public function del_comment($id) {
		$mod = new Db();
		$comment = $mod->delete('social_comments', $id);
	}
	
	public function ratings($id_status, $rating_numbers, $rating_average) {
		$mod = new Db();
		// rating section
		echo '<div id="rating_result'.$id_status.'">';
		$already_voted = $mod->query('SELECT * FROM social_ratings WHERE id_user = '.$_SESSION['user']->id.' AND id_status = '.$id_status);

			if ((!$already_voted) || ($already_voted[0]->id_user != $_SESSION['user']->id)) {
				// rating element
				echo '<span class="fleft" style="margin-right:5px;color:#2348a0;">Vota questo post</span>
						  <select id="rating'.$id_status.'">
						  <option value=""></option>
						  <option value="1">'.$id_status.'</option>
						  <option value="2">'.$id_status.'</option>
						  <option value="3">'.$id_status.'</option>
						  <option value="4">'.$id_status.'</option>
						  <option value="5">'.$id_status.'</option>
						   </select>
						   ';
		} else {
			echo '<span>Voto medio: '.str_replace('.',',',$rating_average).' di '.$rating_numbers.' utenti</span>';
		}
		echo '</div>';
	}
	
		
}
?>