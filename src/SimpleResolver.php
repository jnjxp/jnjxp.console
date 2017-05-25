<?php
/**
 * Jnjxp Console
 *
 * PHP version 5
 *
 * Copyright (C) 2017 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Resolution
 * @package   Jnjxp\Console
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2017 Jake Johns
 * @license   http://jnj.mit-license.org/2017 MIT License
 * @link      http://jakejohns.net
 */

namespace Jnjxp\Console;

/**
 * SimpleResolver
 *
 * @category Resolution
 * @package  Jnjxp\Console
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     http://jakejohns.net
 */
class SimpleResolver
{

    /**
     * Resolve a spec to a callable
     *
     * @param mixed $spec DESCRIPTION
     *
     * @return callable
     *
     * @access public
     */
    public function __invoke($spec)
    {
        if (is_callable($spec)) {
            return $spec;
        }

        if (is_string($spec)) {
            $spec = new $spec;
        }

        if (is_array($spec) && is_string($spec[0])) {
            $spec[0] = new $spec[0];
        }

        if (! is_callable($spec)) {
            throw new \InvalidArgumentException(
                'Could not resolve spec to callable'
            );
        }

        return $spec;
    }
}
