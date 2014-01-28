<?php
require_once '../sql/functions.php';

$db = getDBconn();
$rss = array();

(!empty($_GET['rss'])) ? $rss[] = array('rss' => $_GET['rss']) : $rss[] = array('rss' => NULL);
(!empty($_GET['id'])) ? $rss[] = array('id' => $_GET['id']) : $rss[] = array('id' => NULL);
(!empty($_GET['format'])) ? $rss[] = array('format' => $_GET['format']) : $rss[] = array('format' => NULL);

constructRss($rss, $db);
