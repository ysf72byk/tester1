<?php
// Load the language file
function loadLanguage($lang = 'en'){
  if(file_exists('../app/languages/' . $lang . '.php')){
    require_once '../app/languages/' . $lang . '.php';
  } else {
    require_once '../app/languages/en.php';
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
