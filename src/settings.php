<?php

declare(strict_types=1);

use Lits\Config\DatabaseConfig;
use Lits\Config\TemplateConfig;
use Lits\Framework;

return function (Framework $framework): void {
    $framework->addConfig('database', new DatabaseConfig());

    $settings = $framework->settings();
    assert($settings['template'] instanceof TemplateConfig);

    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    $settings['template']->paths[] = $path . 'template';
};
