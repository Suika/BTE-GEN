<?php

header('Content-Type: text/html; charset=utf-8');

include_once "lib.uuid.php";
require_once 'conf/DbConnector.php';

$post_series = $_POST['series'];
$post_books = $_POST['books'];
$post_img = $_POST['img'];
$post_size = $_POST['size'];

/*
  $post_series = 7;
  $post_books = "Boku_wa_Tomodachi_ga_Sukunai:Volume_07";
  $post_img = "wi";
  $post_size = "original";
 */

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' . mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

$sql = "SELECT  Book_ID, 
                Author.Auth_Name,  
                Series.Ser_Name, 
                Book.Book_Name, 
                Book.Book_Vol, 
                Meta.Title, 
                Meta.Translator, 
                Meta.Editor, 
                Meta.Creator, 
                Meta.Description, 
                Meta.ISBN, 
                Meta.UUID,
                Meta.UUIDI 
            FROM Author, Book, Meta, Series 
            WHERE Book.Series_Ser_ID = " . $post_series . " 
            AND Book.Meta_Meta_ID = Meta.Meta_ID 
            AND Book.Author_Auth_ID = Author.Auth_ID 
            AND Book.Series_Ser_ID = Series.Ser_ID 
            AND Meta.Title = \"" . $post_books . "\"";

$result = mysqli_query($db, $sql);
$zeile = mysqli_fetch_array($result);
mysqli_close($db);

$BID = $zeile['Book_ID'];
$illustrator = "";
$author = $zeile['Auth_Name'];
$editor = 'Simon';
$translator = $zeile['Translator'];
$title = $zeile['Ser_Name'];
$title .= (!empty($zeile['Book_Vol'])) ? " - Volume " . $zeile['Book_Vol'] : null;
$title .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;
$isbn = $zeile['ISBN'];
$desc = $zeile['Description'];
$date = date("Y-m-d");
$uuid = $zeile['UUID'];
$uuidi = $zeile['UUIDI'];

/*
  if ($post_img == "wi") {
  if ($post_size == "original"){
  if (file_exists("epubi/" . $file . ".epub")){
  sendFile("epubi/" . $file . ".epub", $title);
  }
  } else {
  if (file_exists("epubi/resized/" . $post_size ."/" . $file . ".epub")){
  sendFile("epubi/resized/" . $post_size ."/" . $file . ".epub", $title);
  }
  }
  } elseif ($post_img == "ni") {
  if (file_exists("epub/" . $file . ".epub")){
  sendFile("epub/" . $file . ".epub", $title);
  }
  }
 */

$device = array('sgs2' => array('h' => 800, 'w' => 480),
    'iphone4s' => array('h' => 960, 'w' => 640),
    'ipad2' => array('h' => 2048, 'w' => 1536),
    'ipad' => array('h' => 1024, 'w' => 768),
    'acer' => array('h' => 1280, 'w' => 800),
    'kindlefire' => array('h' => 1024, 'w' => 600),
    'iphone3' => array('h' => 480, 'w' => 320),
    'iphone5' => array('h' => 1136, 'w' => 640),
    't9800' => array('h' => 480, 'w' => 360),
    't9810' => array('h' => 640, 'w' => 480),
    'htc' => array('h' => 960, 'w' => 540),
    'lg' => array('h' => 800, 'w' => 400),
    'sb' => array('h' => 320, 'w' => 240));

$body_start = ''
        . '<?xml version="1.0" encoding="utf-8" standalone="no"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n"
        . '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n"
        . '<head>' . "\n"
        . '<title></title>' . "\n"
        . '<link href="../Styles/stylesheet.css" rel="stylesheet" type="text/css" />' . "\n"
        . '<link href="../Styles/page_styles.css" rel="stylesheet" type="text/css" />' . "\n"
        . '</head>' . "\n"
        . '<body>' . "\n";

$body_end = '</body>' . "\n" . '</html>';

$toc_start = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?><!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd"><ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">' . "\n"
        . '<head>' . "\n"
        . '<meta content="urn:uuid:' . $uuid . '" name="dtb:uid"/>' . "\n"
        . '<meta content="1" name="dtb:depth"/>' . "\n"
        . '<meta content="0" name="dtb:totalPageCount"/>' . "\n"
        . '<meta content="0" name="dtb:maxPageNumber"/>' . "\n"
        . '</head>' . "\n"
        . '<docTitle>' . "\n"
        . '<text>Unknown</text>' . "\n"
        . '</docTitle>' . "\n";

$toci_start = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?><!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd"><ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">' . "\n"
        . '<head>' . "\n"
        . '<meta content="urn:uuid:' . $uuidi . '" name="dtb:uid"/>' . "\n"
        . '<meta content="1" name="dtb:depth"/>' . "\n"
        . '<meta content="0" name="dtb:totalPageCount"/>' . "\n"
        . '<meta content="0" name="dtb:maxPageNumber"/>' . "\n"
        . '</head>' . "\n"
        . '<docTitle>' . "\n"
        . '<text>Unknown</text>' . "\n"
        . '</docTitle>' . "\n";

$toc_end = "\n" . '</ncx>';

$content_start = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . "\n"
        . '<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookId" version="2.0">' . "\n"
        . '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">' . "\n";

if (!empty($uuid))
    $content_start .= '<dc:identifier id="BookId" opf:scheme="UUID">urn:uuid:' . $uuidi . '</dc:identifier>' . "\n";

if (!empty($title))
    $content_start .= '<dc:title>' . $title . '</dc:title>' . "\n";

if (!empty($illustrator))
    $content_start .= '<dc:contributor opf:role="ill">' . $illustrator . '</dc:contributor>' . "\n";

if (!empty($isbn))
    $content_start .= '<dc:identifier opf:scheme="ISBN">' . $isbn . '</dc:identifier>' . "\n";

if (!empty($author))
    $content_start .= '<dc:creator opf:role="aut">' . $author . '</dc:creator>' . "\n";

if (!empty($desc))
    $content_start .= '<dc:description>' . $desc . '</dc:description>' . "\n";

if (!empty($date))
    $content_start .= '<dc:date opf:event="creation">' . $date . '</dc:date>' . "\n";

$content_start .= '<dc:contributor opf:role="edt">Simon</dc:contributor>' . "\n";

if (!empty($translator))
    $content_start .= '<dc:contributor opf:file-as="' . $translator . '" opf:role="trl">' . $translator . '</dc:contributor>' . "\n";

if (!empty($date))
    $content_start .= '<dc:date opf:event="publication">' . $date . '</dc:date>' . "\n";

$content_start .= '<dc:publisher>Baka-Tsuki</dc:publisher>' . "\n"
        . '<dc:language>en</dc:language>' . "\n"
        . '<dc:rights>All materials\' copyrights reserved by their respective authors and the associated publishers. Please respect their rights. Works will be deleted upon request by copyright holders.</dc:rights>' . "\n"
        . '<meta content="Cover.jpg" name="cover" />' . "\n"
        . '</metadata>' . "\n"
        . '<manifest>' . "\n"
        . '<item href="toc.ncx" id="ncx" media-type="application/x-dtbncx+xml" />' . "\n"
        . '<item href="Text/Cover.xhtml" id="Cover.xhtml" media-type="application/xhtml+xml" />' . "\n"
        . '<item href="Styles/stylesheet.css" id="stylesheet.css" media-type="text/css" />' . "\n"
        . '<item href="Styles/page_styles.css" id="page_styles.css" media-type="text/css" />' . "\n"
        . '<item href="Text/Body.xhtml" id="Body.xhtml" media-type="application/xhtml+xml" />' . "\n";

if ($post_img == "wi")
    $content_start .= '<item href="Text/Illustrations.xhtml" id="Illustrations.xhtml" media-type="application/xhtml+xml" />' . "\n";

$content_start .= '<item href="Images/Cover.jpg" id="Cover.jpg" media-type="image/jpeg" />' . "\n";

$content_end = '</manifest>' . "\n"
        . '<spine toc="ncx">' . "\n"
        . '<itemref idref="Cover.xhtml" />' . "\n";

if ($post_img == "wi")
    $content_end .= '<itemref idref="Illustrations.xhtml" />' . "\n";

$content_end .= '<itemref idref="Body.xhtml" />' . "\n"
        . '</spine>' . "\n"
        . '<guide>' . "\n"
        . '<reference href="Text/Cover.xhtml" title="Cover" type="cover" />' . "\n"
        . '</guide>' . "\n"
        . '</package>';

if ($result = searchFolder("../ln/images/books/" . $post_series . "/" . $BID . "/*", '/^[Cc]over.jp[e]?g/')) {
    $data = getimagesize("../ln/images/books/" . $post_series . "/" . $BID . "/" . $result);
    $width = $data[0];
    $height = $data[1];
    unset($result);
} else {
    $data = getimagesize('404.jpg');
    $width = $data[0];
    $height = $data[1];
}

$cover = '<?xml version="1.0" encoding="utf-8" standalone="no"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n" . "\n"
        . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n"
        . '<head>' . "\n"
        . '<meta content="true" name="calibre:cover" />' . "\n" . "\n"
        . '<title>Cover</title>' . "\n"
        . '<style type="text/css">' . "\n"
        . '/*<![CDATA[*/' . "\n" . "\n"
        . '@page {padding: 0pt; margin:0pt}' . "\n"
        . 'body { text-align: center; padding:0pt; margin: 0pt; }' . "\n"
        . '/*]]>*/' . "\n"
        . '</style>' . "\n"
        . '</head>' . "\n" . "\n"
        . '<body>' . "\n"
        . '<div>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">' . "\n"
        . '<image height="' . $height . '" width="' . $width . '" xlink:href="../Images/Cover.jpg"></image>' . "\n"
        . '</svg>' . "\n"
        . '</div>' . "\n"
        . '</body>' . "\n"
        . '</html>';

$homepage = file_get_contents('http://www.baka-tsuki.org/project/index.php?action=render&title=' . $zeile['Title']);

$homepage = preg_replace('/<span class="editsection">.*<\/a>]<\/span>/', "", $homepage);
$homepage = preg_replace('%<table\b\sid="toc"[^>]*+>(?:(?R)|[^<]*+(?:(?!</?table\b)<[^<]*+)*+)*+</table>%i', '', $homepage);

/*
 * Warning this might create empty file if there is another table before it with the same tags.
 */
//$homepage = strstr($homepage, '<table class="wikitable collapsible collapsed" style="text-align:left; margin:1em auto 1em auto; clear:both; font-size:100%; background:#E6F2FF; font-weight:900">', TRUE);
//$homepage = preg_replace('%<table\b\sid="collapsibleTable0"[^>]*+>(?:(?R)|[^<]*+(?:(?!</?table\b)<[^<]*+)*+)*+</table>%i', '', $homepage);

if (preg_match('/<h2>/', $homepage) & !preg_match('/<h1>/', $homepage)) {
    $homepage = str_replace("h2", "h1", $homepage);
    $homepage = str_replace("h3", "h2", $homepage);
    $homepage = str_replace("h4", "h3", $homepage);
    $homepage = str_replace("h5", "h4", $homepage);
} elseif (!preg_match('/<h1>/', $homepage) & !preg_match('/<h2>/', $homepage) & preg_match('/<h3>/', $homepage)) {
    $homepage = str_replace("h3", "h1", $homepage);
    $homepage = str_replace("h4", "h2", $homepage);
    $homepage = str_replace("h5", "h3", $homepage);
    $homepage = str_replace("h6", "h4", $homepage);
}

if ($post_img == "wi") {
    withIMG($homepage);
} elseif ($post_img == "ni") {
    withoutIMG($homepage);
}

function getImagesURL($homepage) {

    $input = array();

    $doc = new DOMDocument();
    @$doc->loadHTML($homepage);
    $tags = $doc->getElementsByTagName('a');

    foreach ($tags as $tag) {
        if (preg_match('/\/project\/index\.php\?title=File:/', $tag->getAttribute('href'))) {
            $input[] = end(explode(":", $tag->getAttribute('href')));
        }
    }

    $input = array_unique($input);

    return $input;
}

function getTOC($content) {

    preg_match_all('/<h[0-9].*?>.*?<\/h[0-9]>/i', $content, $matches);
    $toc = array();

    foreach ($matches[0] as $match) {
        preg_match_all('/<h([0-9])>\s*?<span\sclass="mw-headline"\sid="(.*?)">(.*?)<\/h[0-9]>/i', $match, $smatch);

        $toc[] = array('h' => $smatch[1][0], 'id' => $smatch[2][0], 'txt' => strip_tags($smatch[3][0]));

        $content = str_replace($match, '<h' . $smatch[1][0] . ' id="' . $smatch[2][0] . '">' . strip_tags($smatch[3][0]) . '</h' . $smatch[1][0] . '>', $content);
    }

    $html_toc = '<navMap>' . "\n";
    $i = 1;
    $oh = 1;
    $hadfirst = false;

    foreach ($toc as $val) {

        if ($val[h] < $oh) {
            $html_toc .= str_repeat('</navPoint>' . "\n", $oh);
        } elseif ($val[h] > $oh) {
            
        } else {
            $html_toc .= ($hadfirst) ? '</navPoint>' . "\n" : null;
        }

        $html_toc .= '<navPoint id="navPoint-' . $i . '" playOrder="' . $i . '">' . "\n";
        $html_toc .= "\t" . '<navLabel>' . "\n";
        $html_toc .= "\t" . "\t" . '<text>' . $val[txt] . '</text>' . "\n";
        $html_toc .= "\t" . '</navLabel>' . "\n";
        $html_toc .= "\t" . '<content src="Text/Body.xhtml#' . $val[id] . '"/>' . "\n";

        $hadfirst = true;
        $oh = $val[h];
        $i++;
    }

    $html_toc .= '</navPoint>' . "\n";

    if ($oh > 1)
        $html_toc .= '</navPoint>' . "\n";

    $html_toc .= '</navMap>';

    return array('toc' => $html_toc, 'homepage' => $content);
}

function replaceIMG($homepage, $remove) {

    $stringData = '';

    if (!$remove) {

        global $BID, $device, $post_series, $post_size;
        
        $dirimg = array();
        
        if ($handle = opendir('../ln/images/books/' . $post_series . '/' . $BID . '/')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    $data = getimagesize('../ln/images/books/' . $post_series . '/' . $BID . '/' . $entry);
                    $width = $data[0];
                    $height = $data[1];


                    $dirimg[] = array($entry => array('h' => $height, 'w' => $width));
                    $dirimgi[] = $entry;
                }
            }
            closedir($handle);
        }

        $illustrations = "";
        $content_mid = '';

        foreach (getImagesURL($homepage) as $img) {
            if (in_array($img, $dirimgi)) {
                if ($post_size == "original") {
                    $data = getimagesize('../ln/images/books/' . $post_series . '/' . $BID . '/' . $img);
                    $width = $data[0];
                    $height = $data[1];
                } else {
                    $width = $device[$post_size]['w'];
                    $height = $device[$post_size]['h'];
                }

                if ($height > 400 || $width > 400) {

                    $stringData = '<div class="svg_outer svg_inner">
    <svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">
      <image height="' . $height . '" width="' . $width . '" xlink:href="../Images/' . $img . '"></image>
    </svg>
</div>';
                } else {
                    $stringData = '<p><img alt="' . $img . '" class="baka" src="../Images/' . $img . '" /></p>';
                }

                $img_org = $img;
                $img = str_replace(')', '\)', str_replace('(', '\(', $img));

                if (preg_match('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', $homepage)) {
                    $homepage = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', $stringData, $homepage);
                } elseif (preg_match('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', $homepage)) {
                    $homepage = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', $stringData, $homepage);
                } elseif (preg_match('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $homepage)) {
                    $homepage = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $stringData, $homepage);
                } elseif (preg_match('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $homepage)) {
                    $homepage = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $stringData, $homepage);
                } elseif (preg_match('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', $homepage)) {
                    $homepage = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', $stringData, $homepage);
                } else {
                    $illustrations .= $stringData . "\n";
                }

                $img = $img_org;

                if (end(explode(".", $img)) == "jpg" || "jpeg") {
                    $content_mid .= '<item href="Images/' . $img . '" id="' . $img . '" media-type="image/jpeg" />' . "\n";
                } elseif (end(explode(".", $img)) == "png") {
                    $content_mid .= '<item href="Images/' . $img . '" id="' . $img . '" media-type="image/png" />' . "\n";
                }
            } else {
                $homepage = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', '', $homepage);
                $homepage = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', '', $homepage);
                $homepage = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', '', $homepage);
                $homepage = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', '', $homepage);
                $homepage = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', '', $homepage);
            }
        }
    } else {
        $homepage = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>.*<\/div><\/div><\/div>/', $stringData, $homepage);
        $homepage = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/p><\/div>/', $stringData, $homepage);
        $homepage = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>/', $stringData, $homepage);
        $homepage = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>/', $stringData, $homepage);
        $homepage = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/p>/', $stringData, $homepage);
    }

    if (!$remove) {
        return array('homepage' => $homepage, 'ill' => $illustrations, 'content' => $content_mid);
    } else {
        return $homepage;
    }
}

function withIMG($homepage) {

    global $body_start, $body_end, $toc_start, $toci_start, $toc_end, $illustrator, $author, $editor, $translator, $title, $isbn, $desc, $date, $uuid, $uuidi, $BID, $device, $post_series, $post_size, $content_start, $content_end, $cover;

    $file = $title . '.epub';
    $file = str_replace("/", "_", $file);

    if ($post_size == "original") {
        $file_path = "epubi/" . $file;

        if (!copy('wi.epub', $file_path)) {
            echo "copy $file schlug fehl...\n";
            die();
        }
    } else {

        $file_path = "epubi/resized/" . $post_size . "/" . $file;

        if (!is_dir("epubi/resized")) {
            mkdir("epubi/resized");
        }
        if (!is_dir("epubi/resized/" . $post_size)) {
            mkdir("epubi/resized/" . $post_size);
        }

        if (!copy('wi.epub', $file_path)) {
            echo "copy $file schlug fehl...\n";
            die();
        }
    }

    $homepage_arr = replaceIMG($homepage, FALSE);

    $homepage = preg_replace("/<h.>.*?Illustrations.*?<\/h.>/", "", $homepage_arr['homepage']);
    preg_match_all('/<h([0-9]).*?>.*?<\/h[0-9]>/i', $homepage, $matches);
    $homepage = strstr($homepage, '<h' . $matches[1][0] . '>');

    $toc_arr = getTOC($homepage);

    $body = $body_start . $toc_arr['homepage'] . $body_end;
    $illustr = $body_start . $homepage_arr['ill'] . $body_end;
    $toc = $toci_start . $toc_arr['toc'] . $toc_end;
    $content = $content_start . $homepage_arr['content'] . $content_end;
    $toc = preg_replace('/[^(\x20-\x7F)]*/', '', $toc);
    $content = preg_replace('/[^(\x20-\x7F)]*/', '', $content);

    $dirimg = array();
    
    if ($handle = opendir('../ln/images/books/' . $post_series . '/' . $BID . '/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != "cover.jpg" && $entry != "cover.jpeg" && $entry != "Cover.jpg" && $entry != "Cover.jpeg") {
                $dirimg[] = $entry;
            }
        }
        closedir($handle);
    }

    $zip = new ZipArchive;
    if ($zip->open($file_path) === TRUE) {
        $zip->addFromString('OEBPS/Text/Body.xhtml', $body);
        $zip->addFromString('OEBPS/Text/Cover.xhtml', $cover);
        $zip->addFromString('OEBPS/Text/Illustrations.xhtml', $illustr);
        $zip->addFromString('OEBPS/toc.ncx', $toc);
        $zip->addFromString('OEBPS/content.opf', $content);
        if ($result = searchFolder("../ln/images/books/" . $post_series . "/" . $BID . "/*", '/^[Cc]over.jp[e]?g/')) {
            $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/" . $result, 'OEBPS/Images/Cover.jpg');
            unset($result);
        } else {
            $zip->addFile('404.jpg', 'OEBPS/Images/Cover.jpg');
        }
        foreach ($dirimg as $img) {
            if ($post_size == "original") {
                $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/" . $img, 'OEBPS/Images/' . $img);
            } else {
                $width = $device[$post_size]['w'];
                $height = $device[$post_size]['h'];

                if (!is_dir("../ln/images/books/" . $post_series . "/" . $BID . "/resized")) {
                    mkdir("../ln/images/books/" . $post_series . "/" . $BID . "/resized");
                }
                if (!is_dir("../ln/images/books/" . $post_series . "/" . $BID . "/resized/" . $device[$post_size]['h'] . "x" . $device[$post_size]['w'])) {
                    mkdir("../ln/images/books/" . $post_series . "/" . $BID . "/resized/" . $device[$post_size]['h'] . "x" . $device[$post_size]['w']);
                }

                try {

                    $data = getimagesize("../ln/images/books/" . $post_series . "/" . $BID . "/" . $img);
                    $width = $data[0];
                    $height = $data[1];

                    if ($width < 400 || $height < 400) {
                        $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/" . $img, 'OEBPS/Images/' . $img);
                    } else {
                        $image = new Imagick("../ln/images/books/" . $post_series . "/" . $BID . "/" . $img);
                        $image->adaptiveResizeImage($device[$post_size]['h'], $device[$post_size]['w'], TRUE);
                        $image->writeimage("../ln/images/books/" . $post_series . "/" . $BID . "/resized/" . $device[$post_size]['h'] . "x" . $device[$post_size]['w'] . "/" . $img);
                        $image->clear();
                        $image->destroy();
                        $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/resized/" . $device[$post_size]['h'] . "x" . $device[$post_size]['w'] . "/" . $img, 'OEBPS/Images/' . $img);
                    }
                } catch (Exception $e) {
                    $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/" . $img, 'OEBPS/Images/' . $img);
                }
            }
        }
        $zip->close();
    } else {
        echo 'Fehler';
    }
    sendFile($file_path, $title);
}

function withoutIMG($homepage) {

    global $body_start, $body_end, $toc_start, $toc_end, $illustrator, $author, $editor, $translator, $title, $isbn, $desc, $date, $uuid, $BID, $post_series, $content_start, $content_end, $cover;

    $file = $title . '.epub';
    $file = str_replace("/", "_", $file);

    $file_path = "epub/" . $file;

    if (!copy('ni.epub', $file_path)) {
        echo "copy $file schlug fehl...\n";
        die();
    }

    $content = $content_start . $content_end;

    $homepage = preg_replace("/<h.>.*Novel\sIllustrations.*<\/h.>/", "", replaceIMG($homepage, TRUE));
    preg_match_all('/<h([0-9]).*?>.*?<\/h[0-9]>/i', $homepage, $matches);
    $homepage = strstr($homepage, '<h' . $matches[1][0] . '>');

    $toc_arr = getTOC($homepage);


    $body = $body_start . $toc_arr['homepage'] . $body_end;
    $toc = $toc_start . $toc_arr['toc'] . $toc_end;
    $toc = preg_replace('/[^(\x20-\x7F)]*/', '', $toc);
    $content = preg_replace('/[^(\x20-\x7F)]*/', '', $content);

    $zip = new ZipArchive;
    if ($zip->open($file_path) === TRUE) {
        $zip->addFromString('OEBPS/Text/Body.xhtml', $body);
        $zip->addFromString('OEBPS/Text/Cover.xhtml', $cover);
        $zip->addFromString('OEBPS/toc.ncx', $toc);
        $zip->addFromString('OEBPS/content.opf', $content);
        if ($result = searchFolder("../ln/images/books/" . $post_series . "/" . $BID . "/*", '/^[Cc]over.jp[e]?g/')) {
            $zip->addFile("../ln/images/books/" . $post_series . "/" . $BID . "/" . $result, 'OEBPS/Images/Cover.jpg');
            unset($result);
        } else {
            $zip->addFile('404.jpg', 'OEBPS/Images/Cover.jpg');
        }
        $zip->close();
    } else {
        echo 'Fehler';
    }

    sendFile($file_path, $title);
}

function searchFolder($path, $regex) {
    $files = glob($path);
    if (empty($files)) {
        return FALSE;
    } else {
        foreach ($files as $file) {
            if (preg_match($regex, $exp = end(explode('/', $file)))) {
                return $exp;
            }
        }
    }
}

function sendFile($file_path, $title) {
    $fp = fopen($file_path, "r");
    $fstat = fstat($fp);
    fclose($fp);

    if (file_exists($file_path)) {
        header('Content-Type: application/epub+zip');
        header("Content-Length: " . $fstat['size']);
        header('Content-Disposition: attachment; filename="' . $title . '.epub"');
        readfile($file_path);
    }
}

?>