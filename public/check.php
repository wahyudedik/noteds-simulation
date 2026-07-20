<?php

header('Content-Type: text/plain');

echo "=== System Info ===\n";
echo 'Current PHP User (whoami): '.trim(exec('whoami'))."\n";
echo 'PHP posix_getpwuid User: '.(function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'N/A')."\n";

echo "\n=== Directories Status ===\n";
$baseDir = dirname(__DIR__);
$paths = [
    'storage' => $baseDir.'/storage',
    'storage/framework' => $baseDir.'/storage/framework',
    'storage/framework/views' => $baseDir.'/storage/framework/views',
    'bootstrap/cache' => $baseDir.'/bootstrap/cache',
];

foreach ($paths as $name => $path) {
    if (! file_exists($path)) {
        echo "$name: DOES NOT EXIST!\n";

        continue;
    }

    $isWritable = is_writable($path) ? 'WRITABLE' : 'NOT WRITABLE';
    $owner = 'N/A';
    if (function_exists('posix_getpwuid')) {
        $stat = stat($path);
        $ownerInfo = posix_getpwuid($stat['uid']);
        $groupInfo = posix_getgrgid($stat['gid']);
        $owner = ($ownerInfo ? $ownerInfo['name'] : $stat['uid']).':'.($groupInfo ? $groupInfo['name'] : $stat['gid']);
    } else {
        $stat = stat($path);
        $owner = $stat['uid'].':'.$stat['gid'];
    }
    $perms = substr(sprintf('%o', fileperms($path)), -4);

    echo "$name: $isWritable (Owner: $owner, Perms: $perms)\n";
}
