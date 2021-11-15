<?php declare(strict_types = 1);
namespace noxkiwi\rewrite\Interfaces;

/**
 * I am the interface for all UrlRewrite drivers.
 *
 * @package      noxkiwi\rewrite\Interfaces
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface UrlRewriteInterface
{
    /**
     * I will try to fetch the rewrite rule for the given $readable URL.
     *
     * @param string $readable
     *
     * @return array
     */
    public function get(string $readable): array;

    /**
     * I will add the given combination of $readable and the resulting $Request as rule.
     *
     * @param string $readable
     * @param array  $request
     *
     * @return bool
     */
    public function add(string $readable, array $request): bool;

    /**
     * I will remove the given $readable URL.
     *
     * @param string $readable
     *
     * @return bool
     */
    public function remove(string $readable): bool;
}
