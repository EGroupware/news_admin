<?php
	/**
	 * eGroupWare - eTemplates for Application news_admin
	 * http://www.egroupware.org 
	 * generated by soetemplate::dump4setup() 2007-11-08 07:43
	 *
	 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
	 * @package news_admin
	 * @subpackage setup
	 * @version $Id: class.soetemplate.inc.php 23860 2007-05-13 11:24:12Z ralfbecker $
	 */

$templ_version=1;

$templ_data[] = array('name' => 'news_admin.cat','template' => '','lang' => '','group' => '0','version' => '1.4.001','data' => 'a:1:{i:0;a:4:{s:4:"type";s:4:"grid";s:4:"data";a:10:{i:0;a:11:{s:2:"h1";s:6:",!@msg";s:2:"c2";s:2:"th";s:2:"c3";s:3:"row";s:2:"c5";s:7:"row,top";s:2:"c6";s:7:"row,top";s:2:"c7";s:3:"row";s:2:"h2";s:2:"28";s:2:"c8";s:3:"row";s:2:"h4";s:11:",!@is_admin";s:2:"c4";s:3:"row";s:2:"h7";s:19:",!@import_available";}i:1;a:4:{s:1:"A";a:5:{s:4:"type";s:5:"label";s:4:"span";s:13:"all,redItalic";s:5:"align";s:6:"center";s:4:"name";s:3:"msg";s:7:"no_lang";s:1:"1";}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:2;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:11:",,,cat_name";s:5:"label";s:4:"Name";}s:1:"B";a:6:{s:4:"type";s:4:"text";s:4:"size";s:6:"80,150";s:4:"name";s:8:"cat_name";s:6:"needed";s:1:"1";s:4:"span";s:3:"all";s:7:"no_lang";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:3;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:13:",,,cat_parent";s:5:"label";s:6:"Parent";}s:1:"B";a:4:{s:4:"type";s:10:"select-cat";s:4:"size";s:18:"None,1,,news_admin";s:4:"name";s:10:"cat_parent";s:4:"span";s:3:"all";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:4;a:4:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:5:"Owner";}s:1:"B";a:3:{s:4:"type";s:14:"select-account";s:4:"size";s:6:"Global";s:4:"name";s:9:"cat_owner";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:5;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:18:",,,cat_description";s:5:"label";s:11:"Description";}s:1:"B";a:4:{s:4:"type";s:8:"textarea";s:4:"size";s:4:"3,64";s:4:"name";s:15:"cat_description";s:4:"span";s:3:"all";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:6;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:15:",,,cat_readable";s:5:"label";s:16:"Read permissions";}s:1:"B";a:4:{s:4:"type";s:14:"select-account";s:4:"name";s:12:"cat_readable";s:4:"size";s:6:"6,both";s:6:"needed";s:1:"1";}s:1:"C";a:2:{s:4:"type";s:5:"label";s:5:"label";s:17:"Write permissions";}s:1:"D";a:3:{s:4:"type";s:14:"select-account";s:4:"size";s:6:"6,both";s:4:"name";s:12:"cat_writable";}}i:7;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:5:"label";s:10:"Import URL";s:4:"size";s:13:",,,import_url";}s:1:"B";a:5:{s:4:"type";s:4:"text";s:4:"name";s:10:"import_url";s:4:"size";s:2:"80";s:4:"span";s:3:"all";s:4:"help";s:52:"URL of the RSS or Atom feed, empty for own news feed";}s:1:"C";a:4:{s:4:"type";s:5:"label";s:4:"size";s:13:",,,news_begin";s:5:"label";s:5:"Start";s:5:"align";s:5:"right";}s:1:"D";a:2:{s:4:"type";s:4:"date";s:4:"name";s:10:"news_begin";}}i:8;a:4:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:5:"label";s:12:"Import every";s:4:"size";s:19:",,,import_frequency";}s:1:"B";a:4:{s:4:"type";s:13:"select-number";s:4:"name";s:16:"import_frequency";s:4:"size";s:5:",1,24";s:5:"label";s:8:"%s hours";}s:1:"C";a:4:{s:4:"type";s:6:"button";s:4:"span";s:3:"all";s:5:"label";s:10:"Import now";s:4:"name";s:14:"button[import]";}s:1:"D";a:1:{s:4:"type";s:5:"label";}}i:9;a:4:{s:1:"A";a:6:{s:4:"type";s:4:"hbox";s:4:"size";s:1:"3";s:4:"span";s:1:"3";i:1;a:3:{s:4:"type";s:6:"button";s:5:"label";s:4:"Save";s:4:"name";s:12:"button[save]";}i:2;a:3:{s:4:"type";s:6:"button";s:4:"name";s:13:"button[apply]";s:5:"label";s:5:"Apply";}i:3;a:4:{s:4:"type";s:6:"button";s:4:"name";s:14:"button[cancel]";s:5:"label";s:6:"Cancel";s:7:"onclick";s:15:"window.close();";}}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:5:{s:4:"type";s:6:"button";s:5:"label";s:6:"Delete";s:5:"align";s:5:"right";s:4:"name";s:14:"button[delete]";s:7:"onclick";s:39:"return confirm(\'Delete this category\');";}}}s:4:"rows";i:9;s:4:"cols";i:4;}}','size' => '','style' => '','modified' => '1185292263',);

$templ_data[] = array('name' => 'news_admin.cats','template' => '','lang' => '','group' => '0','version' => '1.4.001','data' => 'a:1:{i:0;a:6:{s:4:"type";s:4:"grid";s:4:"data";a:4:{i:0;a:1:{s:2:"h1";s:6:",!@msg";}i:1;a:1:{s:1:"A";a:4:{s:4:"type";s:5:"label";s:4:"span";s:10:",redItalic";s:5:"align";s:6:"center";s:4:"name";s:3:"msg";}}i:2;a:1:{s:1:"A";a:3:{s:4:"type";s:9:"nextmatch";s:4:"size";s:4:"rows";s:4:"name";s:2:"nm";}}i:3;a:1:{s:1:"A";a:4:{s:4:"type";s:6:"button";s:4:"name";s:7:"edit[0]";s:7:"onclick";s:165:"window.open(egw::link(\'/index.php\',\'menuaction=news_admin.news_admin_ui.cat\'),\'_blank\',\'dependent=yes,width=600,height=400,scrollbars=yes,status=yes\'); return false;";s:5:"label";s:3:"Add";}}}s:4:"rows";i:3;s:4:"cols";i:1;s:4:"size";s:4:"100%";s:7:"options";a:1:{i:0;s:4:"100%";}}}','size' => '100%','style' => '','modified' => '1185207325',);

$templ_data[] = array('name' => 'news_admin.cats.rows','template' => '','lang' => '','group' => '0','version' => '1.4.001','data' => 'a:1:{i:0;a:6:{s:4:"type";s:4:"grid";s:4:"data";a:3:{i:0;a:4:{s:2:"c1";s:2:"th";s:2:"c2";s:3:"row";s:1:"I";s:2:"1%";s:1:"G";s:2:"1%";}i:1;a:9:{s:1:"A";a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:5:"label";s:4:"Name";s:4:"name";s:8:"cat_name";}s:1:"B";a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:5:"label";s:11:"Description";s:4:"name";s:15:"cat_description";}s:1:"C";a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:5:"label";s:5:"Owner";s:4:"name";s:9:"cat_owner";}s:1:"D";a:3:{s:4:"type";s:16:"nextmatch-header";s:5:"label";s:16:"Read permissions";s:4:"name";s:8:"readable";}s:1:"E";a:3:{s:4:"type";s:16:"nextmatch-header";s:5:"label";s:17:"Write permissions";s:4:"name";s:8:"writable";}s:1:"F";a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:5:"label";s:12:"Last updated";s:4:"name";s:9:"news_date";}s:1:"G";a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:5:"label";s:5:"Count";s:4:"name";s:8:"num_news";}s:1:"H";a:3:{s:4:"type";s:16:"nextmatch-header";s:5:"label";s:10:"Import URL";s:4:"name";s:10:"import_url";}s:1:"I";a:2:{s:4:"type";s:5:"label";s:5:"label";s:7:"Actions";}}i:2;a:9:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"name";s:16:"${row}[cat_name]";s:4:"size";s:49:",news_admin.uinews.index&cat_id=$row_cont[cat_id]";}s:1:"B";a:2:{s:4:"type";s:5:"label";s:4:"name";s:23:"${row}[cat_description]";}s:1:"C";a:4:{s:4:"type";s:14:"select-account";s:4:"size";s:6:"Global";s:4:"name";s:17:"${row}[cat_owner]";s:8:"readonly";s:1:"1";}s:1:"D";a:3:{s:4:"type";s:14:"select-account";s:4:"name";s:20:"${row}[cat_readable]";s:8:"readonly";s:1:"1";}s:1:"E";a:3:{s:4:"type";s:14:"select-account";s:4:"name";s:20:"${row}[cat_writable]";s:8:"readonly";s:1:"1";}s:1:"F";a:3:{s:4:"type";s:9:"date-time";s:4:"name";s:17:"${row}[news_date]";s:8:"readonly";s:1:"1";}s:1:"G";a:3:{s:4:"type";s:5:"label";s:4:"name";s:16:"${row}[num_news]";s:5:"align";s:6:"center";}s:1:"H";a:3:{s:4:"type";s:5:"label";s:4:"name";s:19:"${row}[import_host]";s:4:"size";s:54:",$row_cont[import_url],,,_blank,,$row_cont[import_url]";}s:1:"I";a:4:{s:4:"type";s:4:"hbox";s:4:"size";s:1:"2";i:1;a:5:{s:4:"type";s:6:"button";s:4:"name";s:23:"edit[$row_cont[cat_id]]";s:7:"onclick";s:190:"window.open(egw::link(\'/index.php\',\'menuaction=news_admin.news_admin_ui.cat&cat_id=$row_cont[cat_id]\'),\'_blank\',\'dependent=yes,width=600,height=400,scrollbars=yes,status=yes\'); return false;";s:5:"label";s:4:"Edit";s:4:"size";s:4:"edit";}i:2;a:5:{s:4:"type";s:6:"button";s:4:"size";s:6:"delete";s:5:"label";s:6:"Delete";s:4:"name";s:25:"delete[$row_cont[cat_id]]";s:7:"onclick";s:62:"return confirm(\'Delete this category incl. all it\'s content\');";}}}}s:4:"rows";i:2;s:4:"cols";i:9;s:4:"size";s:4:"100%";s:7:"options";a:1:{i:0;s:4:"100%";}}}','size' => '100%','style' => '','modified' => '1185207387',);

$templ_data[] = array('name' => 'news_admin.edit','template' => '','lang' => '','group' => '0','version' => '1.3.001','data' => 'a:1:{i:0;a:4:{s:4:"type";s:4:"grid";s:4:"data";a:9:{i:0;a:8:{s:2:"h1";s:6:",!@msg";s:2:"c2";s:2:"th";s:2:"c3";s:3:"row";s:2:"c4";s:7:"row,top";s:2:"c5";s:7:"row,top";s:2:"c6";s:3:"row";s:2:"c7";s:3:"row";s:2:"h2";s:2:"28";}i:1;a:6:{s:1:"A";a:5:{s:4:"type";s:5:"label";s:4:"span";s:13:"all,redItalic";s:5:"align";s:6:"center";s:4:"name";s:3:"msg";s:7:"no_lang";s:1:"1";}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:2;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:9:",,,cat_id";s:5:"label";s:8:"Category";}s:1:"B";a:6:{s:4:"type";s:6:"select";s:4:"size";s:10:"Select one";s:4:"name";s:6:"cat_id";s:6:"needed";s:1:"1";s:4:"span";s:3:"all";s:7:"no_lang";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:3;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:16:",,,news_headline";s:5:"label";s:8:"Headline";}s:1:"B";a:5:{s:4:"type";s:4:"text";s:4:"size";s:6:"70,128";s:4:"name";s:13:"news_headline";s:4:"span";s:17:"all,news_headline";s:6:"needed";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:4;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:14:",,,news_teaser";s:5:"label";s:6:"Teaser";}s:1:"B";a:4:{s:4:"type";s:8:"textarea";s:4:"size";s:4:"3,80";s:4:"name";s:11:"news_teaser";s:4:"span";s:15:"all,news_teaser";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:5;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:15:",,,news_content";s:5:"label";s:7:"Content";}s:1:"B";a:5:{s:4:"type";s:8:"htmlarea";s:4:"name";s:12:"news_content";s:4:"span";s:3:"all";s:4:"size";s:24:"extended,280px,100%,true";s:6:"needed";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:6;a:6:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:7:"Visible";}s:1:"B";a:3:{s:4:"type";s:6:"select";s:4:"name";s:7:"visible";s:7:"no_lang";s:1:"1";}s:1:"C";a:4:{s:4:"type";s:5:"label";s:4:"size";s:13:",,,news_begin";s:5:"label";s:5:"Start";s:5:"align";s:5:"right";}s:1:"D";a:2:{s:4:"type";s:4:"date";s:4:"name";s:10:"news_begin";}s:1:"E";a:4:{s:4:"type";s:5:"label";s:4:"size";s:11:",,,news_end";s:5:"label";s:3:"End";s:5:"align";s:5:"right";}s:1:"F";a:2:{s:4:"type";s:4:"date";s:4:"name";s:8:"news_end";}}i:7;a:6:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:10:"Created by";}s:1:"B";a:4:{s:4:"type";s:14:"select-account";s:4:"name";s:16:"news_submittedby";s:8:"readonly";s:1:"1";s:4:"span";s:1:"4";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:4:{s:4:"type";s:9:"date-time";s:5:"align";s:5:"right";s:4:"name";s:9:"news_date";s:8:"readonly";s:1:"1";}}i:8;a:6:{s:1:"A";a:6:{s:4:"type";s:4:"hbox";s:4:"size";s:1:"3";s:4:"span";s:1:"5";i:1;a:3:{s:4:"type";s:6:"button";s:5:"label";s:4:"Save";s:4:"name";s:12:"button[save]";}i:2;a:3:{s:4:"type";s:6:"button";s:4:"name";s:13:"button[apply]";s:5:"label";s:5:"Apply";}i:3;a:4:{s:4:"type";s:6:"button";s:4:"name";s:14:"button[cancel]";s:5:"label";s:6:"Cancel";s:7:"onclick";s:15:"window.close();";}}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:5:{s:4:"type";s:6:"button";s:5:"label";s:6:"Delete";s:5:"align";s:5:"right";s:4:"name";s:14:"button[delete]";s:7:"onclick";s:35:"return confirm(\'Delete this news\');";}}}s:4:"rows";i:8;s:4:"cols";i:6;}}','size' => '','style' => '','modified' => '1156928134',);

$templ_data[] = array('name' => 'news_admin.edit','template' => '','lang' => '','group' => '0','version' => '1.3.002','data' => 'a:1:{i:0;a:4:{s:4:"type";s:4:"grid";s:4:"data";a:9:{i:0;a:9:{s:2:"h1";s:6:",!@msg";s:2:"c2";s:2:"th";s:2:"c3";s:3:"row";s:2:"c4";s:7:"row,top";s:2:"c5";s:7:"row,top";s:2:"c6";s:3:"row";s:2:"c7";s:3:"row";s:2:"h2";s:2:"28";s:2:"h7";s:19:",!@news_submittedby";}i:1;a:6:{s:1:"A";a:5:{s:4:"type";s:5:"label";s:4:"span";s:13:"all,redItalic";s:5:"align";s:6:"center";s:4:"name";s:3:"msg";s:7:"no_lang";s:1:"1";}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:2;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:9:",,,cat_id";s:5:"label";s:8:"Category";}s:1:"B";a:6:{s:4:"type";s:6:"select";s:4:"size";s:10:"Select one";s:4:"name";s:6:"cat_id";s:6:"needed";s:1:"1";s:4:"span";s:3:"all";s:7:"no_lang";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:3;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:16:",,,news_headline";s:5:"label";s:8:"Headline";}s:1:"B";a:5:{s:4:"type";s:4:"text";s:4:"size";s:6:"70,128";s:4:"name";s:13:"news_headline";s:4:"span";s:17:"all,news_headline";s:6:"needed";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:4;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:14:",,,news_teaser";s:5:"label";s:6:"Teaser";}s:1:"B";a:4:{s:4:"type";s:8:"textarea";s:4:"size";s:4:"3,80";s:4:"name";s:11:"news_teaser";s:4:"span";s:15:"all,news_teaser";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:5;a:6:{s:1:"A";a:3:{s:4:"type";s:5:"label";s:4:"size";s:15:",,,news_content";s:5:"label";s:7:"Content";}s:1:"B";a:5:{s:4:"type";s:8:"htmlarea";s:4:"name";s:12:"news_content";s:4:"span";s:3:"all";s:4:"size";s:58:"$cont[rtfEditorFeatures],320px,100%,true,$cont[upload_dir]";s:6:"needed";s:1:"1";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:1:{s:4:"type";s:5:"label";}}i:6;a:6:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:7:"Visible";}s:1:"B";a:2:{s:4:"type";s:6:"select";s:4:"name";s:7:"visible";}s:1:"C";a:4:{s:4:"type";s:5:"label";s:4:"size";s:13:",,,news_begin";s:5:"label";s:5:"Start";s:5:"align";s:5:"right";}s:1:"D";a:2:{s:4:"type";s:4:"date";s:4:"name";s:10:"news_begin";}s:1:"E";a:4:{s:4:"type";s:5:"label";s:4:"size";s:11:",,,news_end";s:5:"label";s:3:"End";s:5:"align";s:5:"right";}s:1:"F";a:2:{s:4:"type";s:4:"date";s:4:"name";s:8:"news_end";}}i:7;a:6:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:10:"Created by";}s:1:"B";a:4:{s:4:"type";s:14:"select-account";s:4:"name";s:16:"news_submittedby";s:8:"readonly";s:1:"1";s:4:"span";s:1:"4";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:4:{s:4:"type";s:9:"date-time";s:5:"align";s:5:"right";s:4:"name";s:9:"news_date";s:8:"readonly";s:1:"1";}}i:8;a:6:{s:1:"A";a:6:{s:4:"type";s:4:"hbox";s:4:"size";s:1:"3";s:4:"span";s:1:"5";i:1;a:3:{s:4:"type";s:6:"button";s:5:"label";s:4:"Save";s:4:"name";s:12:"button[save]";}i:2;a:3:{s:4:"type";s:6:"button";s:4:"name";s:13:"button[apply]";s:5:"label";s:5:"Apply";}i:3;a:4:{s:4:"type";s:6:"button";s:4:"name";s:14:"button[cancel]";s:5:"label";s:6:"Cancel";s:7:"onclick";s:15:"window.close();";}}s:1:"B";a:1:{s:4:"type";s:5:"label";}s:1:"C";a:1:{s:4:"type";s:5:"label";}s:1:"D";a:1:{s:4:"type";s:5:"label";}s:1:"E";a:1:{s:4:"type";s:5:"label";}s:1:"F";a:5:{s:4:"type";s:6:"button";s:5:"label";s:6:"Delete";s:5:"align";s:5:"right";s:4:"name";s:14:"button[delete]";s:7:"onclick";s:35:"return confirm(\'Delete this news\');";}}}s:4:"rows";i:8;s:4:"cols";i:6;}}','size' => '','style' => '','modified' => '1156928134',);

$templ_data[] = array('name' => 'news_admin.index','template' => '','lang' => '','group' => '0','version' => '1.3.001','data' => 'a:1:{i:0;a:6:{s:4:"type";s:4:"grid";s:4:"data";a:5:{i:0;a:2:{s:2:"h1";s:6:",!@msg";s:2:"h2";s:2:",1";}i:1;a:1:{s:1:"A";a:5:{s:4:"type";s:5:"label";s:4:"span";s:13:"all,redItalic";s:5:"align";s:6:"center";s:4:"name";s:3:"msg";s:7:"no_lang";s:1:"1";}}i:2;a:1:{s:1:"A";a:3:{s:4:"type";s:8:"template";s:5:"align";s:5:"right";s:4:"name";s:22:"news_admin.index.right";}}i:3;a:1:{s:1:"A";a:3:{s:4:"type";s:9:"nextmatch";s:4:"size";s:21:"news_admin.index.rows";s:4:"name";s:2:"nm";}}i:4;a:1:{s:1:"A";a:4:{s:4:"type";s:6:"button";s:5:"label";s:3:"Add";s:7:"onclick";s:159:"window.open(egw::link(\'/index.php\',\'menuaction=news_admin.uinews.edit\'),\'_blank\',\'dependent=yes,width=700,height=600,scrollbars=yes,status=yes\'); return false;";s:4:"name";s:3:"add";}}}s:4:"rows";i:4;s:4:"cols";i:1;s:4:"size";s:4:"100%";s:7:"options";a:1:{i:0;s:4:"100%";}}}','size' => '100%','style' => '','modified' => '1156941745',);

$templ_data[] = array('name' => 'news_admin.index.right','template' => '','lang' => '','group' => '0','version' => '1.3.001','data' => 'a:1:{i:0;a:7:{s:4:"type";s:6:"button";s:4:"data";a:2:{i:0;a:0:{}i:1;a:1:{s:1:"A";a:1:{s:4:"type";s:5:"label";}}}s:4:"rows";i:1;s:4:"cols";i:1;s:4:"name";s:3:"add";s:5:"label";s:3:"Add";s:7:"onclick";s:159:"window.open(egw::link(\'/index.php\',\'menuaction=news_admin.uinews.edit\'),\'_blank\',\'dependent=yes,width=700,height=600,scrollbars=yes,status=yes\'); return false;";}}','size' => '','style' => '','modified' => '1157003094',);

$templ_data[] = array('name' => 'news_admin.index.rows','template' => '','lang' => '','group' => '0','version' => '1.3.001','data' => 'a:1:{i:0;a:6:{s:4:"type";s:4:"grid";s:4:"data";a:3:{i:0;a:5:{s:2:"c1";s:2:"th";s:2:"c2";s:7:"row,top";s:1:"B";s:3:"20%";s:1:"D";s:2:"50";s:1:"C";s:3:"10%";}i:1;a:4:{s:1:"A";a:2:{s:4:"type";s:5:"label";s:5:"label";s:4:"News";}s:1:"B";a:4:{s:4:"type";s:4:"vbox";s:4:"size";s:6:"2,,0,0";i:1;a:3:{s:4:"type";s:23:"nextmatch-accountfilter";s:4:"size";s:8:"From all";s:4:"name";s:16:"news_submittedby";}i:2;a:3:{s:4:"type";s:20:"nextmatch-sortheader";s:4:"name";s:9:"news_date";s:5:"label";s:4:"Date";}}s:1:"C";a:3:{s:4:"type";s:22:"nextmatch-filterheader";s:5:"label";s:7:"Visible";s:4:"name";s:7:"visible";}s:1:"D";a:3:{s:4:"type";s:5:"label";s:5:"label";s:7:"Actions";s:5:"align";s:6:"center";}}i:2;a:4:{s:1:"A";a:5:{s:4:"type";s:4:"vbox";s:4:"size";s:1:"3";i:1;a:5:{s:4:"type";s:5:"label";s:4:"span";s:14:",news_headline";s:4:"name";s:21:"${row}[news_headline]";s:7:"no_lang";s:1:"1";s:4:"size";s:25:",$row_cont[link],,,_blank";}i:2;a:5:{s:4:"type";s:5:"label";s:4:"name";s:19:"${row}[news_teaser]";s:4:"span";s:12:",news_teaser";s:7:"no_lang";s:1:"1";s:4:"size";s:3:",,1";}i:3;a:3:{s:4:"type";s:4:"html";s:4:"span";s:13:",news_content";s:4:"name";s:20:"${row}[news_content]";}}s:1:"B";a:4:{s:4:"type";s:4:"vbox";s:4:"size";s:1:"2";i:1;a:3:{s:4:"type";s:14:"select-account";s:4:"name";s:24:"${row}[news_submittedby]";s:8:"readonly";s:1:"1";}i:2;a:3:{s:4:"type";s:9:"date-time";s:4:"name";s:17:"${row}[news_date]";s:8:"readonly";s:1:"1";}}s:1:"C";a:5:{s:4:"type";s:4:"vbox";s:4:"size";s:6:"3,,0,0";i:1;a:3:{s:4:"type";s:6:"select";s:4:"name";s:15:"${row}[visible]";s:8:"readonly";s:1:"1";}i:2;a:3:{s:4:"type";s:4:"date";s:4:"name";s:18:"${row}[news_begin]";s:8:"readonly";s:1:"1";}i:3;a:3:{s:4:"type";s:4:"date";s:4:"name";s:16:"${row}[news_end]";s:8:"readonly";s:1:"1";}}s:1:"D";a:5:{s:4:"type";s:4:"hbox";s:4:"size";s:1:"2";i:1;a:5:{s:4:"type";s:6:"button";s:4:"size";s:4:"edit";s:5:"label";s:4:"Edit";s:4:"name";s:24:"edit[$row_cont[news_id]]";s:7:"onclick";s:186:"window.open(egw::link(\'/index.php\',\'menuaction=news_admin.uinews.edit&news_id=$row_cont[news_id]\'),\'_blank\',\'dependent=yes,width=700,height=600,scrollbars=yes,status=yes\'); return false;";}i:2;a:5:{s:4:"type";s:6:"button";s:4:"size";s:6:"delete";s:5:"label";s:6:"Delete";s:4:"name";s:26:"delete[$row_cont[news_id]]";s:7:"onclick";s:35:"return confirm(\'Delete this news\');";}s:5:"align";s:6:"center";}}}s:4:"rows";i:2;s:4:"cols";i:4;s:4:"size";s:4:"100%";s:7:"options";a:1:{i:0;s:4:"100%";}}}','size' => '100%','style' => '','modified' => '1156941863',);

