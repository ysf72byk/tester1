<?php
// Load the language file
function loadLanguage($lang = 'en'){
  $lang_file_path = dirname(__DIR__) . '/languages/' . $lang . '.php';
  if(file_exists($lang_file_path)){
    require_once $lang_file_path;
  } else {
    require_once dirname(__DIR__) . '/languages/en.php';
  }
}

// Translate function
function __($key){
  global $lang;
  if(isset($lang[$key])){
    return $lang[$key];
  } else {
    return $key;
  }
}
