<?php declare(strict_types = 1);
namespace noxkiwi\rewrite\UrlRewrite;

use noxkiwi\core\Config\JsonConfig;
use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Exception\InvalidJsonException;
use noxkiwi\core\Filesystem;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\core\Path;
use noxkiwi\rewrite\UrlRewrite;
use noxkiwi\singleton\Exception\SingletonException;

/**
 * I am the JSON UrlRewrite driver.
 *
 * @package      noxkiwi\rewrite\UrlRewrite
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class JsonUrlrewrite extends UrlRewrite
{
    private const CONFIG_URLREWRITE = 'config/urlrewrite.json';

    /**
     * @inheritDoc
     *
     * @param string $readable
     * @param array  $request
     *
     * @return bool
     */
    public function add(string $readable, array $request): bool
    {
        $configuration                                = $this->loadConf();
        $configuration[self::makeReadable($readable)] = JsonHelper::encode($request);
        $this->cache->set(self::CACHE_PREFIX, $readable, $request);

        return $this->saveConf($configuration);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $readable): bool
    {
        // open
        // unset
        $this->cache->clearKey(self::CACHE_PREFIX, $readable);

        // save
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function doGetRewrite(string $readable): array
    {
        $redirects = new JsonConfig(self::CONFIG_URLREWRITE, true);
        if ($redirects->exists($readable)) {
            try {
                return JsonHelper::decodeStringToArray($redirects->get($readable));
            } catch (InvalidJsonException $exception) {
                ErrorHandler::handleException($exception);
            }
        }

        return [];
    }

    /**
     * I will return the current configuration
     * <br />Or an empty array if unavailable.
     *
     * @return       array
     */
    private function loadConf(): array
    {
        $filePath = Path::getHomeDir() . self::CONFIG_URLREWRITE;
        try {
            if (! Filesystem::getInstance()->fileAvailable($filePath)) {
                return [];
            }

            return JsonHelper::decodeFileToArray($filePath);
        } catch (InvalidJsonException | SingletonException $exception) {
            ErrorHandler::handleException($exception);

            return [];
        }
    }

    /**
     * I will store the given $configuration array to the file
     *
     * @param array $configuration
     *
     * @return       bool
     */
    private function saveConf(array $configuration): bool
    {
        $path = Path::getHomeDir() . self::CONFIG_URLREWRITE;
        try {
            Filesystem::getInstance()->fileDelete($path);

            return Filesystem::getInstance()->fileWrite($path, JsonHelper::encode($configuration));
        } catch (SingletonException) {
            return false;
        }
    }
}
