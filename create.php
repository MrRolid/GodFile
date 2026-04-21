<?php
// --- SECURE SESSION INIT ---
session_set_cookie_params([
    'lifetime' => 86400,
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

// --- BASIC SECURITY HEADERS ---
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// --- CSRF TOKEN GENERATION ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('HTTP/1.1 403 Forbidden');
        die(json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']));
    }
}

// --- MULTILINGUAL SUPPORT (i18n) ---
$supported = ['en', 'zh', 'es', 'cs'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported)) {
    $_SESSION['lang'] = $_GET['lang'];
    $params = $_GET;
    unset($params['lang']);
    $url = strtok($_SERVER["REQUEST_URI"], '?') . (!empty($params) ? '?' . http_build_query($params) : '');
    header("Location: " . $url);
    exit;
}

if (!isset($_SESSION['lang'])) {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
    $_SESSION['lang'] = in_array($browser_lang, $supported) ? $browser_lang : 'en';
}
$lang = $_SESSION['lang'];

$i18n = [
    'en' => [
        'nav_editor' => 'Editor', 'nav_gallery' => 'Gallery', 'nav_users' => 'Users', 'nav_settings' => 'Settings', 'nav_logout' => 'Logout',
        'login_title' => 'Login', 'username' => 'Username', 'password' => 'Password', 'enter' => 'Enter',
        'cmd_line' => 'Command Line', 'prompt_ph' => 'Click an element in the preview or type what to build/change...',
        'insert_hint' => 'Click an image to insert its name into the prompt:', 'execute' => 'Execute',
        'history' => 'Change History', 'restore' => 'Restore', 'changed' => 'Changed:', 'empty_project' => 'Nothing here yet. Write a prompt to generate the site.',
        'upload_title' => 'Upload Custom Image (Max 2MB)', 'upload_btn' => 'Upload to Catalog',
        'avail_img' => 'Available AI Images', 'copy_hint' => 'Click the image to copy its name.', 'no_img' => 'No images yet.', 'delete' => 'Delete',
        'config' => 'Configuration', 'provider' => 'Provider', 'folder' => 'Web Folder:', 'api_key' => 'API Key:', 'model' => 'Model:',
        'fetch' => 'Fetch', 'local_url' => 'Local API URL:', 'email' => 'Contact Form Email:', 'save' => 'Save All',
        'test_api' => 'Test API Connection', 'run_test' => 'Run Test',
        'manage_users' => 'Manage Users', 'new_pass' => 'New password', 'change' => 'Change', 'new_name' => 'New name', 'add' => 'Add',
        'terminal' => 'Task Terminal', 'time' => 'Time', 'close_reload' => 'Close terminal and reload preview',
        'tracking' => 'Track clicks', 'open_web' => 'Open clean web ↗',
        'js_confirm' => 'Are you sure?', 'js_copied' => 'Copied: ',
        'js_start' => '--- PROCESS STARTED ---', 'js_plan' => 'PHASE 1: Planning...', 'js_img' => 'Creating image:', 'js_code' => 'Generating code:',
        'js_done' => 'Done. Log saved.', 'js_err' => 'FATAL ERROR', 'js_err_api' => 'API Error:',
        'setup_title' => 'Initial Setup', 'setup_desc' => 'Create the main administrator account.',
        'img_provider' => 'Image Generator:', 'img_api_key' => 'API Key (Images):', 'test_img_api' => 'Test Image API'
    ],
    'cs' => [
        'nav_editor' => 'Editor', 'nav_gallery' => 'Galerie', 'nav_users' => 'Uživatelé', 'nav_settings' => 'Nastavení', 'nav_logout' => 'Odhlásit',
        'login_title' => 'Přihlášení', 'username' => 'Uživatel', 'password' => 'Heslo', 'enter' => 'Vstoupit',
        'cmd_line' => 'Příkazová řádka', 'prompt_ph' => 'Klikni do vedlejšího okna pro výběr elementu, nebo rovnou napiš co chceš vytvořit/změnit...',
        'insert_hint' => 'Klikni na obrázek pro vložení jeho názvu do promptu:', 'execute' => 'Vykonat',
        'history' => 'Historie změn', 'restore' => 'Obnovit', 'changed' => 'Změněno:', 'empty_project' => 'Zatím tu nic není. Napiš prompt a vygeneruj web.',
        'upload_title' => 'Nahrát vlastní obrázek (Max 2MB)', 'upload_btn' => 'Nahrát do katalogu',
        'avail_img' => 'Dostupné obrázky pro AI', 'copy_hint' => 'Kliknutím zkopíruješ název.', 'no_img' => 'Zatím žádné obrázky.', 'delete' => 'Smazat',
        'config' => 'Konfigurace', 'provider' => 'Provider:', 'folder' => 'Složka webu:', 'api_key' => 'API Klíč:', 'model' => 'Model:',
        'fetch' => 'Načíst', 'local_url' => 'Lokální API URL:', 'email' => 'E-mail pro kontaktní formuláře:', 'save' => 'Uložit vše',
        'test_api' => 'Test připojení API (Text)', 'run_test' => 'Provést test',
        'manage_users' => 'Správa uživatelů', 'new_pass' => 'Nové heslo', 'change' => 'Změnit', 'new_name' => 'Nové jméno', 'add' => 'Přidat',
        'terminal' => 'Terminál úloh', 'time' => 'Čas', 'close_reload' => 'Zavřít terminál a aktualizovat náhled',
        'tracking' => 'Sledovat kliknutí', 'open_web' => 'Otevřít čistý web ↗',
        'js_confirm' => 'Opravdu?', 'js_copied' => 'Zkopírováno: ',
        'js_start' => '--- START PROCESU ---', 'js_plan' => 'FÁZE 1: Plánování...', 'js_img' => 'Tvořím obrázek:', 'js_code' => 'Generuji kód:',
        'js_done' => 'Hotovo. Log zapsán.', 'js_err' => 'FATÁLNÍ CHYBA', 'js_err_api' => 'Chyba API:',
        'setup_title' => 'Prvotní instalace', 'setup_desc' => 'Vytvoř si hlavní administrátorský účet.',
        'img_provider' => 'Generátor obrázků:', 'img_api_key' => 'API Klíč (Obrázky):', 'test_img_api' => 'Test připojení API (Obrázky)'
    ],
    'es' => [
        'nav_editor' => 'Editor', 'nav_gallery' => 'Galería', 'nav_users' => 'Usuarios', 'nav_settings' => 'Ajustes', 'nav_logout' => 'Salir',
        'login_title' => 'Iniciar sesión', 'username' => 'Usuario', 'password' => 'Contraseña', 'enter' => 'Entrar',
        'cmd_line' => 'Línea de comandos', 'prompt_ph' => 'Haz clic en un elemento de la vista previa...',
        'insert_hint' => 'Haz clic en una imagen para insertar:', 'execute' => 'Ejecutar',
        'history' => 'Historial de cambios', 'restore' => 'Restaurar', 'changed' => 'Cambiado:', 'empty_project' => 'No hay nada aquí todavía.',
        'upload_title' => 'Subir imagen propia (Max 2MB)', 'upload_btn' => 'Subir al catálogo',
        'avail_img' => 'Imágenes de IA disponibles', 'copy_hint' => 'Haz clic para copiar.', 'no_img' => 'Aún no hay imágenes.', 'delete' => 'Eliminar',
        'config' => 'Configuración', 'provider' => 'Proveedor:', 'folder' => 'Carpeta web:', 'api_key' => 'Clave API:', 'model' => 'Modelo:',
        'fetch' => 'Obtener', 'local_url' => 'URL API Local:', 'email' => 'Email de contacto:', 'save' => 'Guardar todo',
        'test_api' => 'Probar conexión API', 'run_test' => 'Ejecutar prueba',
        'manage_users' => 'Administrar usuarios', 'new_pass' => 'Nueva contraseña', 'change' => 'Cambiar', 'new_name' => 'Nuevo nombre', 'add' => 'Añadir',
        'terminal' => 'Terminal de tareas', 'time' => 'Tiempo', 'close_reload' => 'Cerrar terminal',
        'tracking' => 'Rastrear clics', 'open_web' => 'Abrir web limpia ↗',
        'js_confirm' => '¿Estás seguro?', 'js_copied' => 'Copiado: ',
        'js_start' => '--- PROCESO INICIADO ---', 'js_plan' => 'FASE 1: Planificando...', 'js_img' => 'Creando imagen:', 'js_code' => 'Generando código:',
        'js_done' => 'Hecho. Registro guardado.', 'js_err' => 'ERROR FATAL', 'js_err_api' => 'Error de API:',
        'setup_title' => 'Configuración Inicial', 'setup_desc' => 'Crea la cuenta de administrador principal.',
        'img_provider' => 'Generador de imágenes:', 'img_api_key' => 'Clave API (Imágenes):', 'test_img_api' => 'Test Image API'
    ],
    'zh' => [
        'nav_editor' => '编辑器', 'nav_gallery' => '图库', 'nav_users' => '用户', 'nav_settings' => '设置', 'nav_logout' => '登出',
        'login_title' => '登录', 'username' => '用户名', 'password' => '密码', 'enter' => '进入',
        'cmd_line' => '命令行', 'prompt_ph' => '点击预览中的元素...',
        'insert_hint' => '点击图片插入：', 'execute' => '执行',
        'history' => '更改历史', 'restore' => '恢复', 'changed' => '已更改：', 'empty_project' => '这里还是空的。',
        'upload_title' => '上传自定义图片 (最大 2MB)', 'upload_btn' => '上传',
        'avail_img' => '可用图片', 'copy_hint' => '点击复制。', 'no_img' => '暂无图片。', 'delete' => '删除',
        'config' => '配置', 'provider' => '提供商：', 'folder' => '文件夹：', 'api_key' => 'API 密钥：', 'model' => '模型：',
        'fetch' => '获取', 'local_url' => '本地 API：', 'email' => '联系表单邮箱：', 'save' => '保存全部',
        'test_api' => '测试 API', 'run_test' => '运行测试',
        'manage_users' => '管理用户', 'new_pass' => '新密码', 'change' => '更改', 'new_name' => '新名称', 'add' => '添加',
        'terminal' => '任务终端', 'time' => '时间', 'close_reload' => '关闭并重新加载',
        'tracking' => '追踪点击', 'open_web' => '打开纯净网页 ↗',
        'js_confirm' => '你确定吗？', 'js_copied' => '已复制：',
        'js_start' => '--- 开始 ---', 'js_plan' => '规划中...', 'js_img' => '创建图片：', 'js_code' => '生成代码：',
        'js_done' => '完成。', 'js_err' => '致命错误', 'js_err_api' => 'API 错误：',
        'setup_title' => '初始设置', 'setup_desc' => '创建主管理员帐户。',
        'img_provider' => '图像提供商:', 'img_api_key' => 'API 密钥 (图像):', 'test_img_api' => 'Test Image API'
    ]
];

function t($key) { 
    global $i18n, $lang; 
    return $i18n[$lang][$key] ?? $i18n['en'][$key] ?? $key; 
}

// --- DATABASE CONFIGURATION ---
$db_file = __DIR__ . "/app_data.sqlite";
$pdo = new PDO("sqlite:" . $db_file);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE, password TEXT)");
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT)");
$pdo->exec("CREATE TABLE IF NOT EXISTS logs (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, prompt TEXT, files_changed TEXT, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)");
$pdo->exec("CREATE TABLE IF NOT EXISTS file_versions (id INTEGER PRIMARY KEY AUTOINCREMENT, log_id INTEGER, filename TEXT, content TEXT)");

$default_settings = [
    "api_key" => "", 
    "output_path" => "website_output", 
    "model" => "gemini-2.5-flash",
    "provider" => "google", 
    "local_url" => "http://localhost:11434/v1/chat/completions",
    "contact_email" => "", 
    "img_provider" => "pollinations", 
    "img_api_key" => ""
];

foreach ($default_settings as $k => $v) {
    $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)")->execute([$k, $v]);
}

$settings = [];
foreach ($pdo->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $settings[$row["key"]] = $row["value"];
}

// --- PATH PROTECTION ---
$base_dir = realpath(__DIR__);
$req_path = rtrim($settings["output_path"], '/\\');

if (strpos($req_path, '/') !== 0 && strpos($req_path, ':\\') !== 1) {
    $req_path = __DIR__ . '/' . $req_path;
}

if (!file_exists($req_path)) {
    @mkdir($req_path, 0755, true);
}

$real_safe = realpath($req_path);

if ($real_safe === false || strpos($real_safe, $base_dir) !== 0) {
    $safe_output_dir = $base_dir . "/website_output";
    if (!file_exists($safe_output_dir)) {
        @mkdir($safe_output_dir, 0755, true);
    }
    $web_path = "website_output";
} else {
    $safe_output_dir = $real_safe;
    $web_path = ltrim(substr($safe_output_dir, strlen($base_dir)), '/\\');
}

if (empty($web_path)) {
    $web_path = "website_output";
}

$action = $_POST["action"] ?? $_GET["action"] ?? "";

// --- INSTALLATION CHECK (NO DEFAULT ADMIN) ---
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($user_count == 0) {
    if ($action === "setup_admin" && !empty($_POST['u']) && strlen($_POST['p']) >= 8) {
        $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([
            $_POST["u"], 
            password_hash($_POST["p"], PASSWORD_DEFAULT)
        ]);
        header("Location: " . $_SERVER["PHP_SELF"]); 
        exit;
    }
    $action = "show_setup";
}

// --- LOGOUT (CSRF PROTECTED) ---
if ($action === "logout") { 
    if (isset($_GET['token']) && hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        session_destroy(); 
    }
    header("Location: " . $_SERVER["PHP_SELF"]); 
    exit; 
}

// --- AUTHENTICATION ENFORCEMENT ---
if (!isset($_SESSION["user_id"]) && $action !== "login" && $action !== "show_setup") { 
    $action = "show_login"; 
}

// Define roles
$is_superadmin = (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 1);

// --- AUTHORIZATION & ROLE ENFORCEMENT ---
$admin_only_actions = ['update_settings', 'user_ops'];
if (in_array($action, $admin_only_actions) && !$is_superadmin) {
    header('HTTP/1.1 403 Forbidden'); 
    die(json_encode(['status' => 'error', 'message' => 'Superadmin access required.']));
}

$protected_actions = [
    'upload_img', 'del_img', 'fetch_models', 'test_api', 'test_img_api', 
    'restore', 'plan', 'build_file', 'gen_image', 'save_log'
];
if (in_array($action, $protected_actions) && !isset($_SESSION["user_id"])) {
    header('HTTP/1.1 403 Forbidden'); 
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized.']));
}

// --- RATE LIMIT FOR AI ENDPOINTS ---
if (in_array($action, ["plan", "build_file", "gen_image"])) {
    $now = time();
    if (isset($_SESSION['last_ai_call']) && ($now - $_SESSION['last_ai_call']) < 1) {
        die(json_encode(["status" => "error", "message" => "Rate limit exceeded."]));
    }
    $_SESSION['last_ai_call'] = $now;
}

// --- VISUAL INSPECTOR PROXY (SECURE SANDBOX) ---
if ($action === "preview_site") {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    $file = basename($_GET["file"] ?? "index.html");
    $path = $safe_output_dir . "/" . $file;
    
    if (file_exists($path)) {
        $html = file_get_contents($path);
        
        $html = preg_replace_callback('/(href|src)=["\'](?![a-zA-Z]+:\/\/)([^"\']+\.(css|js))["\']/', function($m) {
            return $m[1] . '="' . $m[2] . '?t=' . time() . '"';
        }, $html);
        
        $origin = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];
        $base_tag = "<base href=\"$origin/$web_path/\">";
        
        if (stripos($html, "<head>") !== false) {
            $html = str_ireplace("<head>", "<head>\n" . $base_tag, $html);
        } else {
            $html = $base_tag . $html;
        }

        // JS includes token check against CSRF inside iframe interactions
        $inspector_js = "
        <script>
            let lastOutlined = null;
            document.addEventListener('mouseover', e => {
                if(lastOutlined) lastOutlined.style.outline = '';
                lastOutlined = e.target; 
                e.target.style.outline = '2px solid #ef4444'; 
                e.target.style.cursor = 'crosshair'; 
                e.target.style.outlineOffset = '-2px';
            });
            document.addEventListener('mouseout', e => { 
                if(lastOutlined) lastOutlined.style.outline = ''; 
            });
            document.addEventListener('click', e => {
                e.preventDefault(); 
                e.stopPropagation();
                if(lastOutlined) lastOutlined.style.outline = '';
                
                let t = e.target; 
                let sel = t.tagName.toLowerCase();
                
                if (t.id) sel += '#' + t.id;
                
                if (t.className && typeof t.className === 'string') {
                    sel += '.' + t.className.trim().split(/\s+/).join('.');
                }
                
                let text = sel.startsWith('img') 
                    ? 'Image: ' + (t.getAttribute('src') || '').split('/').pop().split('?')[0] 
                    : (t.innerText ? t.innerText.substring(0, 40) + '...' : '');
                
                // Uses '*' because iframe runs in sandbox without allow-same-origin (origin is 'null')
                window.parent.postMessage({ 
                    type: 'element_selected', 
                    selector: sel, 
                    text: text, 
                    token: '{$_SESSION['csrf_token']}' 
                }, '*');
            });
        </script>";
        
        if (stripos($html, "</body>") !== false) {
            $html = str_ireplace("</body>", $inspector_js . "\n</body>", $html);
        } else {
            $html .= $inspector_js;
        }
        
        echo $html;
    } else {
        echo "<body style='background:#111; color:#fff; font-family:sans-serif; padding:20px;'><h3>" . t('empty_project') . "</h3></body>";
    }
    exit;
}

if ($action === "view_img") {
    $file = basename($_GET["file"] ?? "");
    $path = $safe_output_dir . "/" . $file;
    
    if ($file && file_exists($path)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($mime, $allowed_mimes)) {
            header("Content-Type: $mime");
            header("Content-Security-Policy: default-src 'none';");
            readfile($path);
        }
    }
    exit;
}

if ($action === "login") {
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5) {
        if (time() - $_SESSION['last_login_time'] < 300) {
            sleep(2); // Tarpit
            die("Too many attempts. Try again in 5 minutes.");
        } else {
            $_SESSION['login_attempts'] = 0;
        }
    }

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$_POST["username"]]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST["password"], $user["password"])) {
        session_regenerate_id(true); 
        $_SESSION['login_attempts'] = 0;
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $_POST["username"];
    } else {
        sleep(2); // Tarpit
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_login_time'] = time();
    }
    header("Location: " . $_SERVER["PHP_SELF"]); 
    exit;
}

if ($action === "update_settings") {
    $allowed_fields = ["api_key", "model", "provider", "local_url", "contact_email", "img_provider", "img_api_key"];
    foreach ($allowed_fields as $field) {
        if (isset($_POST[$field])) {
            $pdo->prepare("UPDATE settings SET value = ? WHERE key = ?")->execute([trim($_POST[$field]), $field]);
        }
    }
    
    if (isset($_POST["output_path"])) {
        $val = preg_replace("/[^a-zA-Z0-9_-]/", "", basename($_POST["output_path"]));
        $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'output_path'")->execute([$val]);
    }
    header("Location: ?page=settings"); 
    exit;
}

if ($action === "user_ops") {
    if ($_POST["sub"] === "add") {
        if (strlen($_POST["p"]) >= 8) {
            $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([
                $_POST["u"], 
                password_hash($_POST["p"], PASSWORD_DEFAULT)
            ]);
        }
    } elseif ($_POST["sub"] === "edit") {
        if (strlen($_POST["p"]) >= 8) {
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([
                password_hash($_POST["p"], PASSWORD_DEFAULT), 
                $_POST["id"]
            ]);
        }
    } elseif ($_POST["sub"] === "del" && $_POST["id"] != $_SESSION["user_id"]) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$_POST["id"]]);
    }
    header("Location: ?page=users"); 
    exit;
}

if ($action === "upload_img") {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0 && $_FILES["image"]["size"] <= 2 * 1024 * 1024) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
        finfo_close($finfo);
        
        $mimes_exts = [
            'image/jpeg' => 'jpg', 
            'image/png' => 'png', 
            'image/gif' => 'gif', 
            'image/webp' => 'webp'
        ];
        
        if (isset($mimes_exts[$mime])) {
            $ext = $mimes_exts[$mime];
            $base_name = preg_replace("/[^a-zA-Z0-9_-]/", "", pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME));
            if (empty($base_name)) {
                $base_name = "img";
            }
            
            $filename = $base_name . '.' . $ext;
            if (file_exists($safe_output_dir . "/" . $filename)) {
                $filename = $base_name . "_" . time() . '.' . $ext;
            }
            
            move_uploaded_file($_FILES["image"]["tmp_name"], $safe_output_dir . "/" . $filename);
        }
    }
    header("Location: ?page=gallery"); 
    exit;
}

if ($action === "del_img") {
    $file = basename($_POST["file"] ?? "");
    $path = $safe_output_dir . "/" . $file;
    if ($file && file_exists($path)) {
        unlink($path);
    }
    header("Location: ?page=gallery"); 
    exit;
}

// --- HELPER AI LOGIC ---

function call_ai($prompt, $system_instruction, $settings, $json_mode = true) {
    $headers = ["Content-Type: application/json"];
    
    if ($settings["provider"] === "google") {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$settings["model"]}:generateContent";
        if (!empty($settings["api_key"])) {
            $headers[] = "x-goog-api-key: " . $settings["api_key"];
        }
        $data = [
            "contents" => [
                ["parts" => [["text" => $system_instruction . "\n\n" . $prompt]]]
            ]
        ];
        if ($json_mode) {
            $data["generationConfig"] = ["response_mime_type" => "application/json"];
        }
    } else {
        $url = $settings["local_url"];
        $data = [
            "model" => $settings["model"], 
            "messages" => [
                ["role" => "system", "content" => $system_instruction], 
                ["role" => "user", "content" => $prompt]
            ]
        ];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_POST => true, 
        CURLOPT_TIMEOUT => 300, 
        CURLOPT_HTTPHEADER => $headers, 
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        return ["error" => curl_error($ch)];
    }
    
    $res_json = json_decode($res, true);
    if ($settings["provider"] === "google") {
        return ["text" => $res_json["candidates"][0]["content"]["parts"][0]["text"] ?? "", "raw" => $res];
    }
    return ["text" => $res_json["choices"][0]["message"]["content"] ?? "", "raw" => $res];
}

function get_current_code($safe_output_dir) {
    $context = ""; 
    $total_size = 0; 
    $max_total_size = 500000; 
    
    if (file_exists($safe_output_dir)) {
        foreach (scandir($safe_output_dir) as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ["html", "css", "js"])) {
                $path = $safe_output_dir . "/" . $file;
                $size = filesize($path);
                
                if ($size < 100000 && ($total_size + $size) < $max_total_size) {
                    $context .= "--- FILE: $file ---\n" . file_get_contents($path) . "\n\n";
                    $total_size += $size;
                }
            }
        }
    }
    return $context;
}

if ($action === "fetch_models") {
    header("Content-Type: application/json");
    if (empty($settings["api_key"])) {
        die(json_encode(["status" => "error", "message" => "Missing API Key."]));
    }
    
    $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models"); 
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_HTTPHEADER => ["x-goog-api-key: " . $settings["api_key"]]
    ]);
    
    $res = curl_exec($ch); 
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch);
    
    if ($http_code === 200) {
        $data = json_decode($res, true); 
        $models = [];
        if (isset($data["models"])) {
            foreach ($data["models"] as $m) {
                if (isset($m["supportedGenerationMethods"]) && in_array("generateContent", $m["supportedGenerationMethods"])) {
                    $models[] = str_replace("models/", "", $m["name"]);
                }
            }
            echo json_encode(["status" => "success", "models" => $models]);
        } else {
            echo json_encode(["status" => "error", "message" => "Unknown response format."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "HTTP $http_code", "raw" => $res]);
    }
    exit;
}

if ($action === "test_api") {
    header("Content-Type: application/json"); 
    ob_clean(); 
    
    $sys_instr = "Reply ONLY with word OK";
    $headers = ["Content-Type: application/json"];
    
    if ($settings["provider"] === "google") {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$settings["model"]}:generateContent";
        if (!empty($settings["api_key"])) {
            $headers[] = "x-goog-api-key: " . $settings["api_key"];
        }
        $post_data = [
            "contents" => [
                ["parts" => [["text" => $sys_instr]]]
            ]
        ];
    } else {
        $url = $settings["local_url"];
        $post_data = [
            "model" => $settings["model"], 
            "messages" => [["role" => "user", "content" => $sys_instr]]
        ];
    }
    
    $ch = curl_init($url); 
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_POST => true, 
        CURLOPT_HTTPHEADER => $headers, 
        CURLOPT_POSTFIELDS => json_encode($post_data)
    ]);
    
    $res = curl_exec($ch); 
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo json_encode([
        "status" => $http_code == 200 ? "success" : "error", 
        "http_code" => $http_code, 
        "raw" => $res ?: curl_error($ch)
    ]); 
    exit;
}

if ($action === "test_img_api") {
    header("Content-Type: application/json"); 
    ob_clean();
    
    $provider = $settings['img_provider'] ?? 'pollinations';
    $api_key = $settings['img_api_key'] ?? '';
    $prompt = "A simple red apple on white background, minimal vector icon";
    $stream = fopen('php://temp', 'w+');
    
    if (strpos($provider, 'openai') === 0) {
        if (empty($api_key)) {
            die(json_encode(["status" => "error", "message" => "Missing API Key."]));
        }
        $ch = curl_init("https://api.openai.com/v1/images/generations");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $api_key], 
            CURLOPT_POSTFIELDS => json_encode([
                "model" => "dall-e-2", 
                "prompt" => $prompt, 
                "n" => 1, 
                "size" => "256x256", 
                "response_format" => "b64_json"
            ]), 
            CURLOPT_TIMEOUT => 20, 
            CURLOPT_VERBOSE => true, 
            CURLOPT_STDERR => $stream
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $err = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code == 200) { 
            $json = json_decode($res, true); 
            if (isset($json['data'][0]['b64_json'])) { 
                $json['data'][0]['b64_json'] = "[BASE64_DATA_OK]"; 
                $res = json_encode($json, JSON_PRETTY_PRINT); 
            } 
        }
        
    } elseif ($provider === 'together') {
        if (empty($api_key)) {
            die(json_encode(["status" => "error", "message" => "Missing API Key."]));
        }
        $ch = curl_init("https://api.together.xyz/v1/images/generations");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $api_key], 
            CURLOPT_POSTFIELDS => json_encode([
                "model" => "black-forest-labs/FLUX.1-schnell", 
                "prompt" => $prompt, 
                "width" => 256, 
                "height" => 256, 
                "steps" => 1, 
                "n" => 1, 
                "response_format" => "b64_json"
            ]), 
            CURLOPT_TIMEOUT => 20, 
            CURLOPT_VERBOSE => true, 
            CURLOPT_STDERR => $stream
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $err = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code == 200) { 
            $json = json_decode($res, true); 
            if (isset($json['data'][0]['b64_json'])) { 
                $json['data'][0]['b64_json'] = "[BASE64_DATA_OK]"; 
                $res = json_encode($json, JSON_PRETTY_PRINT); 
            } 
        }
        
    } else {
        // Pollinations blocks strict clients, simple User-Agent required
        $ch = curl_init("https://image.pollinations.ai/prompt/" . urlencode($prompt) . "?nologo=true&width=256&height=256");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_FOLLOWLOCATION => true, 
            CURLOPT_USERAGENT => "Mozilla/5.0", 
            CURLOPT_TIMEOUT => 15, 
            CURLOPT_VERBOSE => true, 
            CURLOPT_STDERR => $stream
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $err = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code == 200) {
            $res = "OK (Binary data)";
        }
    }
    
    rewind($stream); 
    $verbose_log = stream_get_contents($stream); 
    fclose($stream);
    
    echo json_encode([
        "status" => $http_code == 200 ? "success" : "error", 
        "http_code" => $http_code, 
        "raw" => "HTTP Code: $http_code\ncURL Error: [$errno] $err\n\n--- API Response ---\n" . ($res ?: "Empty") . "\n\n--- cURL Verbose Log ---\n" . $verbose_log
    ]); 
    exit;
}

if ($action === "plan") {
    set_time_limit(300); 
    header("Content-Type: application/json"); 
    ob_clean();
    
    $prompt = $_POST["prompt"] ?? "";
    $current_code = get_current_code($safe_output_dir);
    
    $available_images = [];
    foreach (scandir($safe_output_dir) as $f) {
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif", "webp"])) {
            $available_images[] = $f;
        }
    }
    $img_context = empty($available_images) ? "" : "Available local images: " . implode(", ", $available_images) . ".";

    $sys_instr = "Jsi architekt webu. Analyzuj požadavek uživatele a aktuální kód. 
Tvým jediným úkolem je vytvořit PLÁN. Vrať striktně JSON objekt formátu:
{
  \"code_files\": [\"index.html\", \"style.css\"], // Soubory k úpravě/vytvoření
  \"images\": [{\"filename\": \"logo.jpg\", \"prompt\": \"Anglický popis obrázku pro AI generátor\"}] // Nové obrázky
}
Vrať POUZE tento JSON. Nevracej kód souborů. Nevytvářej backendové skripty (PHP). 
DŮLEŽITÉ: Obrázky musí mít VŽDY příponu .jpg nebo .png!
$img_context\n\nAKTUÁLNÍ KÓD:\n" . ($current_code ?: "Empty project.");

    $ai_res = call_ai("POŽADAVEK: " . $prompt, $sys_instr, $settings, true);
    
    if (isset($ai_res["error"])) {
        die(json_encode(["status" => "error", "message" => $ai_res["error"]]));
    }
    
    // Markdown JSON block cleaning
    $clean = str_replace(["`"."``json", "`"."``"], '', $ai_res["text"]);
    echo trim($clean) ?: json_encode(["status" => "error", "message" => "AI failed to return JSON plan."]); 
    exit;
}

if ($action === "build_file") {
    set_time_limit(300); 
    header("Content-Type: application/json"); 
    ob_clean();
    
    $target_file = basename($_POST["file"] ?? "unknown.txt");
    
    // --- SECURITY: RESTRICT ALLOWED FILE EXTENSIONS ---
    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($ext, ['html', 'css', 'js'])) {
        die(json_encode(["status" => "error", "message" => "Security block: Invalid file extension."]));
    }

    $prompt = $_POST["prompt"] ?? "";
    $current_code = get_current_code($safe_output_dir);
    
    $available_images = [];
    foreach (scandir($safe_output_dir) as $f) {
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif", "webp"])) {
            $available_images[] = $f;
        }
    }
    
    $img_context = empty($available_images) 
        ? "No local images are available yet." 
        : "YOU CAN USE THESE EXISTING IMAGES:\n" . implode(", ", $available_images) . "\n";
        
    $form_email = !empty($settings["contact_email"]) ? $settings["contact_email"] : "user@example.com";

    $sys_instr = "Jsi expert web developer. Nyní zpracováváš soubor: $target_file.
Požadavek uživatele: $prompt

PRAVIDLA:
1. $img_context
2. STRIKTNĚ použij pouze názvy obrázků z lokálního seznamu (např. src=\"logo.jpg\").
3. Fotky mimo lokální seznam vkládej jako: src=\"https://loremflickr.com/800/600/klicove_slovo\".
4. ABSOLUTNÍ ZÁKAZ používat pixabay.com, freepik.com.
5. Vždy připoj CSS soubor: <link rel=\"stylesheet\" href=\"style.css\">.
6. Kontaktní form: action=\"https://formsubmit.co/$form_email\" a method=\"POST\" (žádné PHP).

Vrať POUZE čistý kód. Žádný Markdown. Nic nevysvětluj.
\n\nAKTUÁLNÍ KÓD:\n" . ($current_code ?: "Empty project.");

    $ai_res = call_ai("Generate code for file: $target_file", $sys_instr, $settings, false);
    
    if (isset($ai_res["error"])) {
        die(json_encode(["status" => "error", "message" => $ai_res["error"]]));
    }
    
    $code = $ai_res["text"];
    
    // Robust parsing for possible markdown ticks returned by the model
    if (preg_match('/`'."``[a-z]*\n(.*?)\n`"."``/is", $code, $matches)) {
        $code = $matches[1];
    } else {
        $code = trim(preg_replace('/^`'."``[\w]*\n|\n`"."``$/", '', $code));
    }
    
    file_put_contents($safe_output_dir . "/" . $target_file, $code);
    
    echo json_encode([
        "status" => "success", 
        "file" => $target_file, 
        "size" => strlen($code)
    ]); 
    exit;
}

if ($action === "gen_image") {
    set_time_limit(300); 
    header("Content-Type: application/json"); 
    ob_clean();
    
    $prompt_raw = $_POST["prompt"] ?? "";
    $filename = preg_replace("/[^a-zA-Z0-9_.-]/", "", basename($_POST["filename"] ?? "img_" . time() . ".jpg"));
    
    $img_provider = $settings['img_provider'] ?? 'pollinations'; 
    $img_api_key = $settings['img_api_key'] ?? '';
    $img_data = false; 
    $err_msg = "";
    
    if (strpos($img_provider, 'openai') === 0 && !empty($img_api_key)) {
        $ch = curl_init("https://api.openai.com/v1/images/generations");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $img_api_key], 
            CURLOPT_POSTFIELDS => json_encode([
                "model" => ($img_provider === 'openai2' ? "dall-e-2" : "dall-e-3"), 
                "prompt" => $prompt_raw, 
                "n" => 1, 
                "size" => ($img_provider === 'openai2' ? "512x512" : "1024x1024"), 
                "response_format" => "b64_json"
            ]), 
            CURLOPT_TIMEOUT => 45
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $error = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code !== 200) {
            $err_msg = "OpenAI HTTP $http_code: " . ($res ?: $error);
        } else { 
            $json = json_decode($res, true); 
            if (isset($json['data'][0]['b64_json'])) {
                $img_data = base64_decode($json['data'][0]['b64_json']); 
            } else {
                $err_msg = "Missing b64_json."; 
            }
        }
        
    } elseif ($img_provider === 'together' && !empty($img_api_key)) {
        $ch = curl_init("https://api.together.xyz/v1/images/generations");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_POST => true, 
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $img_api_key], 
            CURLOPT_POSTFIELDS => json_encode([
                "model" => "black-forest-labs/FLUX.1-schnell", 
                "prompt" => $prompt_raw, 
                "width" => 1024, 
                "height" => 1024, 
                "steps" => 4, 
                "n" => 1, 
                "response_format" => "b64_json"
            ]), 
            CURLOPT_TIMEOUT => 45
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $error = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code !== 200) {
            $err_msg = "Together HTTP $http_code: " . ($res ?: $error);
        } else { 
            $json = json_decode($res, true); 
            if (isset($json['data'][0]['b64_json'])) {
                $img_data = base64_decode($json['data'][0]['b64_json']); 
            } else {
                $err_msg = "Missing b64_json."; 
            }
        }
        
    } else {
        $ch = curl_init("https://image.pollinations.ai/prompt/" . urlencode($prompt_raw) . "?nologo=true&width=1024&height=1024");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_FOLLOWLOCATION => true, 
            CURLOPT_USERAGENT => "Mozilla/5.0", 
            CURLOPT_TIMEOUT => 30
        ]);
        
        $res = curl_exec($ch); 
        $errno = curl_errno($ch); 
        $error = curl_error($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        
        if ($http_code == 200 && $res) {
            $img_data = $res; 
        } else {
            $err_msg = "Pollinations HTTP: $http_code. $error";
        }
    }

    if ($img_data) {
        file_put_contents($safe_output_dir . "/" . $filename, $img_data);
        echo json_encode(["status" => "success", "file" => $filename]);
    } else { 
        $ch_ph = curl_init("https://placehold.co/1024x1024/222/aaa.png?text=" . urlencode("AI Error\n" . basename($filename)));
        curl_setopt_array($ch_ph, [
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_FOLLOWLOCATION => true, 
            CURLOPT_USERAGENT => "Mozilla/5.0", 
            CURLOPT_TIMEOUT => 10
        ]);
        
        $placeholder = curl_exec($ch_ph); 
        $ph_http_code = curl_getinfo($ch_ph, CURLINFO_HTTP_CODE); 
        curl_close($ch_ph);
        
        if ($ph_http_code == 200 && $placeholder) {
            file_put_contents($safe_output_dir . "/" . $filename, $placeholder);
            echo json_encode(["status" => "warning", "file" => $filename, "message" => "Generator failed ($err_msg). Used placeholder."]);
        } else {
            file_put_contents($safe_output_dir . "/" . $filename, base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=="));
            echo json_encode(["status" => "error", "message" => "Generation error ($err_msg). Pixel created."]); 
        }
    } 
    exit;
}

if ($action === "save_log") {
    header("Content-Type: application/json"); 
    ob_clean();
    
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, prompt, files_changed) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $_POST["prompt"] ?? "", $_POST["files"] ?? ""]);
    $log_id = $pdo->lastInsertId();
    
    $stmt_ver = $pdo->prepare("INSERT INTO file_versions (log_id, filename, content) VALUES (?, ?, ?)");
    
    foreach (explode(", ", $_POST["files"] ?? "") as $fname) {
        $fname = basename(trim($fname)); 
        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        
        if (!in_array($ext, ['html', 'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            continue;
        }
        
        $path = $safe_output_dir . "/" . $fname;
        if (file_exists($path) && is_file($path)) {
            $content = in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"]) ? "[IMAGE_GENERATED]" : file_get_contents($path);
            $stmt_ver->execute([$log_id, $fname, $content]);
        }
    }
    
    $pdo->commit(); 
    echo json_encode(["status" => "success"]); 
    exit;
}

if ($action === "restore") {
    header("Content-Type: application/json"); 
    ob_clean();
    
    $log_id = (int)$_POST["log_id"];
    $stmt = $pdo->prepare("SELECT filename, content FROM file_versions WHERE log_id = ?");
    $stmt->execute([$log_id]); 
    $files = $stmt->fetchAll();
    
    if (!$files) {
        die(json_encode(["status" => "error", "message" => "Backup not found."]));
    }
    
    $restored = [];
    foreach ($files as $f) { 
        $fname = basename($f["filename"]); 
        file_put_contents($safe_output_dir . "/" . $fname, $f["content"]); 
        $restored[] = $fname; 
    }
    
    $pdo->prepare("INSERT INTO logs (user_id, prompt, files_changed) VALUES (?, ?, ?)")->execute([
        $_SESSION["user_id"], 
        "[RESTORE] Reverted to log ID " . $log_id, 
        implode(", ", $restored)
    ]);
    
    echo json_encode(["status" => "success", "files" => $restored]); 
    exit;
}

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>GodFile - AI CMS</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #cfcfcf; margin: 0; display: flex; height: 100vh; overflow: hidden; }
        nav { width: 80px; background: #1a1a1a; border-right: 1px solid #333; display: flex; flex-direction: column; align-items: center; padding-top: 20px; gap: 20px; }
        nav a { color: #888; text-decoration: none; font-size: 12px; text-align: center; writing-mode: vertical-rl; transform: rotate(180deg); padding: 20px 0; border-radius: 6px; }
        nav a:hover, nav a.active { color: #fff; background: #2a2a2a; }
        .lang-btn { writing-mode: horizontal-tb; transform: none; padding: 5px 0; font-size: 11px; width: 100%; display: block; border-radius: 4px; margin-bottom: 5px; }
        .layout { display: flex; flex: 1; width: 100%; }
        .tools-panel { width: 35%; max-width: 500px; padding: 20px; overflow-y: auto; background: #151515; border-right: 1px solid #333; display: flex; flex-direction: column; transition: all 0.3s; }
        .tools-panel.expanded { width: 100%; max-width: 100%; }
        .card { background: #1e1e1e; padding: 15px; border-radius: 8px; border: 1px solid #333; margin-bottom: 15px; }
        textarea { background: #000; border: 1px solid #444; color: #0f0; padding: 15px; width: 100%; box-sizing: border-box; font-family: monospace; font-size: 14px; border-radius: 6px; outline: none; resize: vertical; }
        textarea:focus { border-color: #3b82f6; }
        button { background: #3b82f6; border: none; font-weight: bold; cursor: pointer; padding: 12px; width: 100%; border-radius: 4px; margin-top: 10px; color: #fff; }
        button:hover { background: #2563eb; }
        button:disabled { background: #555; cursor: not-allowed; }
        .preview-panel { flex: 1; display: flex; flex-direction: column; background: #0a0a0a; transition: all 0.3s; }
        .preview-panel.hidden { display: none !important; }
        .preview-header { background: #1a1a1a; padding: 10px 20px; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; }
        .preview-header span { font-size: 12px; color: #888; }
        iframe { flex: 1; width: 100%; border: none; background: #fff; }
        #loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; flex-direction: column; padding: 30px; box-sizing: border-box; }
        #l-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        #l-timer { font-size: 1.5em; color: #10b981; font-family: monospace; font-weight: bold; }
        #l-console { flex: 1; background: #000; color: #0f0; font-family: monospace; font-size: 14px; padding: 15px; overflow-y: auto; border: 1px solid #333; border-radius: 6px; white-space: pre-wrap; line-height: 1.4; }
        #btn-close-overlay { background: #10b981; padding: 15px; font-size: 1.2em; display: none; margin-top: 15px; color: #fff; width: 100%; cursor: pointer; border: none; font-weight: bold; border-radius: 4px; }
        .log { font-size: 0.85em; border-bottom: 1px solid #333; padding: 12px 0; color: #aaa; position: relative; }
        .log-header { display: flex; justify-content: space-between; font-size: 0.85em; color: #888; margin-bottom: 5px; }
        .btn-restore { background: #4b5563; padding: 4px 8px; font-size: 0.8em; margin: 0; width: auto; color: #fff; border-radius: 4px; cursor: pointer; }
        input, select { background: #222; border: 1px solid #444; color: #fff; padding: 8px; width: 100%; box-sizing: border-box; margin-bottom: 10px;}
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        .gallery-item { background: #222; border: 1px solid #444; border-radius: 6px; padding: 10px; text-align: center; }
        .gallery-item img { max-width: 100%; height: 100px; object-fit: contain; margin-bottom: 10px; cursor: pointer; }
        .mini-gallery::-webkit-scrollbar { height: 6px; } 
        .mini-gallery::-webkit-scrollbar-track { background: #111; border-radius: 3px; } 
        .mini-gallery::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
    </style>
</head>
<body>

<div id="loading-overlay">
    <div id="l-header">
        <h2 style="margin:0; color:#fff;"><?= t('terminal') ?></h2>
        <div id="l-timer"><?= t('time') ?>: 0s</div>
    </div>
    <div id="l-console"></div>
    <button id="btn-close-overlay" onclick="reloadPreview()"><?= t('close_reload') ?></button>
</div>

<?php if ($action === "show_setup"): ?>
    <div style="margin: 100px auto; width: 300px;" class="card">
        <h2 style="color: #10b981;"><?= t('setup_title') ?></h2>
        <p style="font-size:13px;"><?= t('setup_desc') ?></p>
        <form method="POST">
            <input type="hidden" name="action" value="setup_admin">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="u" placeholder="<?= t('username') ?>" required>
            <input type="password" name="p" placeholder="<?= t('new_pw') ?> (min 8)" required minlength="8">
            <button type="submit"><?= t('save_pw') ?></button>
        </form>
    </div>

<?php elseif ($action === "show_login"): ?>
    <div style="margin: 100px auto; width: 300px;" class="card">
        <h2><?= t('login_title') ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input name="username" placeholder="<?= t('username') ?>" required>
            <input type="password" name="password" placeholder="<?= t('password') ?>" required>
            <button type="submit"><?= t('enter') ?></button>
        </form>
    </div>

<?php else: $page = $_GET["page"] ?? "chat"; ?>

    <nav>
        <a href="?" class="<?= $page=="chat"?"active":"" ?>"><?= t('nav_editor') ?></a>
        <a href="#" onclick="togglePreview(); return false;" id="btn-toggle-preview">O/C Preview</a>
        <a href="?page=gallery" class="<?= $page=="gallery"?"active":"" ?>"><?= t('nav_gallery') ?></a>
        <?php if($is_superadmin): ?>
            <a href="?page=users" class="<?= $page=="users"?"active":"" ?>"><?= t('nav_users') ?></a>
            <a href="?page=settings" class="<?= $page=="settings"?"active":"" ?>"><?= t('nav_settings') ?></a>
        <?php endif; ?>
        <a href="?action=logout&token=<?= $_SESSION['csrf_token'] ?>" style="color:#ef4444;"><?= t('nav_logout') ?></a>
        <div style="margin-top: auto; padding-bottom: 20px; width:100%;">
            <?php foreach (['en', 'zh', 'es', 'cs'] as $l): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['lang'=>$l])) ?>" class="lang-btn <?= $lang==$l?'active':'' ?>"><?= strtoupper($l) ?></a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="layout">
        <div class="tools-panel">
            <?php if ($page === "chat"): ?>
                <div class="card" style="border-color: #3b82f6;">
                    <h3 style="margin-top:0; color: #3b82f6;"><?= t('cmd_line') ?></h3>
                    <textarea id="prompt" rows="8" placeholder="<?= t('prompt_ph') ?>"></textarea>
                    
                    <?php
                    $mini_imgs = [];
                    foreach (scandir($safe_output_dir) as $f) {
                        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif", "webp"])) {
                            $mini_imgs[] = $f;
                        }
                    }
                    if (!empty($mini_imgs)):
                    ?>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333;">
                        <div style="font-size: 11px; color: #888; margin-bottom: 5px;"><?= t('insert_hint') ?></div>
                        <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 5px;" class="mini-gallery">
                            <?php foreach ($mini_imgs as $img): ?>
                                <img src="?action=view_img&file=<?= urlencode($img) ?>" title="<?= htmlspecialchars($img) ?>" style="height: 40px; cursor: pointer; border-radius: 4px; border: 1px solid #444;" onclick="insertImageToPrompt('<?= htmlspecialchars($img) ?>')">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <button onclick="orchestrate()"><?= t('execute') ?></button>
                </div>
                
                <div class="card">
                    <h3 style="margin-top:0;"><?= t('history') ?></h3>
                    <?php
                    $logs = $pdo->query("SELECT logs.*, users.username FROM logs JOIN users ON logs.user_id = users.id ORDER BY timestamp DESC LIMIT 20");
                    while ($l = $logs->fetch()) {
                        $has_backup = $pdo->query("SELECT COUNT(*) FROM file_versions WHERE log_id = " . $l['id'])->fetchColumn() > 0;
                        echo "<div class='log'>";
                        echo "<div class='log-header'><strong>{$l['timestamp']} - {$l['username']}</strong>";
                        if ($has_backup) {
                            echo "<button class='btn-restore' onclick='restore({$l['id']})'>".t('restore')."</button>";
                        }
                        echo "</div>";
                        echo "<div>".t('changed')." " . htmlspecialchars($l['files_changed']) . "</div>";
                        echo "<i>" . nl2br(htmlspecialchars($l['prompt'])) . "</i>";
                        echo "</div>";
                    }
                    ?>
                </div>

            <?php elseif ($page === "gallery"): ?>
                <div class="card">
                    <h2><?= t('upload_title') ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_img">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="file" name="image" accept="image/png, image/jpeg, image/gif, image/webp" required>
                        <button type="submit" style="width: auto;"><?= t('upload_btn') ?></button>
                    </form>
                </div>
                <div class="card">
                    <h2><?= t('avail_img') ?></h2>
                    <p style="font-size: 13px; color: #aaa;"><?= t('copy_hint') ?></p>
                    <div class="gallery">
                        <?php
                        $imgs = [];
                        foreach (scandir($safe_output_dir) as $f) {
                            if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif", "webp"])) {
                                $imgs[] = $f;
                            }
                        }
                        if (empty($imgs)) echo "<p>".t('no_img')."</p>";
                        foreach ($imgs as $img): ?>
                            <div class="gallery-item">
                                <img src="?action=view_img&file=<?= urlencode($img) ?>" onclick="copyImgName('<?= htmlspecialchars($img) ?>')">
                                <div style="word-break: break-all; font-size: 0.8em; margin-bottom: 5px;"><?= htmlspecialchars($img) ?></div>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="del_img">
                                    <input type="hidden" name="file" value="<?= htmlspecialchars($img) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" style="background:none; border:none; color:#ef4444; padding:0; font-size:0.8em; cursor:pointer;" onclick="return confirm('<?= t('js_confirm') ?>')">[<?= t('delete') ?>]</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ($page === "settings" && $is_superadmin): ?>
                <div class="card">
                    <h2><?= t('config') ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_settings">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <label><?= t('provider') ?></label>
                        <select name="provider">
                            <option value="google" <?= $settings["provider"]=="google"?"selected":"" ?>>Google Gemini</option>
                            <option value="local" <?= $settings["provider"]=="local"?"selected":"" ?>>Local</option>
                        </select>
                        <label><?= t('folder') ?></label><input name="output_path" value="<?= htmlspecialchars($settings["output_path"]) ?>">
                        <label><?= t('api_key') ?></label><input type="password" name="api_key" value="<?= htmlspecialchars($settings["api_key"]) ?>">
                        <label><?= t('model') ?></label>
                        <div style="display: flex; gap: 10px;">
                            <select name="model" id="model-select" style="flex: 1;">
                                <optgroup label="Selected"><option value="<?= htmlspecialchars($settings["model"]) ?>" selected><?= htmlspecialchars($settings["model"]) ?></option></optgroup>
                                <optgroup label="Google API" id="google-models-group"></optgroup>
                                <optgroup label="Local"><option value="llama3">Llama 3</option></optgroup>
                            </select>
                            <button type="button" onclick="fetchModels()" id="btn-fetch-models" style="width: auto; margin-top: 0;"><?= t('fetch') ?></button>
                        </div>
                        <label><?= t('local_url') ?></label><input name="local_url" value="<?= htmlspecialchars($settings["local_url"]) ?>">
                        <label><?= t('email') ?></label><input type="email" name="contact_email" value="<?= htmlspecialchars($settings["contact_email"] ?? "") ?>">
                        
                        <hr style="border: 0; border-top: 1px solid #333; margin: 20px 0;">
                        <label><?= t('img_provider') ?></label>
                        <select name="img_provider">
                            <option value="pollinations" <?= ($settings["img_provider"] ?? "pollinations")=="pollinations"?"selected":"" ?>>Pollinations.ai (Free)</option>
                            <option value="openai" <?= ($settings["img_provider"] ?? "")=="openai"?"selected":"" ?>>OpenAI (DALL-E 3)</option>
                            <option value="openai2" <?= ($settings["img_provider"] ?? "")=="openai2"?"selected":"" ?>>OpenAI (DALL-E 2)</option>
                            <option value="together" <?= ($settings["img_provider"] ?? "")=="together"?"selected":"" ?>>Together AI (FLUX.1 Schnell)</option>
                        </select>
                        <label><?= t('img_api_key') ?></label><input type="password" name="img_api_key" value="<?= htmlspecialchars($settings["img_api_key"] ?? "") ?>">
                        <button type="submit"><?= t('save') ?></button>
                    </form>
                </div>
                <div class="card">
                    <h2><?= t('test_api') ?></h2>
                    <button onclick="testApi()" style="background: #10b981;"><?= t('run_test') ?></button>
                    <div id="test-result" style="display:none; margin-top: 15px;"><pre id="test-raw" style="background:#000; color:#0f0; padding:15px; border:1px solid #444; overflow:auto;"></pre></div>
                </div>
                <div class="card">
                    <h2><?= t('test_img_api') ?></h2>
                    <button onclick="testImgApi()" style="background: #10b981;"><?= t('run_test') ?></button>
                    <div id="test-img-result" style="display:none; margin-top: 15px;"><pre id="test-img-raw" style="background:#000; color:#0f0; padding:15px; border:1px solid #444; overflow:auto;"></pre></div>
                </div>

            <?php elseif ($page === "users" && $is_superadmin): ?>
                <div class="card">
                    <h2><?= t('manage_users') ?></h2>
                    <?php foreach ($pdo->query("SELECT * FROM users")->fetchAll() as $u): ?>
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <span style="width:100px;"><?= htmlspecialchars($u["username"]) ?></span>
                            <form method="POST" style="display:flex; gap:5px; flex:1;">
                                <input type="hidden" name="action" value="user_ops">
                                <input type="hidden" name="id" value="<?= $u["id"] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="password" name="p" placeholder="<?= t('new_pass') ?>" style="width:100px; margin:0;">
                                <button name="sub" value="edit" style="margin:0; width:auto;"><?= t('change') ?></button>
                                <?php if($u["id"] != 1): ?>
                                    <button name="sub" value="del" style="margin:0; width:auto; background:#ef4444;"><?= t('delete') ?></button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <form method="POST" style="display:flex; gap:10px; margin-top: 10px;">
                        <input type="hidden" name="action" value="user_ops">
                        <input type="hidden" name="sub" value="add">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input name="u" placeholder="<?= t('new_name') ?>" required style="width:150px; margin:0;">
                        <input type="password" name="p" placeholder="<?= t('password') ?>" required style="width:150px; margin:0;">
                        <button type="submit" style="margin:0; width:auto;"><?= t('add') ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="preview-panel">
            <div class="preview-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <label style="color: #10b981; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" id="toggle-tracking" checked onchange="toggleTracking()"> <?= t('tracking') ?>
                    </label>
                    <span>/<?= htmlspecialchars($web_path) ?>/index.html</span>
                </div>
                <a href="<?= htmlspecialchars($web_path) ?>/" target="_blank" style="color: #3b82f6; font-size: 13px; text-decoration: none;"><?= t('open_web') ?></a>
            </div>
            <!-- Removed allow-same-origin for sandbox security -->
            <iframe id="live-preview" sandbox="allow-scripts allow-forms" src="?action=preview_site&file=index.html&t=<?= time() ?>"></iframe>
        </div>

    </div>

    <script>
        const CSRF_TOKEN = "<?= $_SESSION['csrf_token'] ?>";

        function insertImageToPrompt(filename) {
            const p = document.getElementById("prompt");
            const insertText = `[obrázek: ${filename}] `;
            const startPos = p.selectionStart; 
            const endPos = p.selectionEnd;
            p.value = p.value.substring(0, startPos) + insertText + p.value.substring(endPos, p.value.length);
            p.focus(); 
            p.selectionStart = startPos + insertText.length; 
            p.selectionEnd = startPos + insertText.length;
        }

        function copyImgName(name) { 
            navigator.clipboard.writeText(name).then(() => { 
                alert('<?= t('js_copied') ?>' + name); 
            }); 
        }

        function toggleTracking() {
            const isChecked = document.getElementById('toggle-tracking').checked;
            const iframe = document.getElementById('live-preview');
            let filename = 'index.html';
            
            try {
                const currentUrl = new URL(iframe.contentWindow.location.href);
                if (currentUrl.searchParams.has('file')) {
                    filename = currentUrl.searchParams.get('file');
                } else { 
                    const parts = currentUrl.pathname.split('/'); 
                    const lastPart = parts[parts.length - 1]; 
                    if (lastPart && lastPart.includes('.')) {
                        filename = lastPart; 
                    }
                }
            } catch(e) {}
            
            const t = new Date().getTime();
            if (isChecked) {
                iframe.src = "?action=preview_site&file=" + filename + "&t=" + t;
            } else {
                iframe.src = "<?= htmlspecialchars($web_path) ?>/" + filename + "?t=" + t;
            }
        }

        function togglePreview() {
            const previewPanel = document.querySelector('.preview-panel');
            const toolsPanel = document.querySelector('.tools-panel');
            previewPanel.classList.toggle('hidden'); 
            toolsPanel.classList.toggle('expanded');
        }

        window.addEventListener("message", (event) => {
            if (event.data && event.data.type === "element_selected" && event.data.token === CSRF_TOKEN) {
                const p = document.getElementById("prompt");
                let desc = event.data.text ? ` (${event.data.text})` : '';
                const hint = `Uprav [${event.data.selector}]${desc}:\n`;
                p.value = hint + p.value.replace(/^Uprav \[.*?\](\s\(.*?\))?:\s*\n/, '');
                p.focus();
            }
        });

        function reloadPreview() { 
            document.getElementById("live-preview").src = document.getElementById("live-preview").src; 
            document.getElementById("loading-overlay").style.display = "none"; 
        }

        let timerInt;
        
        function appendToConsole(text, color = "#0f0") {
            const cons = document.getElementById("l-console");
            const span = document.createElement("span"); 
            span.style.color = color; 
            span.innerText = text + "\n";
            cons.appendChild(span); 
            cons.scrollTop = cons.scrollHeight;
        }

        async function fetchJSON(action, dataObj) {
            const fd = new FormData(); 
            fd.append("action", action); 
            fd.append("csrf_token", CSRF_TOKEN);
            
            for (const key in dataObj) {
                fd.append(key, dataObj[key]);
            }
            
            const res = await fetch("?", {method: "POST", body: fd});
            const text = await res.text();
            
            try { 
                return JSON.parse(text); 
            } catch (e) { 
                if (text.toLowerCase().includes('504') || text.toLowerCase().includes('timeout')) {
                    throw new Error("Timeout 504: Host spojení ukončil, ale kód vzadu stále běží. Počkej 20 sekund a obnov stránku.");
                }
                throw new Error("Chyba serveru:\n" + text.substring(0, 200)); 
            }
        }
        
        const delay = ms => new Promise(res => setTimeout(res, ms));

        async function orchestrate() {
            const p = document.getElementById("prompt").value.trim();
            if (!p) return;

            document.getElementById("loading-overlay").style.display = "flex";
            document.getElementById("l-console").innerHTML = ""; 
            document.getElementById("btn-close-overlay").style.display = "none";
            
            appendToConsole("<?= t('js_start') ?>", "#3b82f6"); 
            appendToConsole("Prompt: " + p, "#fff");

            let time = 0; 
            document.getElementById("l-timer").innerText = `<?= t('time') ?>: 0s`;
            timerInt = setInterval(() => { 
                time++; 
                document.getElementById("l-timer").innerText = `<?= t('time') ?>: ${time}s`; 
            }, 1000);

            try {
                appendToConsole("\n<?= t('js_plan') ?>", "#fbbf24");
                const plan = await fetchJSON("plan", { prompt: p });
                if (plan.status === "error") {
                    throw new Error(plan.message);
                }
                
                let codeFiles = plan.code_files || []; 
                let images = plan.images || []; 
                let allMod = [];

                if (codeFiles.length > 0 || images.length > 0) {
                    for (let img of images) {
                        appendToConsole(`\n<?= t('js_img') ?> ${img.filename}`, "#a855f7");
                        try {
                            const res = await fetchJSON("gen_image", { prompt: img.prompt, filename: img.filename });
                            if (res.status === "success" || res.status === "warning") { 
                                appendToConsole("-> OK", "#10b981"); 
                                allMod.push(img.filename); 
                            } else {
                                appendToConsole("-> " + (res.message || "Chyba"), "#ef4444");
                            }
                        } catch(e) { 
                            appendToConsole("-> " + e.message, "#ef4444"); 
                        }
                        await delay(2000); 
                    }

                    for (let file of codeFiles) {
                        appendToConsole(`\n<?= t('js_code') ?> ${file}`, "#3b82f6");
                        try {
                            const res = await fetchJSON("build_file", { prompt: p, file: file });
                            if (res.status === "success") { 
                                appendToConsole(`-> OK.`, "#10b981"); 
                                allMod.push(file); 
                            } else {
                                appendToConsole("-> <?= t('js_err_api') ?> " + (res.message || "Chyba"), "#ef4444");
                            }
                        } catch(e) { 
                            appendToConsole("-> " + e.message, "#ef4444"); 
                        }
                        await delay(3000); 
                    }

                    if (allMod.length > 0) {
                        await fetchJSON("save_log", { prompt: p, files: allMod.join(", ") });
                        appendToConsole("\n<?= t('js_done') ?>", "#10b981");
                    }
                }
            } catch (err) {
                appendToConsole("\n--- <?= t('js_err') ?> ---", "#ef4444"); 
                appendToConsole(err.message, "#ef4444");
            }
            
            clearInterval(timerInt);
            document.getElementById("btn-close-overlay").style.display = "block"; 
            document.getElementById("prompt").value = "";
        }

        async function restore(logId) {
            if (!confirm("<?= t('js_confirm') ?>")) return;
            
            const res = await fetchJSON("restore", { log_id: logId });
            if (res.status === "success") {
                location.reload(); 
            } else {
                alert("Chyba: " + res.message);
            }
        }

        async function fetchModels() {
            const btn = document.getElementById("btn-fetch-models"); 
            const group = document.getElementById("google-models-group");
            btn.innerText = "...";
            
            const res = await fetchJSON("fetch_models", {});
            if (res.status === "success") {
                group.innerHTML = ""; 
                res.models.forEach(m => { 
                    const opt = document.createElement("option"); 
                    opt.value = m; 
                    opt.innerText = m; 
                    group.appendChild(opt); 
                });
                btn.innerText = "OK";
            } else { 
                alert("Chyba: " + res.message); 
                btn.innerText = "Error"; 
            }
        }

        async function testApi() {
            document.getElementById("test-result").style.display = "block"; 
            document.getElementById("test-raw").innerText = "Testuji...";
            const res = await fetchJSON("test_api", {}); 
            document.getElementById("test-raw").innerText = "HTTP: " + res.http_code + "\n\n" + res.raw;
        }
        
        async function testImgApi() {
            document.getElementById("test-img-result").style.display = "block"; 
            document.getElementById("test-img-raw").innerText = "Testuji...";
            const res = await fetchJSON("test_img_api", {}); 
            document.getElementById("test-img-raw").innerText = "HTTP Status: " + res.http_code + "\n\n" + res.raw;
        }
    </script>
<?php endif; ?>
</body>
</html>
