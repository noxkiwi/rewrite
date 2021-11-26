<?php declare(strict_types = 1);
namespace noxkiwi\rewrite;

use Exception;
use noxkiwi\cache\Cache;
use noxkiwi\core\ErrorHandler;
use noxkiwi\rewrite\Interfaces\UrlRewriteInterface;
use noxkiwi\singleton\Singleton;
use function is_array;
use function str_replace;
use function strtolower;
use function urldecode;
use const E_USER_NOTICE;

/**
 * I am the base UrlRewrite driver.
 *
 * @package      noxkiwi\rewrite
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class UrlRewrite extends Singleton implements UrlRewriteInterface
{
    protected const USE_DRIVER = true;
    protected Cache $cache;

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();
        try {
            $this->cache = Cache::getInstance();
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);
        }
    }

    public const CACHE_PREFIX = Cache::DEFAULT_PREFIX . 'URLREWRITE_';

    /**
     * I will optimize the given $url
     *
     * @param string $url
     *
     * @return       string
     */
    final public static function makeReadable(string $url): string
    {
        return strtolower(str_replace(' ', '_', $url));
    }

    /**
     * @inheritDoc
     */
    final public function get(string $readable): array
    {
        $readable = urldecode(strtolower($readable));
        $redirect = $this->cache->get(self::CACHE_PREFIX, $readable);
        if (! empty($redirect) && is_array($redirect)) {
            return $redirect;
        }
        $redirect = $this->doGetRewrite($readable);
        $this->cache->set(self::CACHE_PREFIX, $readable, $redirect);

        return $redirect;
    }

    /**
     * I will return the array of Request data that is available from the $readable URL you provided
     * <br />If not found, I will return an empty array
     *
     * @param string $readable
     *
     * @return       array
     */
    abstract protected function doGetRewrite(string $readable): array;
}

