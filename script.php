<?php

$zip_uri = 'private://zipName.zip';                                       // zip file path
$zipname = drupal_realpath($zip_uri);

$zip = new ZipArchive;                                                    // create zip Archive
$zip->open($zipname, ZipArchive::CREATE);
$dir = "zipDir";                                                          // directory inside zip
$zip->addEmptyDir($dir);

$query = "SELECT nid FROM `node` where type='article'";                   // select node article
$record = db_query($query);
$articles = $record->fetchCol();
if(!empty($articles)){
  foreach ($articles as $nid) {
    $article = node_load($nid);
    if(!empty($article->field_article_doc)){                              // get file from {field_article_doc}
      $f = $article->field_article_doc[LANGUAGE_NONE][0];
      $f = (object) $f;
      $ext = pathinfo(basename($f->uri), PATHINFO_EXTENSION);
      $new_filename = $article->title .".". $ext;
      $zip->addFile(drupal_realpath($f->uri), $dir.'/'. $new_filename);    // push file into zip
    }
  }
}

$zip->close();                                                              // close zipArchive

file_save((object)['uri' => $zip_uri]);                                     // you can save zipfile if you want it to be an entity file and refer it in database

