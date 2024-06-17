<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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

    public const REWRITE_COND_HTTPS_1_OLD = 'RewriteCond %{HTTPS} off [OR]';

    public const REWRITE_COND_HTTPS_2_OLD = 'RewriteCond %{SERVER_PORT} 80 [OR]';

    public const REWRITE_COND_WWW_1_OLD = 'RewriteCond %{HTTP_HOST} !^www\. [NC]';

    public const REWRITE_COND_WWW_2_OLD = 'RewriteCond %{HTTP_HOST} ^(?:www\.)?(.+)$ [NC]';

    public const REWRITE_RULE_OLD = 'RewriteRule ^.*$ https://www.%1%{REQUEST_URI} [L,NE,R=301]'; // [L,NE,R=301]

    public const REWRITE_RULE_FW_ASSETS_OLD = 'RewriteRule ^(assets|bundles)/ - [ENV=CONTAO_ASSETS:true]';

    public const REWRITE_RULE_FW_ASSETS_NEW = 'RewriteRule ^(assets\/(?!framway).*|bundles)/ - [ENV=CONTAO_ASSETS:true]';

    public const REWRITE_COND_HTTPS = 'RewriteCond %{HTTPS} off';

    public const REWRITE_RULE_HTTPS = 'RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';

    public const REWRITE_COND_WWW = 'RewriteCond %{HTTP_HOST} !^www\. [NC]';

    public const REWRITE_RULE_WWW = 'RewriteRule (.*) https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]';

    public function __construct(protected string $filepath)
    {
    }

    public function hasRedirectToWwwAndHttps_OLD(): bool
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

            if ($this->isLineARedirectionToHttps1_OLD($line)) {
                $hasRewriteCondHttps1 = true;
            } elseif ($this->isLineARedirectionToHttps2_OLD($line)) {
                $hasRewriteCondHttps2 = true;
            } elseif ($this->isLineARedirectionToWWW1_OLD($line)) {
                $hasRewriteCondWww1 = true;
            } elseif ($this->isLineARedirectionToWWW2_OLD($line)) {
                $hasRewriteCondWww2 = true;
            } elseif ($this->isLineARedirectionToWwwAndHttps_OLD($line)) {
                $hasRewriteRule = true;
            }
        }

        return $hasRewriteCondHttps1 && $hasRewriteCondHttps2 && $hasRewriteCondWww1 && $hasRewriteCondWww2 && $hasRewriteRule;
    }

    public function hasRedirectToWwwAndHttps(): bool
    {
        $content = $this->getLines();

        $hasRewriteCondHttps = false;
        $hasRewriteRuleHttps = false;
        $hasRewriteCondWww = false;
        $hasRewriteRuleWww = false;

        foreach ($content as $line) {
            if ($this->isComment($line)) {
                continue;
            }

            if ($this->isLineARewriteCondHttps($line)) {
                $hasRewriteCondHttps = true;
            } elseif ($this->isLineARewriteRuleHttps($line)) {
                $hasRewriteRuleHttps = true;
            } elseif ($this->isLineARewriteCondWww($line)) {
                $hasRewriteCondWww = true;
            } elseif ($this->isLineARewriteRuleWww($line)) {
                $hasRewriteRuleWww = true;
            }
        }

        return $hasRewriteCondHttps && $hasRewriteRuleHttps && $hasRewriteCondWww && $hasRewriteRuleWww;
    }

    // public function enableRedirectToWwwAndHttps(): bool
    // {
    //     $content = $this->getLines();
    //     $foundInFirstLoop = false;
    //     // we loop once to find if the lines are already here
    //     // but may be commented
    //     // If lines weren't found, we loop until
    //     // we find the "Rewrite on" line
    //     // and append its content with a concatenation of all 5 desired lines, separated by "\n"
    //     foreach ($content as $index => $line) {
    //         if ($this->isLineARedirectionToHttps1($line)
    //             || $this->isLineARedirectionToHttps2($line)
    //             || $this->isLineARedirectionToWWW1($line)
    //             || $this->isLineARedirectionToWWW2($line)
    //             || $this->isLineARedirectionToWwwAndHttps($line)
    //         ) {
    //             if ($this->isComment($line)) {
    //                 $content[$index] = $this->uncomment($line);
    //                 $foundInFirstLoop = true;
    //             }
    //         }
    //     }

    //     if (!$foundInFirstLoop) {
    //         foreach ($content as $index => $line) {
    //             if ($this->isLineARewriteEngineOn($line)) {
    //                 $content[$index] = $line.self::REWRITE_COND_HTTPS_1.\PHP_EOL.self::REWRITE_COND_HTTPS_2.\PHP_EOL.self::REWRITE_COND_WWW_1.\PHP_EOL.self::REWRITE_COND_WWW_2.\PHP_EOL.self::REWRITE_RULE;
    //             }
    //         }
    //     }

    //     return $this->writeFile($content);
    // }

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
            if ($this->isLineARewriteCondHttps($line)
                || $this->isLineARewriteRuleHttps($line)
                || $this->isLineARewriteCondWww($line)
                || $this->isLineARewriteRuleWww($line)
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
                    $content[$index] = $line.self::REWRITE_COND_HTTPS.\PHP_EOL.self::REWRITE_RULE_HTTPS.\PHP_EOL.self::REWRITE_COND_WWW.\PHP_EOL.self::REWRITE_RULE_WWW.\PHP_EOL;
                }
            }
        }

        return $this->writeFile($content);
    }

    public function disableRedirectToWwwAndHttps_OLD(): bool
    {
        $content = $this->getLines();
        foreach ($content as $index => $line) {
            if (!$this->isComment($line)
            && ($this->isLineARedirectionToHttps1_OLD($line)
                || $this->isLineARedirectionToHttps2_OLD($line)
                || $this->isLineARedirectionToWWW1_OLD($line)
                || $this->isLineARedirectionToWWW2_OLD($line)
                || $this->isLineARedirectionToWwwAndHttps_OLD($line)
            )) {
                $content[$index] = $this->comment($line);
            }
        }

        return $this->writeFile($content);
    }

    public function disableRedirectToWwwAndHttps(): bool
    {
        $content = $this->getLines();
        foreach ($content as $index => $line) {
            if (!$this->isComment($line)
            && ($this->isLineARewriteCondHttps($line)
                || $this->isLineARewriteRuleHttps($line)
                || $this->isLineARewriteCondWww($line)
                || $this->isLineARewriteRuleWww($line)
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

    protected function isLineARedirectionToHttps1_OLD(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_HTTPS_1_OLD);
    }

    protected function isLineARedirectionToHttps2_OLD(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_HTTPS_2_OLD);
    }

    protected function isLineARedirectionToWWW1_OLD(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_WWW_1_OLD);
    }

    protected function isLineARedirectionToWWW2_OLD(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_WWW_2_OLD);
    }

    protected function isLineARedirectionToWwwAndHttps_OLD(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE_OLD);
    }

    protected function isLineARewriteCondHttps(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_HTTPS);
    }

    protected function isLineARewriteRuleHttps(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE_HTTPS);
    }

    protected function isLineARewriteCondWww(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_COND_WWW);
    }

    protected function isLineARewriteRuleWww(string $line): bool
    {
        return false !== stripos($line, self::REWRITE_RULE_WWW);
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
