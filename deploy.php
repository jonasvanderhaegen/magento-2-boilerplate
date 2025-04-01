<?php
namespace Deployer;

// This will only work if your hosting provider would be Hypernode. For other hostingproviders you'll have to rewrite several lines.

require 'recipe/magento2.php';

// Project name
set('application', 'Foobar magento 2');

set('keep_releases', 3);

set('bin/composer', '/usr/local/bin/composer');

// Project repository
set('repository', 'git@github.com:foobar/foobar.git');

set('shared_dirs', [
    'var/composer_home',
    'var/log',
    'var/export',
    'var/report',
    'var/rma',
    'var/import_history',
    'var/session',
    'var/importexport',
    'var/import',
    'var/backups',
    'var/tmp',
    'pub/sitemaps',
    'sitemaps',
    'pub/media'
]);

set('shared_files', [
    '{{magento_dir}}/app/etc/env.php',
    '{{magento_dir}}/var/.maintenance.ip',
    '{{magento_dir}}/app/etc/config.php',
]);

set('clear_paths', [
    'generated/*',
    'pub/static/_cache/*',
    'var/generation/*',
    'var/cache/*',
    'var/page_cache/*',
    'var/view_preprocessed/*',
    'scripts/*'
]);

// Hosts
host('foobarstaging.hypernode.io')
    ->set('labels', [
        'stage' =>'foobar_staging',
        'env' => 'staging',
    ])
    ->set('remote_user', 'app')
    ->set('branch', 'next')
    ->set('deploy_path', '/data/web')
    ->set('writable_mode', 'chmod')
    ->set('deployment_languages', 'en_US fr_FR nl_NL')
    ->set('theme', 'magento/lumia')
;

host('foobar.hypernode.io')
    ->set('labels', [
        'stage' =>'foobar_production',
        'env' => 'production',
    ])
    ->set('remote_user', 'app')
    ->set('branch', 'main')
    ->set('deploy_path', '/data/web')
    ->set('writable_mode', 'chmod')
    ->set('deployment_languages', 'en_US fr_FR nl_NL')
    ->set('theme', 'magento/lumia')
;


desc('Deploy assets');
task('magento:deploy:assets', function () {
    run("{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy en_US fr_FR --theme Magento/backend --no-parent", [
        'timeout' => 1800,
    ]);
    run("{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy {{deployment_languages}} --theme {{theme}} --no-parent", [
        'timeout' => 1800,
    ]);
});

desc('Create subdirectories');
task('hypernode:create-storeviews', function () {
    run("ln -s /data/web/current/pub/ /data/web/current/pub/fr");
    run("ln -s /data/web/current/pub/ /data/web/current/pub/en");
    run("ln -s /data/web/current/pub/ /data/web/current/pub/de");
    run("ln -s /data/web/current/pub/ /data/web/current/pub/nl");
});
after('deploy:symlink', 'hypernode:create-storeviews');

desc('Backup assets');
task('magento:backup:download', function () {
    try {
        $output = run("{{bin/php}} gdpr-dump.phar");
    } catch (\Exception $e) {
        if ($e->getCode() === 1) {
            //command not found
            $outputURl = run('curl -L -s https://api.github.com/repos/Smile-SA/gdpr-dump/releases/latest | grep -o -E "https://(.*)releases(.*)gdpr-dump.phar"');
            run('curl -L -s ' . $outputURl . " > gdpr-dump.phar");
        }
    }

    //get the database credentials
    $outputDBFile = run("n98-magerun2-5.0.0 db:info --format=csv --root-dir=\"{{current_path}}\" ");

    $outputDB = explode("\n", $outputDBFile);
    $config = [];
    foreach ($outputDB as $line) {
        list($key, $value) = explode(",", $line);
        $config[$key] = $value;
    }

    $db_host = $config['host'];
    $db_name = $config['dbname'];
    $db_user = $config['username'];
    $db_pass = $config['password'];

    upload("app/etc/gdpr-dump.yaml", '{{current_path}}/app/etc/gdpr-dump.yaml');
    run("DB_HOST=" . $db_host . " DB_USER=" . $db_user . " DB_PASSWORD=" . $db_pass . " DB_NAME=" . $db_name . "  {{bin/php}} gdpr-dump.phar {{current_path}}/app/etc/gdpr-dump.yaml");

    download("~/database.sql.gz", "var/backups/");
    run("rm ~/database.sql.gz");
});

desc('Restart Byte Hypernode Mysql');
task('hypernode:restart:mysql', function () {
    run("hypernode-servicectl restart mysql");
    sleep(3);
});
before('magento:deploy:assets', 'hypernode:restart:mysql');

desc('Restart Byte Hypernode FPM');
task('hypernode:restart:fpm', function () {
    $fpmversion = run("hypernode-servicectl --help | egrep -i -o '(php[0-9].[0-9]-fpm)'");
    run("hypernode-servicectl restart " . $fpmversion);
    sleep(3);
});
before('deploy:success', 'hypernode:restart:fpm');
before('magento:upgrade:db', 'hypernode:restart:fpm');

// Failure
after('deploy:failed', 'deploy:unlock');


desc('Upload the database and import it');
task('database:upload:acc', function () {
    upload("var/backups/database.sql.gz", '{{current_path}}/db/database.sql.gz');
    run("gunzip {{current_path}}/db/database.sql.gz");
    run("n98-magerun2-5.0.0 db:import {{current_path}}/db/database.sql --drop --root-dir=\"{{current_path}}\" ");
    run('rm {{current_path}}/db/database.sql');
});

desc('Convert the database for acc use');
task('database:convert:acc', function () {
    upload("db/prod-to-acc.sql", '{{current_path}}/db/prod-to-acc.sql');
    run("n98-magerun2-5.0.0 db:import {{current_path}}/db/prod-to-acc.sql --root-dir=\"{{current_path}}\" ");
});

desc('Download some necessary assets');
task('media:download', function () {
    runLocally('mkdir -p pub/media/wysiwyg/magento/lumia/flags');
    download('{{current_path}}/pub/media/wysiwyg/magento/lumia/', "pub/media/wysiwyg/magento/lumia/");
});
