<?php
$path  = '';

if(!defined('WP_LOAD_PATH')){
	$root = dirname(dirname(dirname(dirname(__FILE__)))).'/';

	if(file_exists($root.'wp-load.php')){
        define('WP_LOAD_PATH',$root);
	}else{
        if(file_exists($path.'wp-load.php')){
            define('WP_LOAD_PATH',$path);
        }else{
            exit("Cannot find wp-load.php");
        }
	}
}

require_once(WP_LOAD_PATH.'wp-load.php');

global $wpdb;
$links_manager_table_name = $wpdb->prefix."links_manager";

$post_id = intval($_POST["post_id"]);
$removed_links = explode("|",$_POST["removed_links"]);
$link_number = intval($_POST["link_number"]);

$removed_links_count = 0;

foreach($removed_links as $removed_link){
    if(is_numeric($removed_link) && $removed_link<$link_number){
        $removed_links_count++;
    }
}

$link_number -= $removed_links_count;

switch($_POST["action"]){
    case "remove":

    $post = get_post($post_id);
    preg_match_all("/(<a.*>)(.*)(<\/a>)/ismU",$post->post_content,$matches,PREG_SET_ORDER);

    $new_post = array();
    $new_post['ID'] = $post_id;
    $new_post['post_content'] = str_replace($matches[$link_number][0],$matches[$link_number][2],$post->post_content);

    wp_update_post($new_post);

    break;
    case "add_nofollow":

    $post = get_post($post_id);
    preg_match_all("/((<a)(.*>))(.*)(<\/a>)/ismU",$post->post_content,$matches,PREG_SET_ORDER);

    $new_post = array();
    $new_post['ID'] = $post_id;
    $new_post['post_content'] = str_replace($matches[$link_number][0],$matches[$link_number][2].' rel="nofollow" '.$matches[$link_number][3].$matches[$link_number][4].$matches[$link_number][5],$post->post_content);

    wp_update_post($new_post);

    if(preg_match("/<img\s*.*src\s*=\s*[\"|\']([a-zA-Z0-9\.\-;:\/\?&=_|\r|\n]{1,})[\"|\']/isxmU",$matches[$link_number][0],$imgs)){
        echo($matches[$link_number][2].' rel="nofollow" '.$matches[$link_number][3].substr($imgs[1],strrpos($imgs[1],"/")+1).$matches[$link_number][5]."[links-manager-separator]".$matches[$link_number][2].' rel="nofollow" '.$matches[$link_number][3].$matches[$link_number][4].$matches[$link_number][5]);
    }else{
        echo($matches[$link_number][2].' rel="nofollow" '.$matches[$link_number][3].$matches[$link_number][4].$matches[$link_number][5]."[links-manager-separator]".$matches[$link_number][2].' rel="nofollow" '.$matches[$link_number][3].$matches[$link_number][4].$matches[$link_number][5]);
    }

    break;
    case "remove_nofollow":

    $post = get_post($post_id);
    preg_match_all("/(<a.*>)(.*)(<\/a>)/ismU",$post->post_content,$matches,PREG_SET_ORDER);

    $new_post = array();
    $new_post['ID'] = $post_id;
    $new_post['post_content'] = str_replace($matches[$link_number][0],preg_replace("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i","",$matches[$link_number][0]),$post->post_content);

    wp_update_post($new_post);

    if(preg_match("/<img\s*.*src\s*=\s*[\"|\']([a-zA-Z0-9\.\-;:\/\?&=_|\r|\n]{1,})[\"|\']/isxmU",$matches[$link_number][0],$imgs)){
        echo(preg_replace("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i","",$matches[$link_number][1].substr($imgs[1],strrpos($imgs[1],"/")+1)."</a>")."[links-manager-separator]".preg_replace("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i","",$matches[$link_number][0]));
    }else{
        echo(preg_replace("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i","",$matches[$link_number][0])."[links-manager-separator]".preg_replace("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i","",$matches[$link_number][0]));
    }

    break;
    case "edit":

    $edited_link = stripslashes($_POST["edited_link"]);

    if(!preg_match("/(<a.*>)(.*)(<\/a>)/ismU",$edited_link)){
        echo("invalid_link");
    }else{
        $post = get_post($post_id);
        preg_match_all("/((<a.*>))(.*)(<\/a>)/ismU",$post->post_content,$matches,PREG_SET_ORDER);

        $new_post = array();
        $new_post['ID'] = $post_id;
        $new_post['post_content'] = str_replace($matches[$link_number][0],$edited_link,$post->post_content);

        wp_update_post($new_post);

        if(preg_match("/<img\s*.*src\s*=\s*[\"|\']([a-zA-Z0-9\.\-;:\/\?&=_|\r|\n]{1,})[\"|\']/isxmU",$edited_link,$imgs)){
            $the_link = $matches[$link_number][1].substr($imgs[1],strrpos($imgs[1],"/")+1)."</a>";
        }else{
            $the_link = $edited_link;
        }

        if(preg_match("/rel\s*=\s*[\'|\"]\s*nofollow\s*[\'|\"]/i",$edited_link)){
            echo("nofollow|".$the_link);
        }else{
            echo("follow|".$the_link);
        }
    }

    break;
    default:
    echo("error");
}
?>