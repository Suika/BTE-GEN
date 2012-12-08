<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of epub
 *
 * @author Simon
 */
class epub {

    private $zip;
    private $title = "";
    private $fileName = "";
    private $language = "en";
    private $isbn = "";
    private $uuid = "";
    private $description = "";
    private $author = "";
    private $publisher = "";
    private $date = null;
    private $rights = "All materials' copyrights reserved by their respective authors and the associated publishers. Please respect their rights. Works will be deleted upon request by copyright holders.";
    private $depth = 1;
    private $BID = 0;
    private $SID = 0;
    private $illustrator = "";
    private $editor = "";
    private $translator = "";
    private $ncx = "";
    private $manifest = "";
    private $opf = "";
    private $opf_ncx = "";
    private $body = "";
    private $illustartions = "";
    private $cover = "";
    private $ncx_navmap = "";
    private $contentIllustartions = "";
    private $content = "";
    private $contentImgUrls = array();
    private $device = array('sgs2' => array('h' => 800, 'w' => 480),
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
        'sb' => array('h' => 320, 'w' => 240),
        'kobotouch' => array('h' => 800, 'w' => 600));
    private $resolution = "";

    function __construct() {
        include_once("Zip.php");

        $this->zip = new Zip();
        $this->zip->addFile("application/epub+zip", "mimetype");
        $this->zip->addDirectory("META-INF/");
        $this->zip->addDirectory("OEBPS/");
        $this->zip->addDirectory("OEBPS/Images/");
        $this->zip->addDirectory("OEBPS/Styles/");
        $this->zip->addDirectory("OEBPS/Text/");

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">' . PHP_EOL
                . "\t" . '<rootfiles>' . PHP_EOL
                . "\t\t" . '<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>' . PHP_EOL
                . "\t" . '</rootfiles>' . PHP_EOL
                . '</container>';

        $zip->addFile($content, "META-INF/container.xml");
    }

    protected function getContent($urlTitle) {
        $this->content = file_get_contents('http://www.baka-tsuki.org/project/index.php?action=render&title=' . $urlTitle);
        return TRUE;
    }

    protected function cleanContent($content) {
        $content = preg_replace('/<span class="editsection">.*<\/a>]<\/span>/', "", $content);
        $content = preg_replace('%<table\b\sid="toc"[^>]*+>(?:(?R)|[^<]*+(?:(?!</?table\b)<[^<]*+)*+)*+</table>%i', '', $content);
        $this->content = $content;
        return TRUE;
    }

    protected function trimHeaders($content) {
        if (preg_match('/<h2>/', $content) & !preg_match('/<h1>/', $content)) {
            $content = str_replace("h2", "h1", $content);
            $content = str_replace("h3", "h2", $content);
            $content = str_replace("h4", "h3", $content);
            $content = str_replace("h5", "h4", $content);
        } elseif (!preg_match('/<h1>/', $content) & !preg_match('/<h2>/', $content) & preg_match('/<h3>/', $content)) {
            $content = str_replace("h3", "h1", $content);
            $content = str_replace("h4", "h2", $content);
            $content = str_replace("h5", "h3", $content);
            $content = str_replace("h6", "h4", $content);
        }

        $this->content = $content;
        return TRUE;
    }

    protected function getImagesURL($content) {

        $urls = array();

        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $tags = $doc->getElementsByTagName('a');

        foreach ($tags as $tag) {
            if (preg_match('/\/project\/index\.php\?title=File:/', $tag->getAttribute('href'))) {
                array_push($urls, end(explode(":", $tag->getAttribute('href'))));
            }
        }

        $this->contentImgUrls = array_unique($urls);
        return TRUE;
    }

    protected function getTOC($content) {

        preg_match_all('/<h[0-9].*?>.*?<\/h[0-9]>/i', $content, $matches);
        $toc = array();
        $headfirst = FALSE;
        $headfound = FALSE;

        foreach ($matches[0] as $match) {
            preg_match_all('/<h([0-9])>\s*?<span\sclass="mw-headline"\sid="(.*?)">(.*?)<\/h[0-9]>/i', $match, $smatch);

            if ($smatch[1][0] == 1) {
                $headfound = TRUE;
            }
            if ($smatch[1][0] > 1 && $headfirst == FALSE) {
                $toc[] = array('h' => 1, 'id' => preg_replace('/[\+\.\-:]/', '', $smatch[2][0]), 'txt' => trim(strip_tags($smatch[3][0])));
                $content = str_replace($match, '<h1 id="' . preg_replace('/[\+\.\-:]/', '', $smatch[2][0]) . '">' . trim(strip_tags($smatch[3][0])) . '</h1>', $content);
                $headfirst = TRUE;
            } elseif ($smatch[1][0] > 1 && $headfirst == TRUE && $headfound == FALSE) {
                $toc[] = array('h' => ($smatch[1][0] - 1), 'id' => preg_replace('/[\+\.\-:]/', '', $smatch[2][0]), 'txt' => trim(strip_tags($smatch[3][0])));
                $content = str_replace($match, '<h' . ($smatch[1][0] - 1) . ' id="' . preg_replace('/[\+\.\-:]/', '', $smatch[2][0]) . '">' . trim(strip_tags($smatch[3][0])) . '</h' . ($smatch[1][0] - 1) . '>', $content);
            } elseif ($smatch[1][0] == 1 || $headfirst == TRUE && $headfound == TRUE) {
                $headfirst = TRUE;
                $toc[] = array('h' => $smatch[1][0], 'id' => preg_replace('/[\+\.\-:]/', '', $smatch[2][0]), 'txt' => trim(strip_tags($smatch[3][0])));
                $content = str_replace($match, '<h' . $smatch[1][0] . ' id="' . preg_replace('/[\+\.\-:]/', '', $smatch[2][0]) . '">' . trim(strip_tags($smatch[3][0])) . '</h' . $smatch[1][0] . '>', $content);
            }
        }

        $html_toc = '<navMap>' . PHP_EOL;
        $i = 1;
        $oh = 1;
        $depth = 1;
        $hadfirst = false;

        foreach ($toc as $val) {

            if ($val[h] < $oh) {
                $html_toc .= str_repeat('</navPoint>' . PHP_EOL, $oh);
            } elseif ($val[h] > $oh) {
                
            } else {
                $html_toc .= ($hadfirst) ? '</navPoint>' . PHP_EOL : null;
            }

            $html_toc .= '<navPoint id="navPoint-' . $i . '" playOrder="' . $i . '">' . PHP_EOL;
            $html_toc .= "\t" . '<navLabel>' . PHP_EOL;
            $html_toc .= "\t" . "\t" . '<text>' . $val[txt] . '</text>' . PHP_EOL;
            $html_toc .= "\t" . '</navLabel>' . PHP_EOL;

            if ($i == 1) {
                $html_toc .= "\t" . '<content src="Text/Body.xhtml" />' . PHP_EOL;
            } else {
                $html_toc .= "\t" . '<content src="Text/Body.xhtml#' . $val[id] . '" />' . PHP_EOL;
            }

            $hadfirst = true;
            $oh = $val[h];
            $i++;

            if ($val[h] > $depth) {
                $depth = $val[h];
            }
        }

        $html_toc .= '</navPoint>' . PHP_EOL;

        if ($oh > 1)
            $html_toc .= '</navPoint>' . PHP_EOL;

        $html_toc .= '</navMap>';

        $this->depth = $depth;
        $this->ncx_navmap = $html_toc;
        $this->content = $content;

        return TRUE;
    }

    protected function replaceIMG($content, $remove = FALSE) {


        if (!$remove) {

            $dirimg = array();

            if ($handle = opendir('../ln/images/books/' . $this->SID . '/' . $this->BID . '/')) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {

                        $data = getimagesize('../ln/images/books/' . $this->SID . '/' . $this->BID . '/' . $entry);
                        $width = $data[0];
                        $height = $data[1];


                        $dirimg[] = array($entry => array('h' => $height, 'w' => $width));
                        $dirimgi[] = $entry;
                    }
                }
                closedir($handle);
            }

            foreach (getImagesURL($content) as $img) {
                if (in_array($img, $dirimgi)) {
                    if ($this->resolution == "original") {
                        $data = getimagesize('../ln/images/books/' . $this->SID . '/' . $this->BID . '/' . $img);
                        $width = $data[0];
                        $height = $data[1];
                    } else {
                        $width = $this->device[$this->resolution]['w'];
                        $height = $this->device[$this->resolution]['h'];
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

                    if (preg_match('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', $content)) {
                        $content = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', $stringData, $content);
                    } elseif (preg_match('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', $content)) {
                        $content = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', $stringData, $content);
                    } elseif (preg_match('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $content)) {
                        $content = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $stringData, $content);
                    } elseif (preg_match('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $content)) {
                        $content = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', $stringData, $content);
                    } elseif (preg_match('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', $content)) {
                        $content = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', $stringData, $content);
                    } else {
                        $this->contentIllustartions .= $stringData . PHP_EOL;
                    }

                    $img = $img_org;

                    if (end(explode(".", $img)) == "jpg" || "jpeg") {
                        $this->manifest .= '<item href="Images/' . $img . '" id="' . $img . '" media-type="image/jpeg" />' . PHP_EOL;
                    } elseif (end(explode(".", $img)) == "png") {
                        $this->manifest .= '<item href="Images/' . $img . '" id="' . $img . '" media-type="image/png" />' . PHP_EOL;
                    } elseif (end(explode(".", $img)) == "gif") {
                        $this->manifest .= '<item href="Images/' . $img . '" id="' . $img . '" media-type="image/gif" />' . PHP_EOL;
                    }
                } else {
                    $content = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>.*<\/div><\/div><\/div>/', '', $content);
                    $content = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p><\/div>/', '', $content);
                    $content = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', '', $content);
                    $content = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/div>/', '', $content);
                    $content = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:' . $img . '".*<\/a><\/p>/', '', $content);
                }
            }
        } else {
            $content = preg_replace('/<div class="thumb tright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>.*<\/div><\/div><\/div>/', $stringData, $content);
            $content = preg_replace('/<div style="width: 100%; overflow:auto;">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/p><\/div>/', $stringData, $content);
            $content = preg_replace('/<div class="floatright">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>/', $stringData, $content);
            $content = preg_replace('/<div class="floatleft">.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/div>/', $stringData, $content);
            $content = preg_replace('/<p>.*href="http:\/\/www.baka-tsuki.org\/project\/index.php\?title=File:.*<\/a><\/p>/', $stringData, $content);
        }

        if (!$remove) {
            $this->content = $content;
            return TRUE;
        } else {
            $this->content = $content;
            return TRUE;
        }
    }

    protected function searchFolder($path, $regex) {
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

    protected function removeBeforeHeader($content) {
        $content = preg_replace("/<h.>.*?Illustrations.*?<\/h.>/", "", $content);
        preg_match_all('/<h([0-9]).*?>.*?<\/h[0-9]>/i', $content, $matches);
        $content = strstr($content, '<h' . $matches[1][0] . '>');

        $this->content = $content;
    }

    protected function forceASCII($ncx, $opf) {
        $this->ncx = preg_replace('/[^(\x20-\x7F)]*/', '', $ncx);
        $this->opf = preg_replace('/[^(\x20-\x7F)]*/', '', $opf);
    }

    protected function constructNCX($navmap, $uuid, $depth, $title) {
        $this->ncx =
                '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL
                . '<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN"' . PHP_EOL
                . '"http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">' . PHP_EOL
                . '<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">' . PHP_EOL
                . "\t" . '<head>' . PHP_EOL
                . "\t\t" . '<meta name="dtb:uid" content="urn:uuid:' . $uuid . '" />' . PHP_EOL
                . "\t\t" . '<meta name="dtb:depth" content="' . $depth . '" />' . PHP_EOL
                . "\t\t" . '<meta name="dtb:totalPageCount" content="0" />' . PHP_EOL
                . "\t\t" . '<meta name="dtb:maxPageNumber" content="0" />' . PHP_EOL
                . "\t" . '</head>' . PHP_EOL
                . "\t" . '<docTitle>' . PHP_EOL
                . "\t\t" . '<text>' . $title . '</text>' . PHP_EOL
                . "\t" . '</docTitle>' . PHP_EOL
                . $navmap
                . '</ncx>';
    }

    protected function constructOPF() {
        $this->opf =
                '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . PHP_EOL
                . '<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookId" version="2.0">' . PHP_EOL
                . "\t" . '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">' . PHP_EOL;

        if (!empty($this->uuid))
            $this->ncx .= '<dc:identifier id="BookId" opf:scheme="UUID">urn:uuid:' . $this->uuid . '</dc:identifier>' . PHP_EOL;

        if (!empty($this->title))
            $this->ncx .= '<dc:title>' . $this->title . '</dc:title>' . PHP_EOL;

        if (!empty($this->illustrator))
            $this->ncx .= '<dc:contributor opf:role="ill">' . $this->illustrator . '</dc:contributor>' . PHP_EOL;

        if (!empty($this->isbn))
            $this->ncx .= '<dc:identifier opf:scheme="ISBN">' . $this->isbn . '</dc:identifier>' . PHP_EOL;

        if (!empty($this->author)) {
            $temp = explode(" ", $this->author);
            $this->ncx .= '<dc:creator opf:file-as="' . $temp[1] . ', ' . $temp[0] . '" opf:role="aut">' . $this->author . '</dc:creator>' . PHP_EOL;
            unset($temp);
        }

        if (!empty($this->description))
            $this->ncx .= '<dc:description>' . $this->description . '</dc:description>' . PHP_EOL;

        if (!empty($this->date)) {
            $this->ncx .= '<dc:date>' . $this->date . '</dc:date>' . PHP_EOL;
            $this->ncx .= '<dc:date opf:event="creation">' . $this->date . '</dc:date>' . PHP_EOL;
        }

        if (!empty($this->editor))
            $this->ncx .= '<dc:contributor opf:role="edt">' . $this->editor . '</dc:contributor>' . PHP_EOL;

        if (!empty($this->translator))
            $this->ncx .= '<dc:contributor opf:file-as="' . $this->translator . '" opf:role="trl">' . $this->translator . '</dc:contributor>' . PHP_EOL;

        if (!empty($this->date))
            $this->ncx .= '<dc:date opf:event="publication">' . $this->date . '</dc:date>' . PHP_EOL;

        if (!empty($this->publisher))
            $this->ncx .= '<dc:publisher>' . $this->publisher . '</dc:publisher>' . PHP_EOL;

        if (!empty($this->language))
            $this->ncx .= '<dc:language>' . $this->language . '</dc:language>' . PHP_EOL;

        if (!empty($this->rights))
            $this->ncx .= '<dc:rights>' . $this->rights . '</dc:rights>' . PHP_EOL;

        $this->ncx .= '<meta content="Cover.jpg" name="cover" />' . PHP_EOL
                . '</metadata>' . PHP_EOL
                . '<manifest>' . PHP_EOL
                . '<item href="toc.ncx" id="ncx" media-type="application/x-dtbncx+xml" />' . PHP_EOL
                . '<item href="Text/Cover.xhtml" id="Cover.xhtml" media-type="application/xhtml+xml" />' . PHP_EOL
                . '<item href="Styles/stylesheet.css" id="stylesheet.css" media-type="text/css" />' . PHP_EOL
                . '<item href="Styles/page_styles.css" id="page_styles.css" media-type="text/css" />' . PHP_EOL
                . '<item href="Text/Body.xhtml" id="Body.xhtml" media-type="application/xhtml+xml" />' . PHP_EOL;
        if (!empty($this->manifest))
            $this->ncx .= $this->manifest;

        if (!empty($this->contentIllustartions))
            $this->ncx .= '<item href="Text/Illustrations.xhtml" id="Illustrations.xhtml" media-type="application/xhtml+xml" />';

        $this->ncx .= '</manifest>' . PHP_EOL
                . '<spine toc="ncx">' . PHP_EOL
                . '<itemref idref="Cover.xhtml" />' . PHP_EOL;

        if (!empty($this->contentIllustartions))
            $this->ncx .= '<itemref idref="Illustrations.xhtml" />' . PHP_EOL;

        $this->ncx .= '<itemref idref="Body.xhtml" />' . PHP_EOL
                . '</spine>' . PHP_EOL
                . '<guide>' . PHP_EOL
                . '<reference href="Text/Cover.xhtml" title="Cover" type="cover" />' . PHP_EOL
                . '</guide>' . PHP_EOL
                . '</package>';
    }

    protected function constructCover() {
        if ($result = searchFolder("../ln/images/books/" . $this->SID . "/" . $this->BID . "/*", '/^[Cc]over.jp[e]?g/')) {
            $data = getimagesize("../ln/images/books/" . $this->SID . "/" . $this->BID . "/" . $result);
            $width = $data[0];
            $height = $data[1];
            unset($result);
        } else {
            $data = getimagesize('404.jpg');
            $width = $data[0];
            $height = $data[1];
        }

        $this->cover = '<?xml version="1.0" encoding="utf-8" standalone="no"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . PHP_EOL . PHP_EOL
                . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . PHP_EOL
                . '<head>' . PHP_EOL
                . '<meta content="true" name="calibre:cover" />' . PHP_EOL . PHP_EOL
                . '<title>Cover</title>' . PHP_EOL
                . '<style type="text/css">' . PHP_EOL
                . '/*<![CDATA[*/' . PHP_EOL . PHP_EOL
                . '@page {padding: 0pt; margin:0pt}' . PHP_EOL
                . 'body { text-align: center; padding:0pt; margin: 0pt; }' . PHP_EOL
                . '/*]]>*/' . PHP_EOL
                . '</style>' . PHP_EOL
                . '</head>' . PHP_EOL . PHP_EOL
                . '<body>' . PHP_EOL
                . '<div>' . PHP_EOL;
        if ($this->resolution == "original") {
            $this->cover .= '<svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">' . PHP_EOL
                    . '<image height="' . $height . '" width="' . $width . '" xlink:href="../Images/Cover.jpg"></image>' . PHP_EOL;
        } else {
            $this->cover .= '<svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 ' . $device[$this->resolution]['w'] . ' ' . $device[$this->resolution]['h'] . '" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">' . PHP_EOL
                    . '<image height="' . $this->device[$this->resolution]['h'] . '" width="' . $this->device[$this->resolution]['w'] . '" xlink:href="../Images/Cover.jpg"></image>' . PHP_EOL;
        }

        $this->cover .= '</svg>' . PHP_EOL
                . '</div>' . PHP_EOL
                . '</body>' . PHP_EOL
                . '</html>';
    }

    public function processEpub() {
        
    }

    private function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->setFileName($title);
        $this->title = $title;
    }

    private function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    private function getIsbn() {
        return $this->isbn;
    }

    public function setIsbn($isbn) {
        $this->isbn = $isbn;
    }

    private function getUuid() {
        return $this->uuid;
    }

    public function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    private function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    private function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    private function getPublisher() {
        return $this->publisher;
    }

    public function setPublisher($publisher) {
        $this->publisher = $publisher;
    }

    private function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    private function getRights() {
        return $this->rights;
    }

    public function setRights($rights) {
        $this->rights = $rights;
    }

    private function getIllustrator() {
        return $this->illustrator;
    }

    public function setIllustrator($illustrator) {
        $this->illustrator = $illustrator;
    }

    private function getEditor() {
        return $this->editor;
    }

    public function setEditor($editor) {
        $this->editor = $editor;
    }

    private function getResolution() {
        return $this->resolution;
    }

    public function setResolution($resolution) {
        $this->resolution = $resolution;
    }

    private function getFileName() {
        return $this->fileName;
    }

    public function setFileName($fileName) {
        $this->fileName = str_replace("/", "_", $fileName) . ".epub";
    }

}

?>
