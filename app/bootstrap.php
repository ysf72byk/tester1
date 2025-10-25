<?php
  // Load Config using an absolute path
  require_once dirname(__DIR__) . '/core/config.php';

  // Require our controller using an absolute path
  require_once dirname(__DIR__) . '/core/Controller.php';

  // Autoload Core Libraries using an absolute path
  spl_autoload_register(function($className){
    require_once dirname(__DIR__) . '/core/' . $className . '.php';
  });

  // Load Helpers using an absolute path
  require_once __DIR__ . '/helpers/url_helper.php';
  require_once __DIR__ . '/helpers/session_helper.php';
  require_once __DIR__ . '/helpers/language_helper.php';

  // Load Language
  loadLanguage('tr');
