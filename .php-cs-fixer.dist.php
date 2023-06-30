<?php

$finder = PhpCsFixer\Finder::create()->in(['src']);

return (new PhpCsFixer\Config())->setRules(['@PSR12' => true])->setFinder($finder);
