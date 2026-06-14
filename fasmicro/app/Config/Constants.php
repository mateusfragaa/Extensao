<?php

// Definir o time zone default do projeto
defined("DEFAULT_TIME_ZONE") || define("DEFAULT_TIME_ZONE", "America/Sao_Paulo");

// Controller padrão a ser executado
defined("DEFAULT_CONTROLLER") || define("DEFAULT_CONTROLLER", "Home");

// Método padrão do controller a ser executado
defined("DEFAULT_METHOD") || define("DEFAULT_METHOD", "index");

// Controllers autorizados a executar sem autenticação
defined("CONTROLLER_AUTH") || define("CONTROLLER_AUTH", [
    "Home",
    "Login"
]);

// Tamanho máximo para upload de arquivos (5 mega bytes)
defined('FILE_MAXSIZE') || define('FILE_MAXSIZE', 5);

// Versão padrão da API (usada quando a URL não especifica versão)
defined('API_VERSION') || define('API_VERSION', 1);

// ---------------------------------------------------------------------------
// Proteção CSRF
// ---------------------------------------------------------------------------

// Ativa/desativa a proteção CSRF nas rotas web (true = ativo)
defined('CSRF_PROTECTION')   || define('CSRF_PROTECTION',   true);

// Nome do campo hidden e da chave de sessão que armazena o token
defined('CSRF_TOKEN_NAME')   || define('CSRF_TOKEN_NAME',   'csrf_token');

// Nome do header HTTP aceito em requisições AJAX (ex: fetch/axios)
defined('CSRF_HEADER_NAME')  || define('CSRF_HEADER_NAME',  'X-CSRF-Token');

// Tempo de vida do token em segundos (padrão: 2 horas)
defined('CSRF_EXPIRE')       || define('CSRF_EXPIRE',        7200);

// true = gera novo token após cada validação bem-sucedida (mais seguro,
// mas pode quebrar abas concorrentes); false = token estável por sessão/TTL
defined('CSRF_REGENERATE')   || define('CSRF_REGENERATE',    false);

// URIs que ficam fora da validação CSRF (array de prefixos/substrings)
defined('CSRF_EXCLUDE_URIS') || define('CSRF_EXCLUDE_URIS', []);

// ---------------------------------------------------------------------------

// Arquivos aceitos em Uploads
defined('FILE_ALLOWEDTYPES') || define('FILE_ALLOWEDTYPES', [
    'image/jpg', 
    'image/jpeg', 
    'image/png', 
    'image/gif', 
    'image/bmp',
    'image/webp',
    'image/svg+xml',
    'application/pdf',
    'application/msword',                                                           // DOC (Word 97-2003)
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',      // DOCX 
    'application/vnd.ms-excel',                                                     // XLS (Excel 97-2003)
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',            // XLSX (Excel 2007+)
    'application/vnd.ms-powerpoint',                                                // PPT (PowerPoint 97-2003)
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',    // PPTX 
    'text/plain',                                                                   // TXT
    'text/csv',
    'application/zip',
    'application/x-rar-compressed',
    'audio/mpeg',
    'audio/wav',
    'audio/ogg',
    'audio/aac',
    'video/mp4',
    'video/webm',
    'video/ogg',
    'video/x-msvideo',
    'application/json',
    'application/xml'
]);
