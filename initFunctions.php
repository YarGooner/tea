<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.10.2018
 * Time: 14:17
 */

function getFileList($root, $basePath = '')
{
    $files = [];
    $handle = opendir($root);
    while (($path = readdir($handle)) !== false) {
        if ($path === '.git' || $path === '.svn' || $path === '.' || $path === '..') {
            continue;
        }
        $fullPath = "$root/$path";
        $relativePath = $basePath === '' ? $path : "$basePath/$path";
        if (is_dir($fullPath)) {
            $files = array_merge($files, getFileList($fullPath, $relativePath));
        } else {
            $files[] = $relativePath;
        }
    }
    closedir($handle);
    return $files;
}

function copyFile($root, $source, $target, &$all, $params)
{
    if (!is_file($root . '/' . $source)) {
        echo "       skip $target ($source not exist)\n";
        return true;
    }
    if (is_file($root . '/' . $target)) {
        if (file_get_contents($root . '/' . $source) === file_get_contents($root . '/' . $target)) {
            echo "  unchanged $target\n";
            return true;
        }
        if ($all) {
            echo "  overwrite $target\n";
        } else {
            echo "      exist $target\n";
            echo "            ...overwrite? [Yes|No|All|Quit] ";


            $answer = !empty($params['overwrite']) ? $params['overwrite'] : trim(fgets(STDIN));
            if (!strncasecmp($answer, 'q', 1)) {
                return false;
            } else {
                if (!strncasecmp($answer, 'y', 1)) {
                    echo "  overwrite $target\n";
                } else {
                    if (!strncasecmp($answer, 'a', 1)) {
                        echo "  overwrite $target\n";
                        $all = true;
                    } else {
                        echo "       skip $target\n";
                        return true;
                    }
                }
            }
        }
        file_put_contents($root . '/' . $target, file_get_contents($root . '/' . $source));
        return true;
    }
    echo "   generate $target\n";
    @mkdir(dirname($root . '/' . $target), 0777, true);
    file_put_contents($root . '/' . $target, file_get_contents($root . '/' . $source));
    return true;
}

function getParams()
{
    $rawParams = [];
    if (isset($_SERVER['argv'])) {
        $rawParams = $_SERVER['argv'];
        array_shift($rawParams);
    }

    $params = [];
    foreach ($rawParams as $param) {
        if (preg_match('/^--(\w+)(=(.*))?$/', $param, $matches)) {
            $name = $matches[1];
            $params[$name] = isset($matches[3]) ? $matches[3] : true;
        } else {
            $params[] = $param;
        }
    }
    return $params;
}

function setWritable($root, $paths)
{
    foreach ($paths as $writable) {
        if (is_dir("$root/$writable")) {
            if (@chmod("$root/$writable", 0777)) {
                echo "      chmod 0777 $writable\n";
            } else {
                printError("Operation chmod not permitted for directory $writable.");
            }
        } else {
            printError("Directory $writable does not exist.");
        }
    }
}

function setExecutable($root, $paths)
{
    foreach ($paths as $executable) {
        if (file_exists("$root/$executable")) {
            if (@chmod("$root/$executable", 0755)) {
                echo "      chmod 0755 $executable\n";
            } else {
                printError("Operation chmod not permitted for $executable.");
            }
        } else {
            printError("$executable does not exist.");
        }
    }
}

function setCookieValidationKey($root, $paths)
{
    foreach ($paths as $file) {
        echo "   generate cookie validation key in $file\n";
        $file = $root . '/' . $file;
        $length = 32;
        $bytes = openssl_random_pseudo_bytes($length);
        $key = strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
        $content = preg_replace('/(("|\')cookieValidationKey("|\')\s*=>\s*)(""|\'\')/', "\\1'$key'", file_get_contents($file));
        file_put_contents($file, $content);
    }
}

function createSymlink($root, $links)
{
    foreach ($links as $link => $target) {
        //first removing folders to avoid errors if the folder already exists
        @rmdir($root . "/" . $link);
        //next removing existing symlink in order to update the target
        if (is_link($root . "/" . $link)) {
            @unlink($root . "/" . $link);
        }
        if (@symlink($root . "/" . $target, $root . "/" . $link)) {
            echo "      symlink $root/$target $root/$link\n";
        } else {
            printError("Cannot create symlink $root/$target $root/$link.");
        }
    }
}

/**
 * Prints error message.
 * @param string $message message
 */
function printError($message)
{
    echo "\n  " . formatMessage("Error. $message", ['fg-red']) . " \n";
}

/**
 * Returns true if the stream supports colorization. ANSI colors are disabled if not supported by the stream.
 *
 * - windows without ansicon
 * - not tty consoles
 *
 * @return boolean true if the stream supports ANSI colors, otherwise false.
 */
function ansiColorsSupported()
{
    return DIRECTORY_SEPARATOR === '\\'
        ? getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON'
        : function_exists('posix_isatty') && @posix_isatty(STDOUT);
}

/**
 * Get ANSI code of style.
 * @param string $name style name
 * @return integer ANSI code of style.
 */
function getStyleCode($name)
{
    $styles = [
        'bold' => 1,
        'fg-black' => 30,
        'fg-red' => 31,
        'fg-green' => 32,
        'fg-yellow' => 33,
        'fg-blue' => 34,
        'fg-magenta' => 35,
        'fg-cyan' => 36,
        'fg-white' => 37,
        'bg-black' => 40,
        'bg-red' => 41,
        'bg-green' => 42,
        'bg-yellow' => 43,
        'bg-blue' => 44,
        'bg-magenta' => 45,
        'bg-cyan' => 46,
        'bg-white' => 47,
    ];
    return $styles[$name];
}

/**
 * Formats message using styles if STDOUT supports it.
 * @param string $message message
 * @param string[] $styles styles
 * @return string formatted message.
 */
function formatMessage($message, $styles)
{
    if (empty($styles) || !ansiColorsSupported()) {
        return $message;
    }

    return sprintf("\x1b[%sm", implode(';', array_map('getStyleCode', $styles))) . $message . "\x1b[0m";
}
//get configuration file
function getJson(){
    $path = chooseJson();
    $json = file_get_contents($path);
    $json = json_decode($json, true);
    return $json;
}
//choose configuration file
function chooseJson() {
    $path = 'init_config';
    if (file_exists($path)) {
        if (is_dir($path)) {
            $files = [];
            foreach (scandir($path) as $item) {
                $ext = new SplFileInfo($item);
                $ext = $ext->getExtension();
                if ($ext == 'json') {
                    $files[] = $item;
                }
            }
            if (!$files) {
                echo 'Path does not contain any JSON files' . PHP_EOL;
                exit;
            }
            $select = select('Select configuration file', $files);
            if (confirm('Confirm selected file [' . $files[$select] . ']')) {
                $path .= '/' . $files[$select];
                return $path;
            } else {
                exit;
            }
        }
    }
}
//initialize environment
function initEnv($environment){
    switch ($environment){
        case 'dev': $environment = 'Development';
            break;
        case 'prod': $environment = 'Production';
            break;
    }
    $params = getParams();
    $root = str_replace('\\', '/', __DIR__);
    $envs = require "$root/environments/index.php";
    $envNames = array_keys($envs);
    $envName = null;

    $env = $envs[$environment];

    $files = getFileList("$root/environments/{$env['path']}");
    if (isset($env['skipFiles'])) {
        $skipFiles = $env['skipFiles'];
        array_walk($skipFiles, function(&$value) use($env, $root) { $value = "$root/$value"; });
        $files = array_diff($files, array_intersect_key($env['skipFiles'], array_filter($skipFiles, 'file_exists')));
    }
    $all = false;
    foreach ($files as $file) {
        if (!copyFile($root, "environments/{$env['path']}/$file", $file, $all, $params)) {
            break;
        }
    }

    $callbacks = ['setCookieValidationKey', 'setWritable', 'setExecutable', 'createSymlink'];
    foreach ($callbacks as $callback) {
        if (!empty($env[$callback])) {
            $callback($root, $env[$callback]);
        }
    }
}
//initials db parameters in 'common/main-local.php'
function initDatabase($db)
{
    $config = file_get_contents('common/config/main-local.php');

    $db_params = new stdClass;
    getDBParams($db_params);

    $delta = strlen($db['host'])-strlen($db_params->host);
    if($db['host']) {
        $config = substr_replace($config, $db['host'], $db_params->host_start_pos, strlen($db_params->host));
    }

    if($db['name']) {
        $config = substr_replace($config, $db['name'], $db_params->name_start_pos+$delta, strlen($db_params->name));
        $delta += strlen($db['name'])-strlen($db_params->name);
    } else $db['name'] = $db_params->name;

    if($db['username']) {
        $config = substr_replace($config, $db['username'], $db_params->username_start_pos+$delta, strlen($db_params->username));
        $delta += strlen($db['username'])-strlen($db_params->username);
    } else $db['username'] = $db_params->username;

    if($db['password']) {
        $config = substr_replace($config, $db['password'], $db_params->password_start_pos+$delta, strlen($db_params->password));
    } else $db['password'] = $db_params->password;

    file_put_contents('common/config/main-local.php', $config);
    return $db;
}
//unZip archive with user files and db dump
function initContent($file){
    $path = 'console/data';
    $destinationPath = '/htdocs/uploads/global';
    $zip = new ZipArchive();
    $zip->open($path.'\\'.$file);
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $item = $zip->getNameIndex($i);
        $zipExt = new SplFileInfo($item);
        $zipExt = $zipExt->getExtension();
        if($zipExt != 'sql'){
            $zip->extractTo($destinationPath,$item);
        } else {
            $zip->extractTo($path,$item);
        }
    }
    $zip->close();
}
//create new database and user for it
function createDB($cred,$params){
    $root = $cred['username'];
    $root_password = $cred['password'];

    $user = $params['username'];
    $pass = $params['password'];
    $db = $params['name'];
    $host = $cred['host'];

    try {
        $dbh = new PDO("mysql:host=$host", $root, $root_password);

        $dbh->exec("CREATE DATABASE `$db`;
            CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
            GRANT ALL ON `$db`.* TO '$user'@'localhost';
            FLUSH PRIVILEGES;")
        or die(print_r($dbh->errorInfo(), true));
        $cred['name'] = $params['name'];
        $cred = initDatabase($cred);
        return $cred;

    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
}
//create new user of application
function createUser($db_params,$cred){
    $host = $db_params['host'];
    $db_name = $db_params['name'];
    $user = $db_params['username'];
    $pass = $db_params['password'];
    $table = $cred['tablename'];
    $dbh = new PDO("mysql:host=$host;dbname=$db_name", $user, $pass);

    $username = $cred['username'];
    $password = $cred['password'];
    $auth_key = generateRandomString(32);
    $password_hash = password_hash($password,PASSWORD_DEFAULT,['cost' => 13]);
    $email = $cred['email'];
    $created_at = time();

    if(!$dbh->query('SELECT * FROM '.$table)){
        createUserTable($table,$dbh);
    }
    $insert = 'INSERT INTO '.$table.' VALUES (NULL, "'.$username.'","'.$auth_key.'","'.$password_hash.'",NULL, "'.$email.'", "10","'.$created_at.'","'.$created_at.'")';
    $dbh->exec($insert);
}
//create "user" table if doesn't exist
function createUserTable($name, $connection){
    $sql = "CREATE TABLE ".$name." ( 
              `id` INT NOT NULL AUTO_INCREMENT, 
              `username` varchar(255) NOT NULL,
              `auth_key` varchar(32) NOT NULL,
              `password_hash` varchar(255) NOT NULL,
              `password_reset_token` varchar(255) DEFAULT NULL,
              `email` varchar(255) NOT NULL,
              `status` smallint(6) NOT NULL DEFAULT '10',
              `created_at` int(11) NOT NULL,
              `updated_at` int(11) NOT NULL, PRIMARY KEY (`id`)) ";
    if(!$connection->exec($sql)){
        var_dump($connection->errorInfo());
    }
}

function generatePasswordHash($password, $cost = 13)
{

    if (function_exists('password_hash')) {
        /* @noinspection PhpUndefinedConstantInspection */
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    $salt = $this->generateSalt($cost);
    echo "Salt:";
    $hash = crypt($password, $salt);
    // strlen() is safe since crypt() returns only ascii
    if (!is_string($hash) || strlen($hash) !== 60) {
        throw new Exception('Unknown error occurred while generating hash.');
    }

    return $hash;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateSalt($cost = 13)
{
    $cost = (int) $cost;
    if ($cost < 4 || $cost > 31) {
        throw new InvalidArgumentException('Cost must be between 4 and 31.');
    }

    // Get a 20-byte random string
    $rand = generateRandomString(20);
    // Form the prefix that specifies Blowfish (bcrypt) algorithm and cost parameter.
    $salt = sprintf('$2y$%02d$', $cost);
    // Append the random salt data in the required base64 format.
    $salt .= str_replace('+', '.', substr(base64_encode($rand), 0, 22));

    return $salt;
}
//import data from .sql file to db
function importDump($file) {
    $path = 'console/data/'.$file;
    $db_params = new stdClass;
    getDBParams($db_params);

    if (!$db_params->name) {
        echo 'DB component not configured' . PHP_EOL;
        exit;
    }
    if(exec('mysql --host=' . $db_params->host . ' --user=' . $db_params->username . ' --password=' . $db_params->password . ' ' . $db_params->name . ' < ' . $path)){
        deleteDump($path);
    };
}
//delete .sql-file
function deleteDump($path) {
    if (file_exists($path)) {
        unlink($path);
    }
}

function select($prompt, $options = [])
{
    top:
    foreach ($options as $key => $value) {
        echo "[".$key."] - " . $value . "\r\n";
    }
    echo("$prompt [" . implode(',', array_keys($options)) . ',?]: ');
    $input = trim(fgets(STDIN));
    if ($input === '?') {
        foreach ($options as $key => $value) {
            echo(" $key - $value");
        }
        echo(' ? - Show help');
        goto top;
    } elseif (!array_key_exists($input, $options)) {
        goto top;
    }

    return $input;
}

function confirm($message, $default = false)
{
    while (true) {
        echo($message . ' (yes|no) [' . ($default ? 'yes' : 'no') . ']:');
        $input = trim(fgets(STDIN));

        if (empty($input)) {
            return $default;
        }

        if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
            return true;
        }

        if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
            return false;
        }
    }
}

function getDsnAttribute($name, $dsn)
{
    if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
        return $match[1];
    } else {
        return null;
    }
}

function getDBParams($db_params=null){
    $config = file_get_contents('common/config/main-local.php');

    $db_params->name_start_pos = stripos($config,'dbname=')+7;
    $db_params->name_end_pos = stripos(substr($config,$db_params->name_start_pos),'\'');
    $db_params->name = substr(substr($config,$db_params->name_start_pos),0,$db_params->name_end_pos);

    $db_params->host_start_pos = stripos($config,'host=')+5;
    $db_params->host_end_pos = stripos(substr($config,$db_params->host_start_pos),';');
    $db_params->host = substr(substr($config,$db_params->host_start_pos),0,$db_params->host_end_pos);

    $db_params->username_start_pos = stripos($config,'username')+14;
    $db_params->username_end_pos = stripos(substr($config,$db_params->username_start_pos),'\'');
    $db_params->username = substr(substr($config,$db_params->username_start_pos),0,$db_params->username_end_pos);

    $db_params->password_start_pos = stripos($config,'password')+14;
    $db_params->password_end_pos = stripos(substr($config,$db_params->password_start_pos),'\'');
    $db_params->password = substr(substr($config,$db_params->password_start_pos),0,$db_params->password_end_pos);

    return $db_params;
}

function readline($prompt = null){
    if($prompt){
        echo $prompt;
    }
    $fp = fopen("php://stdin","r");
    $line = rtrim(fgets($fp, 1024));
    return $line;
}

function getDB(){
    $json = getJson();
    $db = $json->db[0];
    return $db;
}



