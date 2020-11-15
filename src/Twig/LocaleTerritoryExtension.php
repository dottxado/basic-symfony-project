<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleTerritoryExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $localeTerritories = [];

    public function __construct(string $locale_territories)
    {
        $locale_territories = explode('|', trim($locale_territories));
        if (empty($locale_territories)) {
            return;
        }
        foreach ($locale_territories as $locale_territory) {
            $this->localeTerritories[substr($locale_territory, 0, 2)] = $locale_territory;
        }
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('localeTerritory', [$this, 'outputTerritory']),
//            new TwigFunction('getLocalizedPostTitle', [$this, 'getLocalizedPostTitle']),
//            new TwigFunction('getLocalizedPostContent', [$this, 'getLocalizedPostContent']),
        ];
    }

    public function outputTerritory($locale)
    {
        if (!isset($this->localeTerritories[$locale])) {
            throw new \UnexpectedValueException('The list of supported locales with their territories must not be empty.');
        }

        return $this->localeTerritories[$locale];
    }

//    public function getLocalizedPostTitle(Post $post, $locale)
//    {
//        if ('it' === $locale) {
//            return $post->getTitleIt();
//        } else {
//            return $post->getTitleEn();
//        }
//    }

//    public function getLocalizedPostContent(Post $post, $locale)
//    {
//        if ('it' === $locale) {
//            return $post->getContentIt();
//        } else {
//            return $post->getContentEn();
//        }
//    }
}
