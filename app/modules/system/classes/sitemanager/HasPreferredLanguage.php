<?php namespace System\Classes\SiteManager;

/**
 * HasPreferredLanguage implements browser detection logic
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasPreferredLanguage
{
    /**
     * getSiteFromBrowser locates the site based on the browser locale, e.g. HTTP_ACCEPT_LANGUAGE
     */
    public function getSiteFromBrowser(string $acceptLanguage)
    {
        $locales = $this->findAcceptedLocales($acceptLanguage);

        foreach ($locales as $locale => $priority) {
            if ($foundSite = $this->getSiteForLocale($locale)) {
                return $foundSite;
            }
        }

        return $this->getPrimarySite();
    }

    /**
     * getSiteForLocale
     */
    public function getSiteForLocale(string $locale)
    {
        return $this->listSites()
            ->where('is_enabled', true)
            ->filter(function($site) use ($locale) {
                return $site->matchesLocale($locale);
            })
            ->first()
        ;
    }

    /**
     * findAcceptedLocales based on an accepted string, e.g. en-GB,en-US;q=0.9,en;q=0.8
     * Returns a sorted array in format of `[(string) locale => (float) priority]`
     */
    protected function findAcceptedLocales(string $acceptedStr): array
    {
        $result = $matches = [];
        $acceptedStr = strtolower($acceptedStr);

        // Find explicit matches
        preg_match_all('/([\w-]+)(?:[^,\d]+([\d.]+))?/', $acceptedStr, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $locale = $match[1] ?? '';
            $priority = (float) ($match[2] ?? 1.0);

            if ($locale) {
                $result[$locale] = $priority;
            }
        }

        // Estimate other locales by popping off the region (en-us -> en)
        foreach ($result as $locale => $priority) {
            $shortLocale = explode('-', $locale)[0];
            if ($shortLocale !== $locale && !array_key_exists($shortLocale, $result)) {
                $result[$shortLocale] = $priority - 0.1;
            }
        }

        arsort($result);
        return $result;
    }
}
