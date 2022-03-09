<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backup;

class BackupDatabase
{
    public $suffix;
    public $dirs;
    protected $dbInstance;

    public function __construct()
    {
        try {
            $this->dbInstance = new \PDO('mysql:host='.\Config::get('dbHost').';dbname='.\Config::get('dbDatabase'),
    \Config::get('dbUser'), \Config::get('dbPass'));
        } catch (Exception $e) {
            die('Error '.$e->getMessage());
        }
        $this->suffix = date('Ymd_His');
    }

    public function backup($tables = '*', $filename = '', $path = ''): void
    {
        $output = '-- database backup - '.date('Y-m-d H:i:s').PHP_EOL;
        $output .= 'SET NAMES utf8;'.PHP_EOL;
        $output .= "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';".PHP_EOL;
        $output .= 'SET foreign_key_checks = 0;'.PHP_EOL;
        $output .= 'SET AUTOCOMMIT = 0;'.PHP_EOL;
        $output .= 'START TRANSACTION;'.PHP_EOL;
        //get all table names
        if ('*' === $tables) {
            $tables = [];
            $query = $this->dbInstance->prepare('SHOW TABLES');
            $query->execute();
            while ($row = $query->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            $query->closeCursor();
        } else {
            $tables = \is_array($tables) ? $tables : explode(',', $tables);
        }

        foreach ($tables as $table) {
            $query = $this->dbInstance->prepare("SELECT * FROM `$table`");
            $query->execute();
            $output .= "DROP TABLE IF EXISTS `$table`;".PHP_EOL;

            $query2 = $this->dbInstance->prepare("SHOW CREATE TABLE `$table`");
            $query2->execute();
            $row2 = $query2->fetch(\PDO::FETCH_NUM);
            $query2->closeCursor();
            $output .= PHP_EOL.$row2[1].';'.PHP_EOL;

            while ($row = $query->fetch(\PDO::FETCH_NUM)) {
                $output .= "INSERT INTO `$table` VALUES(";
                for ($j = 0; $j < \count($row); ++$j) {
                    $row[$j] = addslashes($row[$j] ?: '');
                    $row[$j] = str_replace("\n", '\\n', $row[$j]);
                    if (isset($row[$j])) {
                        $output .= "'".$row[$j]."'";
                    } else {
                        $output .= "''";
                    }
                    if ($j < (\count($row) - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ');'.PHP_EOL;
            }
        }
        $output .= PHP_EOL.PHP_EOL;

        $output .= 'COMMIT;';

        if (!$filename) {
            $filename = 'sgbackup_'.date('Ymd_Hi');
        }

        // Generate the full path
        if ($path) {
            // Add last slash if not found
            if ('' !== substr($path, 0, -1)) {
                $path .= '/';
            }
        } else {
            $path = 'files/backups/';
        }

        $this->writeUTF8filename($path.$filename.'.sql', $output);
    }

    private function writeUTF8filename($fn, $c): void
    {
        /* save as utf8 encoding */
        $f = new \File($fn);
        // Now UTF-8 - Add byte order mark
        $f->write(pack('CCC', 0xef, 0xbb, 0xbf));
        $f->write($c);
        $f->close();
    }
}
