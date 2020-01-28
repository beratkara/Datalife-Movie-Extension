<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2018 Berat Kara - www.beratkara.com - skype : beratkara@windowslive.com
=====================================================
 This code is protected by copyright
=====================================================
 File: bkbot.php
-----------------------------------------------------
 Use: Setup bkbot
=====================================================
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function VeriOku($Url,$proxy = ''){ 

    $Curl = curl_init ();
	curl_setopt($Curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($Curl, CURLOPT_URL, $Url);
    curl_setopt($Curl, CURLOPT_REFERER, 'http://www.google.com');
    curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
	
	$request_headers = array(
	  'Connection: keep-alive',
	  'Upgrade-Insecure-Requests: 1',
	  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
	  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
	  'Accept-Encoding: compressed',
	  'Accept-Language: en-US,en;q=0.9',
	);
	
	curl_setopt($Curl, CURLOPT_HTTPHEADER, $request_headers);
	
	curl_setopt($Curl, CURLOPT_ENCODING,  'gzip,deflate');
	if(strlen($proxy) > 0)
		curl_setopt($Curl, CURLOPT_PROXY, $proxy);
	curl_setopt($Curl, CURLOPT_POSTREDIR, 3);
    $VeriOku = curl_exec ($Curl);
    curl_close($Curl);
	return str_replace(array("\n","\t","\r"), null, $VeriOku);
}

function seo($text)
{
	$find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
	$replace = array('C', 'S', 'G', 'U', 'I', 'O', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
	$text = str_replace($find, $replace, $text);
	$text = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $text);
	$text = trim(preg_replace('/\s+/', ' ', $text));
	$text = str_replace(' ', '-', $text);
	return $text;
}

function keyword($text)
{
	return str_replace(' ', ',', $text);
}

function file_download($link,$dosya_adi){
	
	$filename = $dosya_adi;

	if (!file_exists($filename))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$dosya=curl_exec($ch);
		curl_close($ch);

		$fp = fopen($filename,'w');
		fwrite($fp, $dosya);
		fclose($fp);
	}
}

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1) {

	msg("error", $lang['index_denied'], $lang['index_denied']);

}

echoheader( "<i class=\"fa fa-user-circle-o position-left\"></i><span class=\"text-semibold\">BKBOT Film Botu - <a href='https://www.r10.net/members/96158-furkank.html' title='Berat Kara'>İletişim</a></span>", "Yönetim Paneli" );

if(isset($_POST['kaydet']))
{
	$pbaslik = $_POST['baslik'];
	$originaltitles = $_POST['originaltitles'];
	$pdescription = $_POST['description'];
	$pkategoriler = $_POST['kategoriler'];
	$pkalite = $_POST['kalite'];
	$pyil = $_POST['yil'];
	$poyuncular = $_POST['oyuncular'];
	$psure = $_POST['sure'];
	$pvideo = $_POST['video'];
	$posterurl = $_POST['posterid'];
	
	$xfields = "";
	
	$siteurl = "$_SERVER[HTTP_X_FORWARDED_PROTO]://$_SERVER[HTTP_HOST]/";
	
	if(count($pvideo) > 1)
		$xfields = 'original_title|'.$originaltitles.'||quality|'.$pkalite.'||year|'.$pyil;
	else
		$xfields = 'iframe|<iframe src="'.$pvideo[0].'" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>||original_title|'.$originaltitles.'||quality|'.$pkalite.'||year|'.$pyil;
	
	$xfields .= '||image|'.$siteurl.'poster/'.$posterurl.'||durata|'.$psure.'||cast|';
	
	for($i = 0; $i < count($poyuncular); $i++)
	{
		$xfields .= $poyuncular[$i];
		if($i != count($poyuncular) - 1)
			$xfields .= ",";
	}
	
	if(count($pvideo) > 1)
	{
		for($i = 0; $i < count($pvideo); $i++)//test
		{
			$test = explode ("|",$pvideo[$i]);
			if(!empty($test[1]))
			{
				if($i == 0)
					$xfields .= '||iframe|<iframe src="https://altadefinizione01.vin/player/play.php" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				$xfields .= '||'.$test[0].'|<iframe src="'.$test[1].'" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
			}
		}
	}
	
	$createauthor = urlencode($member_id['name']);
	$createdate = date("Y-m-d H:i:s");
	$createshort_story = $pdescription.'<img src="'.$siteurl.'poster/'.$posterurl.'" alt="'.$pbaslik.' Streaming" class="fr-dib" style="width:0px;height:0px;">';
	$createfull_story = "<h2 style=\"text-align:center;\"><br></h2><h2 style=\"text-align:center;\"><b><span style=\"font-size:24px;\">".$pbaslik."</span></b></h2><br>";
	$createxfields = $xfields;
	$createtitle = $pbaslik;
	$createdescr = $pbaslik." Streaming, $pbaslik Altadefinizione01, $pbaslik Streaming ita gratis in alta definizione HD 720p, 1080p.";
	$createkeywords = $pbaslik." Streaming, $pbaslik Altadefinizione01, $pbaslik streaming ita";
	$createcategory = "";
	$createalt_name = seo(strtolower($pbaslik));//seo url
	$createalt_name = str_replace("---","-",$createalt_name);
	$createtags = $pbaslik." Streaming, $pbaslik Altadefinizione01";
	$createmetatitle = $pbaslik." Streaming ITA Full HD Gratis - Altadefinizione01";

	for($i = 0; $i < count($pkategoriler); $i++)
	{
		$catname = $pkategoriler[$i];
		$altseoname = strtolower(seo($catname));
		$catdescp = $catname." Streaming ITA Full HD Gratis - Altadefinizione01";
		$result = $db->query( "SELECT id FROM " . PREFIX . "_category WHERE name like '%$catname%' order by id desc limit 0,1" );
		$row = $db->get_array($result);
		if(!isset($row))
		{
			$result = $db->query( "INSERT INTO " . PREFIX . "_category VALUES(NULL,'0','1','$catname','$altseoname','','','$catdescp','','','','0','','','','0','1','$catdescp','','','','');" );
			if($i == count($pkategoriler) - 1)
				$createcategory .= $db->insert_id();
			else
				$createcategory .= $db->insert_id().",";
		}
		else
		{
			if($i == count($pkategoriler) - 1)
				$createcategory .= $row['id'];
			else
				$createcategory .= $row['id'].",";
		}
	}

	$result = $db->query( "INSERT INTO " . PREFIX . "_post VALUES(NULL,'$createauthor','$createdate','$createshort_story','$createfull_story','$createxfields','$createtitle','$createdescr','$createkeywords','$createcategory','$createalt_name','0','0','1','1','0','0','','$createtags','$createmetatitle');" );
	if($result)
		echo '<div class="alert alert-success" role="alert">Film Eklendi !</div>';
	else
		echo '<div class="alert alert-danger" role="alert">Film Eklenemedi !</div>';
}

echo '

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item '.(isset($_POST['type']) && $_POST['type'] == 'single' ? 'active' : ( !isset($_POST['type']) ? 'active' : '')).'">
    <a class="nav-link '.(isset($_POST['type']) && $_POST['type'] == 'single' ? 'active' : ( !isset($_POST['type']) ? 'active' : '')).'" id="film-tab" data-toggle="tab" href="#film" role="tab" aria-controls="film" aria-selected="true" aria-expanded="true">Film Çek</a>
  </li>
  <li class="nav-item '.(isset($_POST['type']) && $_POST['type'] == 'cats' ? 'active' : '').'">
    <a class="nav-link '.(isset($_POST['type']) && ($_POST['type'] == 'cats') ? 'active' : '').'" id="cats-tab" data-toggle="tab" href="#cats" role="tab" aria-controls="cats" aria-selected="false">Kategori Çek</a>
  </li>
  <li class="nav-item '.(isset($_POST['type']) && $_POST['type'] == 'page' ? 'active' : '').'">
    <a class="nav-link '.(isset($_POST['type']) && ($_POST['type'] == 'page') ? 'active' : '').'" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="false">Tüm Filmleri Çek</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade'.(isset($_POST['type']) && ($_POST['type'] == 'single') ? ' active in' : ( !isset($_POST['type']) ? ' active in' : '')).'" id="film" role="tabpanel" aria-labelledby="film-tab">
		<form method="post" action="" class="form-horizontal" autocomplete="off">
			<div class="panel panel-default">
			  <div class="panel-heading">
				Film Bilgisi Getir
			  </div>
			  <div class="panel-body">

					<div class="form-group">
					  <label class="control-label col-md-2 col-sm-3">Url</label>
					  <div class="col-md-10 col-sm-9">
						<input class="form-control width-600" value="'.(isset($pageurl) ? $pageurl : 'https://www.altadefinizione01.cc/10208-shaun-vita-da-pecora-farmageddon-stream-gratis.html').'" maxlength="1000" type="text" name="pageurl">
					  </div>
					 </div>
					<input type="hidden" name="type" id="type" value="single">
					

			   </div>
				<div class="panel-footer">
					<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>Bilgileri Getir</button>
				</div>
			</div>
		</form>
  </div>
  <div class="tab-pane fade'.(isset($_POST['type']) && $_POST['type'] == 'cats' ? ' active in' : '').'" id="cats" role="tabpanel" aria-labelledby="cats-tab">
		<form method="post" action="" class="form-horizontal" autocomplete="off">
			<div class="panel panel-default">
			  <div class="panel-heading">
				Kategori Bilgisi Getir
			  </div>
			  <div class="panel-body">

					<div class="form-group">
					  <label class="control-label col-md-2 col-sm-3">Url</label>
					  <div class="col-md-10 col-sm-9">
						<input class="form-control width-600" value="'.(isset($categoryurl) ? $categoryurl : 'https://www.altadefinizione01.cc/azione/').'" maxlength="1000" type="text" name="categoryurl">
					  </div>
					 </div>
					<input type="hidden" name="type" id="type" value="cats">

			   </div>
				<div class="panel-footer">
					<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>Bilgileri Getir</button>
				</div>
			</div>
		</form>
  </div>
  <div class="tab-pane fade'.(isset($_POST['type']) && $_POST['type'] == 'page' ? ' active in' : '').'" id="all" role="tabpanel" aria-labelledby="all-tab">
		<form method="post" action="" class="form-horizontal" autocomplete="off">
			<div class="panel panel-default">
			  <div class="panel-heading">
				Sayfa Bilgisi Getir
			  </div>
			  <div class="panel-body">

					<div class="form-group">
					  <label class="control-label col-md-2 col-sm-3">Url</label>
					  <div class="col-md-10 col-sm-9">
						<input class="form-control width-600" value="'.(isset($categoryurl) ? $categoryurl : 'https://www.altadefinizione01.cc/').'" maxlength="1000" type="text" name="categoryurl">
					  </div>
					 </div>
					<input type="hidden" name="type" id="type" value="page">

			   </div>
				<div class="panel-footer">
					<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>Bilgileri Getir</button>
				</div>
			</div>
		</form>
  </div>
</div>


';

if(isset($_POST['categoryurl']))
{
	$gourl = $_POST['categoryurl'];
	
	$Kaynak = VeriOku($gourl);
	
	$pages = '<a href="'.$gourl.'page/[0-9]*?/">([0-9]*?)</a>';
	
	preg_match_all('@'.$pages.'@',$Kaynak,$sayfalama);
	
	$totalsayfa = $sayfalama[1][count($sayfalama[1])-1];
	
	echo '
	<div class="panel panel-default">
		<div class="panel-heading">
		</div>
		<form method="post" action="" class="form-horizontal" autocomplete="off">
		
		<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-sm-2">Sayfa İşlemi:</label>
					<div class="col-sm-10">
						<select name="sayfa">
						';
						for($i = 1; $i <= $totalsayfa; $i++)
						{
							echo '<option value="'.$gourl.'/page/'.$i.'/" '.($i == $_POST['sayfa'] ? 'selected="selected"' : '').'>'.$i.'</option>';
						}
						echo '
						</select>
						<button type="submit" id="sayfakaydet" name="sayfakaydet" class="btn bg-teal btn-sm btn-raised position-right"><i class="fa fa-floppy-o position-left"></i>Sayfadaki Filmleri Kaydet</button>
					</div>
				</div>
				'.(isset($_POST['type']) ? '<input type="hidden" name="type" id="type" value="'.$_POST['type'].'">' : '').'
			</form>
		</div>
	</div>
	';
}

if(isset($_POST['sayfakaydet']))
{
	$gourl = $_POST['sayfa'];
	
	$Kaynaks = VeriOku($gourl);
	
	$filmregex = '<div class="cover boxcaption">.*?<h2>.*?<a href="(.*?)">(.*?)</a>.*?</h2>.*?</div>.*?</div>';
	preg_match_all('@'.$filmregex.'@',$Kaynaks,$filmler);
	
	$counter = 0;
	
	$filmler[1] = array_reverse($filmler[1]);
	
	$m = 0;
	
	$pagetype = strpos($Kaynaks, "son_eklenen_head_tv");
	if ($pagetype !== false) 
		$m = 5;
	
	for($m; $m < count($filmler[1]); $m++)
	{
		flush();
		ob_get_contents();

		$counter++;
		if($counter > 50)
			break;
		
		$Kaynak = VeriOku($filmler[1][$m]);
		
		$fulltitle = '<meta property="og:title" content="(.*?)" />';
	
		$titloregex = '<span class="titulo_o">(.*?)</span>';
		$qualitaregex = '<p class="meta_dd"> <b class="icon-playback-play" title="Qualita"></b> (.*?) </p>';
		$subsregex = '<li><div class="mov-label">Subs:</div> <div class="mov-desc"><a href="(.*?)">(.*?)</a></div></li>';
		$annoregex = '<p class="meta_dd"> <b class="icon-clock" title="Anno"></b> (.*?) </p>';
		$direttoreregex = '<li><div class="mov-label">Direttore:</div> <div class="mov-desc">(.*?)</div></li>';
		$lanciareregex = '<p class="meta_dd limpiar"> <b class="icon-male" title="Attori"></b> (.*?) </p>';
		$durataregex = '<p class="meta_dd"> <b class="icon-time" title="Durata"></b> (.*?) </p>';
		$tiporegex = '<p class="meta_dd limpiar"> <b class="icon-medal" title="Genere"></b> (.*?) </p>';
		$videoregex = '<a href="#" data-link="(.*?)">';
		$descriptionregex = '<div class="entry-content">.*?<h3>Trama</h3>(.*?)<a.*?</div>';
		$posterregex = '<img width=".*?" height=".*?" src="/uploads/(.*?)" class=".*?wp-post-image" alt=".*?" title=".*?" itemprop="image" />';
		
		preg_match_all('@'.$fulltitle.'@',$Kaynak,$fulltitlesonuc);
		preg_match_all('@'.$titloregex.'@',$Kaynak,$titlosonuc);
		preg_match_all('@'.$qualitaregex.'@',$Kaynak,$qualitasonuc);
		preg_match_all('@'.$subsregex.'@',$Kaynak,$subssonuc);
		preg_match_all('@'.$annoregex.'@',$Kaynak,$annosonuc);
		preg_match_all('@'.$lanciareregex.'@',$Kaynak,$lanciaresonuc);
		preg_match_all('@'.$durataregex.'@',$Kaynak,$duratasonuc);
		preg_match_all('@'.$tiporegex.'@',$Kaynak,$tiposonuc);
		preg_match_all('@'.$videoregex.'@',$Kaynak,$videosonuc);
		preg_match_all('@'.$descriptionregex.'@',$Kaynak,$descriptionsonuc);
		preg_match_all('@'.$posterregex.'@',$Kaynak,$postersonuc);

		$titloname = $titlosonuc[1][0];
		$titloname = str_replace("'", "\'", $titloname);
		
		$flltitloname = $fulltitlesonuc[1][0];
		$flltitloname = str_replace("'", "\'", $flltitloname);
		
		$qualitaurl = "";//$qualitasonuc[1][0];
		$qualitaname = $qualitasonuc[1][0];
		$qualitaname = str_replace("'", "\'", $qualitaname);
		
		$annourl = "";//$annosonuc[1][0];
		$annoname = $annosonuc[1][0];
		$annoname = str_replace("'", "\'", $annoname);
		
		preg_match_all('@<a href=".*?">(.*?)</a>@',$lanciaresonuc[1][0],$lanciarename);
		for($i = 0; $i < count($lanciarename[1]); $i++)
			$lanciarename[1][$i] = str_replace("'", "\'", $lanciarename[1][$i]);
		
		$durataname = $duratasonuc[1][0];
		$durataname = str_replace("'", "\'", $durataname);
		
		$videobilgi = array();
		
		for($i = 0; $i < count($videosonuc[1]); $i++)
		{
			$pos = strpos($videosonuc[1][$i], "supervideo");
			if ($pos !== false) {
				$video = "supervideo|".$videosonuc[1][$i];
				array_push($videobilgi,$video);
			}else{
				$pos = strpos($videosonuc[1][$i], "verystream");
				if ($pos !== false) {
					$video = "verystream|".$videosonuc[1][$i];
					array_push($videobilgi,$video);
				}else{
					$pos = strpos($videosonuc[1][$i], "oload");
					if ($pos !== false) {
						$video = "openload|".$videosonuc[1][$i];
						array_push($videobilgi,$video);
					}
				}
			}
		}
		
		$description = $descriptionsonuc[1][0];
		$description = str_replace("'", "\'", $description);
		
		$posterurl = "https://www.altadefinizione01.cc/uploads/".$postersonuc[1][0];
		
		$imagename = md5(seo($fulltitlesonuc[1][0])).".png";
		$uploadfile = $_SERVER['DOCUMENT_ROOT']."/poster/".$imagename;
		file_download($posterurl,$uploadfile);
		
		$tiponame = $tiposonuc[1][0];
		$allcategory = "";
		$kategoriler = explode(" / ",$tiponame);
		for($i = 0; $i < count($kategoriler); $i++)
			$allcategory .= $kategoriler[$i];
		
		preg_match_all('@<a href="(.*?)">(.*?)</a>@',$allcategory,$cats);
		
		$siteurl = "$_SERVER[HTTP_X_FORWARDED_PROTO]://$_SERVER[HTTP_HOST]/";
		
		
	
		$pbaslik = $flltitloname;
		$originaltitles = $titloname;
		$pdescription = $description;
		$pkategoriler = $cats[2];
		$pkalite = $qualitaname;
		$pyil = $annoname;
		$poyuncular = $lanciarename[1];
		$psure = $durataname;
		$pvideo = $videobilgi;
		$posterurl = $imagename;
		
		$xfields = "";
		
		$siteurl = "$_SERVER[HTTP_X_FORWARDED_PROTO]://$_SERVER[HTTP_HOST]/";
		
		if(count($pvideo) > 1)
			$xfields = 'original_title|'.$originaltitles.'||quality|'.$pkalite.'||year|'.$pyil;
		else
			$xfields = 'iframe|<iframe src="'.$pvideo[0].'" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>||original_title|'.$originaltitles.'||quality|'.$pkalite.'||year|'.$pyil;
		
		$xfields .= '||image|'.$siteurl.'poster/'.$posterurl.'||durata|'.$psure.'||cast|';
		
		for($i = 0; $i < count($poyuncular); $i++)
		{
			$xfields .= $poyuncular[$i];
			if($i != count($poyuncular) - 1)
				$xfields .= ",";
		}
		
		if(count($pvideo) > 1)
		{
			for($i = 0; $i < count($pvideo); $i++)//test
			{
				$test = explode ("|",$pvideo[$i]);
				if(!empty($test[1]))
				{
					if($i == 0)
						$xfields .= '||iframe|<iframe src="https://altadefinizione01.vin/player/play.php" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
					$xfields .= '||'.$test[0].'|<iframe src="'.$test[1].'" width="660" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				}
			}
		}
		
		$createauthor = urlencode($member_id['name']);
		$createdate = date("Y-m-d H:i:s");
		$createshort_story = $pdescription.'<img src="'.$siteurl.'poster/'.$posterurl.'" alt="'.$pbaslik.' Streaming" class="fr-dib" style="width:0px;height:0px;">';
		$createfull_story = "<h2 style=\"text-align:center;\"><br></h2><h2 style=\"text-align:center;\"><b><span style=\"font-size:24px;\">".$pbaslik."</span></b></h2><br>";
		$createxfields = $xfields;
		$createtitle = $pbaslik;
		$createdescr = $pbaslik." Streaming, $pbaslik Altadefinizione01, $pbaslik Streaming ita gratis in alta definizione HD 720p, 1080p.";
		$createkeywords = $pbaslik." Streaming, $pbaslik Altadefinizione01, $pbaslik streaming ita";
		$createcategory = "";
		$createalt_name = seo(strtolower($pbaslik));//seo url
		$createalt_name = str_replace("---","-",$createalt_name);
		$createtags = $pbaslik." Streaming, $pbaslik Altadefinizione01";
		$createmetatitle = $pbaslik." Streaming ITA Full HD Gratis - Altadefinizione01";

		for($i = 0; $i < count($pkategoriler); $i++)
		{
			$catname = $pkategoriler[$i];
			$altseoname = strtolower(seo($catname));
			$catdescp = $catname." Streaming ITA Full HD Gratis - Altadefinizione01";
			$result = $db->query( "SELECT id FROM " . PREFIX . "_category WHERE name like '%$catname%' order by id desc limit 0,1" );
			$row = $db->get_array($result);
			if(!isset($row))
			{
				$result = $db->query( "INSERT INTO " . PREFIX . "_category VALUES(NULL,'0','1','$catname','$altseoname','','','$catdescp','','','','0','','','','0','1','$catdescp','','','','');" );
				if($i == count($pkategoriler) - 1)
					$createcategory .= $db->insert_id();
				else
					$createcategory .= $db->insert_id().",";
			}
			else
			{
				if($i == count($pkategoriler) - 1)
					$createcategory .= $row['id'];
				else
					$createcategory .= $row['id'].",";
			}
		}

		$result = $db->query( "INSERT INTO " . PREFIX . "_post VALUES(NULL,'$createauthor','$createdate','$createshort_story','$createfull_story','$createxfields','$createtitle','$createdescr','$createkeywords','$createcategory','$createalt_name','0','0','1','1','0','0','','$createtags','$createmetatitle');" );
		if($result)
			echo '<div class="alert alert-success" role="alert">Film Eklendi !</div>';
		else
			echo '<div class="alert alert-danger" role="alert">Film Eklenemedi !</div>';
		
		sleep(1);
	}
}


if(isset($_POST['pageurl']))
{
	$gourl = $_POST['pageurl'];
	
	$Kaynak = VeriOku($gourl);
	
	$fulltitle = '<meta property="og:title" content="(.*?)" />';
	
	$titloregex = '<span class="titulo_o">(.*?)</span>';
	$qualitaregex = '<p class="meta_dd"> <b class="icon-playback-play" title="Qualita"></b> (.*?) </p>';
	$subsregex = '<li><div class="mov-label">Subs:</div> <div class="mov-desc"><a href="(.*?)">(.*?)</a></div></li>';
	$annoregex = '<p class="meta_dd"> <b class="icon-clock" title="Anno"></b> (.*?) </p>';
	$direttoreregex = '<li><div class="mov-label">Direttore:</div> <div class="mov-desc">(.*?)</div></li>';
	$lanciareregex = '<p class="meta_dd limpiar"> <b class="icon-male" title="Attori"></b> (.*?) </p>';
	$durataregex = '<p class="meta_dd"> <b class="icon-time" title="Durata"></b> (.*?) </p>';
	$tiporegex = '<p class="meta_dd limpiar"> <b class="icon-medal" title="Genere"></b> (.*?) </p>';
	$videoregex = '<a href="#" data-link="(.*?)">';
	$descriptionregex = '<div class="entry-content">.*?<h3>Trama</h3>(.*?)<a.*?</div>';
	$posterregex = '<img width=".*?" height=".*?" src="/uploads/(.*?)" class=".*?wp-post-image" alt=".*?" title=".*?" itemprop="image" />';
	
	preg_match_all('@'.$fulltitle.'@',$Kaynak,$fulltitlesonuc);
	preg_match_all('@'.$titloregex.'@',$Kaynak,$titlosonuc);
	preg_match_all('@'.$qualitaregex.'@',$Kaynak,$qualitasonuc);
	preg_match_all('@'.$subsregex.'@',$Kaynak,$subssonuc);
	preg_match_all('@'.$annoregex.'@',$Kaynak,$annosonuc);
	preg_match_all('@'.$lanciareregex.'@',$Kaynak,$lanciaresonuc);
	preg_match_all('@'.$durataregex.'@',$Kaynak,$duratasonuc);
	preg_match_all('@'.$tiporegex.'@',$Kaynak,$tiposonuc);
	preg_match_all('@'.$videoregex.'@',$Kaynak,$videosonuc);
	preg_match_all('@'.$descriptionregex.'@',$Kaynak,$descriptionsonuc);
	preg_match_all('@'.$posterregex.'@',$Kaynak,$postersonuc);

	$titloname = $titlosonuc[1][0];
	$titloname = str_replace("'", "\'", $titloname);
	
	$flltitloname = $fulltitlesonuc[1][0];
	$flltitloname = str_replace("'", "\'", $flltitloname);
	
	$qualitaurl = "";//$qualitasonuc[1][0];
	$qualitaname = $qualitasonuc[1][0];
	$qualitaname = str_replace("'", "\'", $qualitaname);
	
	$annourl = "";//$annosonuc[1][0];
	$annoname = $annosonuc[1][0];
	$annoname = str_replace("'", "\'", $annoname);
	
	preg_match_all('@<a href=".*?">(.*?)</a>@',$lanciaresonuc[1][0],$lanciarename);
	
	$durataname = $duratasonuc[1][0];
	$durataname = str_replace("'", "\'", $durataname);
	
	$videobilgi = array();
	
	for($i = 0; $i < count($videosonuc[1]); $i++)
	{
		$pos = strpos($videosonuc[1][$i], "supervideo");
		if ($pos !== false) {
			$video = array("type"=>"supervideo","url"=>$videosonuc[1][$i]);
			array_push($videobilgi,$video);
		}else{
			$pos = strpos($videosonuc[1][$i], "verystream");
			if ($pos !== false) {
				$video = array("type"=>"verystream","url"=>$videosonuc[1][$i]);
				array_push($videobilgi,$video);
			}else{
				$pos = strpos($videosonuc[1][$i], "oload");
				if ($pos !== false) {
					$video = array("type"=>"openload","url"=>$videosonuc[1][$i]);
					array_push($videobilgi,$video);
				}
			}
		}
	}
	
	$description = $descriptionsonuc[1][0];
	$description = str_replace("'", "\'", $description);
	
	$posterurl = "https://www.altadefinizione01.cc/uploads/".$postersonuc[1][0];
	
	$imagename = md5(seo($fulltitlesonuc[1][0])).".png";
	$uploadfile = $_SERVER['DOCUMENT_ROOT']."/poster/".$imagename;
	file_download($posterurl,$uploadfile);
	
	$tiponame = $tiposonuc[1][0];
	$allcategory = "";
	$kategoriler = explode(" / ",$tiponame);
	for($i = 0; $i < count($kategoriler); $i++)
		$allcategory .= $kategoriler[$i];
	
	preg_match_all('@<a href="(.*?)">(.*?)</a>@',$allcategory,$cats);
	
	$siteurl = "$_SERVER[HTTP_X_FORWARDED_PROTO]://$_SERVER[HTTP_HOST]/";
	
	echo '
	<div class="panel panel-default">
		<div class="panel-heading">
		</div>
		<form method="post" action="" class="form-horizontal" autocomplete="off">
		
		<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-sm-2">Film İşlemi:</label>
					<div class="col-sm-10">
						<button type="submit" id="kaydet" name="kaydet" class="btn bg-teal btn-sm btn-raised position-right"><i class="fa fa-floppy-o position-left"></i>Filmi Kaydet</button>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Başlık:</label>
					<div class="col-sm-10">
						<input type="hidden" id="posterid" name="posterid" value="'.$imagename.'">
						<b><input type="text" class="form-control" id="baslik" name="baslik" value="'.$flltitloname.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Orjinal Adı:</label>
					<div class="col-sm-10">
						<b><input type="text" class="form-control" id="originaltitles" name="originaltitles" value="'.$titloname.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Açıklama:</label>
					<div class="col-sm-10">
						<b><input type="text" class="form-control" id="description" name="description" value="'.$description.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Kategoriler:</label>
					<div class="col-sm-10">
						';
						
						for($i = 0; $i < count($cats[2]); $i++)
						{
							echo '<input type="hidden" name="kategoriler[]" value="'.$cats[2][$i].'">';
							echo '<b><a href="'.$cats[1][$i].'" class="form-control" target="_blank" style="color:#603add;">'.$cats[2][$i].'</a></b>';
							if($i != count($cats[2]) - 1)
								echo ',';
						}
							

					echo'</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Kalite:</label>
					<div class="col-sm-10">
						<b><input type="text" class="form-control" id="kalite" name="kalite" value="'.$qualitaname.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Yapım Yılı:</label>
					<div class="col-sm-10">
						<b><input type="text" class="form-control" id="yil" name="yil" value="'.$annoname.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Oyuncular:</label>
					<div class="col-sm-10">
						';
						
						for($i = 0; $i < count($lanciarename[1]); $i++)
						{
							echo '<input type="hidden" name="oyuncular[]" value="'.$lanciarename[1][$i].'">';
							echo '<b>'.$lanciarename[1][$i].'</b>';
							if($i != count($lanciarename[1]) - 1)
								echo ' / ';
						}
					echo'
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Süre:</label>
					<div class="col-sm-10">
						<b><input type="text" class="form-control" id="sure" name="sure" value="'.$durataname.'" /></b>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-sm-2">Video:</label>
					<div class="col-sm-8">
					';
						for($i = 0; $i < count($videobilgi); $i++)
							echo '<input type="text" name="video[]" id="video[]" class="form-control" value="'.$videobilgi[$i]["type"].'|'.$videobilgi[$i]["url"].'">';
					
					echo '
					</div>
				</div>
			</form>
		</div>
	</div>
	';
}

echofooter();
die();

?>