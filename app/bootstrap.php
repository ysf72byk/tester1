<?php
  // Load Config
  require_once '../core/config.php';

  // Require our controller
  require_once '../core/Controller.php';

  // Autoload Core Libraries
  spl_autoload_register(function($className){
    require_once '../core/' . $className . '.php';
  });

  // Load Helpers
  require_once 'helpers/url_helper.php';
  require_once 'helpers/session_helper.php';
  require_once 'helpers/language_helper.php';

  // Load Language
  loadLanguage('tr');
