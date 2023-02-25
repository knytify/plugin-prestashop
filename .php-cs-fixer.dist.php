<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude('somedir')
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    'class_attributes_separation' => true,
    'phpdoc_to_comment' => true,
    'single_quote' => true,
    'array_syntax' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_extra_blank_lines' => true,
    'blank_line_before_statement' => true
])
    ->setFinder($finder);
