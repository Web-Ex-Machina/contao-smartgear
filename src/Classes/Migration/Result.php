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

namespace WEM\SmartgearBundle\Classes\Migration;

class Result
{
    public const STATUS_NOT_EXCUTED_YET = 'not executed yet';

    public const STATUS_SHOULD_RUN = 'should run';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_FAIL = 'fail';

    public const STATUS_SUCCESS = 'success';

    /** @var string */
    protected string $status = self::STATUS_NOT_EXCUTED_YET;

    /** @var array */
    protected array $logs = [];

    public function setStatus(string $status): self
    {
        if (!\in_array($status, [self::STATUS_NOT_EXCUTED_YET, self::STATUS_SHOULD_RUN, self::STATUS_SKIPPED, self::STATUS_FAIL, self::STATUS_SUCCESS], true)) {
            throw new \InvalidArgumentException(sprintf('The given status "%s" is invalid', $status));
        }

        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function addLog(string $message): self
    {
        $this->logs[] = $message;

        return $this;
    }
}
