<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Classes\Analyzer;

class Htaccess
{
    public const REWRITE_ENGINE_ON = 'RewriteEngine On';
    public const REWRITE_COND_HTTPS_1 = 'RewriteCond %{HTTPS} off [OR]';
    public const REWRITE_COND_HTTPS_2 = 'RewriteCond %{SERVER_PORT} 80 [OR]';
    public const REWRITE_COND_WWW_1 = 'RewriteCond %{HTTP_HOST} !^www\. [NC]';
    public const REWRITE_COND_WWW_2 = 'RewriteCond %{HTTP_HOST} ^(?:www\.)?(.+)$ [NC]';
    public const REWRITE_RULE = 'RewriteRule ^.*$ https://www.%1%{REQUEST_URI} [L,NE,R=301]'; // [L,NE,R=301]
    public const REWRITE_RULE_FW_ASSETS_OLD = 'RewriteRule ^(assets|bundles)/ - [ENV=CONTAO_ASSETS:true]';
    public const REWRITE_RULE_FW_ASSETS_NEW = 'RewriteRule ^(assets\/(?!framway).*|bundles)/ - [ENV=CONTAO_ASSETS:true]';

    // RewriteCond %{HTTPS} off
    // RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    // RewriteCond %{HTTP_HOST} !^www\. [NC]
    // RewriteRule (.*) https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    protected $filepath;

    public function __construct(string $htAccessFilePath)
    {
        $this->filepath = $htAccessFilePath;
    }

    public function hasRedirectToWwwAndHttps(): bool
    {
        $content = $this->getLines();

        $hasRewriteCondHttps1 = false;
        $hasRewriteCondHttps2 = false;
        $hasRewriteCondWww1 = false;
        $hasRewriteCondWww2 = false;
        $hasRewriteRule = false;

        foreach ($content as $line) {
            if ($this->isComment($line)) {
                continue;
            }
            if ($this->isLineARedirectionToHttps1($line)) {
                $hasRewriteCondHttps1 = true;
            } elseif ($this->isLineARedirectionToHttps2($line)) {
                $hasRewriteCondHttps2 = true;
            } elseif ($this->isLineARedirectionToWWW1($line)) {
                $hasRewriteCondWww1 = true;
            } elseif ($this->isLineARedirectionToWWW2($line)) {
                $hasRewriteCondWww2 = true;
            } elseif ($this->isLineARedirectionToWwwAndHttps($line)) {
                $hasRewriteRule = true;
            }
        }

        return $hasRewriteCondHttps1 && $hasRewriteCondHttps2 && $hasRewriteCondWww1 && $hasRewriteCondWww2 && $hasRewriteRule;
    }

    public function enableRedirectToWwwAndHttps(): bool
    {
        $content = $this->getLines();
        $foundInFirstLoop = false;
        // we loop once to find if the lines are already here
        // but may be commented
        // If lines weren't found, we loop until
        // we find the "Rewrite on" line
        // and append its content with a concatenation of all 5 desired lines, separated by "\n"
        foreach ($content as $index => $line) {
            if ($this->isLineARedirectionToHttps1($line)
                || $this->isLineARedirectionToHttps2($line)
                || $this->isLineARedirectionToWWW1($line)
                || $this->isLineARedirectionToWWW2($line)
                || $this->isLineARedirectionToWwwAndHttps($line)
            ) {
                if ($this->isComment($line)) {
                    $content[$index] = $this->uncomment($line);
                    $foundInFirstLoop = true;
                }
            }
        }

        if (!$foundInFirstLoop) {
            foreach ($content as $index => $line) {
                if ($this->isLineARewriteEngineOn($line)) {
                    $content[$index] = $line.self::REWRITE_COND_HTTPS_1.\PHP_EOL.self::REWRITE_COND_HTTPS_2.\PHP_EOL.self::REWRITE_COND_WWW_1.\PHP_EOL.self::REWRITE_COND_WWW_2.\PHP_EOL.self::REWRITE_RULE;
                }
            }
        }

        return $this->writeFile($content);
    }

    public function disableRedirectToWwwAndHttps(): bool
    {
        $content = $this->getLines();
        foreach ($content as $index => $line) {
            if (!$this->isComment($line)
            && ($this->isLineARedirectionToHttps1($line)
                || $this->isLineARedirectionToHttps2($line)
                || $this->isLineARedirectionToWWW1($line)
                || $this->isLineARedirectionToWWW2($line)
                || $this->isLineARedirectionToWwwAndHttps($line)
            )) {
                $content[$index] = $this->comment($line);
            }
        }

        return $this->writeFile($content);
    }

    public function enableFramwayAssetsManagementRules(): bool
    {
        $content = $this->getLines();
        foreach ($content as $index => $line) {
            if (!$this->isComment($line) && $this->isLineARewriteRuleFwAssetsOld($line)) {
                $content[$index] = $this->comment($line).self::REWRITE_RULE_FW_ASSETS_NEW.\PHP_EOL;
            }
        }

        return $this->writeFile($content);
    }

    public function disableFramwayAssetsManagementRules(): bool
    {
        $content = $this->getLines();
        foreach ($content as $index => $line) {
            if ($this->isComment($line) && $this->isLineARewriteRuleFwAssetsOld($line)) {
                $content[$index] = $this->uncomment($line);
            }
            if (!$this->isComment($line) && $this->isLineARewriteRuleFwAssetsNew($line)) {
                unset($content[$index]);
            }
        }

        return $this->writeFile($content);
    }

    protected function isLineARewriteEngineOn(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_ENGINE_ON);
    }

    protected function isLineARedirectionToHttps1(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_HTTPS_1);
    }

    protected function isLineARedirectionToHttps2(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_HTTPS_2);
    }

    protected function isLineARedirectionToWWW1(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_WWW_1);
    }

    protected function isLineARedirectionToWWW2(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_WWW_2);
    }

    protected function isLineARedirectionToWwwAndHttps(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE);
    }

    protected function isLineARewriteRuleFwAssetsOld(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE_FW_ASSETS_OLD);
    }

    protected function isLineARewriteRuleFwAssetsNew(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE_FW_ASSETS_NEW);
    }

    protected function isComment(string $line): bool
    {
        return 0 === strncmp('#', $line, 1);
    }

    protected function uncomment(string $line): string
    {
        return str_replace('#', '', $line);
    }

    protected function comment(string $line): string
    {
        return '#'.$line;
    }

    protected function getLines(): array
    {
        return array_filter(file($this->filepath));
    }

    protected function writeFile(array $lines): bool
    {
        $this->createBackupFile();

        return false !== file_put_contents($this->filepath, implode('', $lines));
    }

    protected function createBackupFile(): bool
    {
        return false !== file_put_contents($this->filepath.'_'.date('Ymd_his'), file_get_contents($this->filepath));
    }
}
