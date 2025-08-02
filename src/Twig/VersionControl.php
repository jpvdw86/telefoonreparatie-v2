<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class VersionControl extends AbstractExtension
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('versionControl', [$this, 'versionControl']),
        ];
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function versionControl($file)
    {
        $originalFile = $file;
        $file = str_replace(['//','\\\\'],['/', '\\'], $file);
        try {
            if (strpos($file, '?')) {
                $file = $file . '&v=' . md5_file(getenv('WEB_DIR') . $file);
            } else {
                $file = $file . '?v=' . md5_file(getenv('WEB_DIR') . $file);
            }

            return $file;
        } catch (\Exception $exception) {
            return $originalFile;
        }
    }
}