<?php

namespace Pear\Crypt\DiffieHellman\Math;

use Pear\Crypt\DiffieHellman\Math\BigInteger\Bcmath;
use Pear\Crypt\DiffieHellman\Math\BigInteger\BigIntegerException;
use Pear\Crypt\DiffieHellman\Math\BigInteger\BigIntegerInterface;
use Pear\Crypt\DiffieHellman\Math\BigInteger\Gmp;

/**
 * Math extension wrapper for DiffieHellman with some additional helper
 * methods for RNG and binary conversion.
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2005-2007, Pádraic Brady <padraic.brady@yahoo.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Encryption
 * @package     Crypt_DiffieHellman
 * @author      Pádraic Brady <padraic.brady@yahoo.com>
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     $Id$
 * @link        http://
 */

/**
 * Crypt_DiffieHellman_Math_BigInteger class
 *
 * @category   Encryption
 * @package    Crypt_DiffieHellman
 * @author     Pádraic Brady <padraic.brady@yahoo.com>
 * @copyright  2005-2007 Pádraic Brady
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://
 * @version    @package_version@
 * @access     public
 */
class BigInteger
{
    /**
     * Holds an instance of one of the three arbitrary precision wrappers.
     *
     * @var BigIntegerInterface
     */
    protected $_math = null;

    /**
     * Constructor; Detects a suitable PHP extension for
     * arbitrary precision math and instantiates the suitable wrapper
     * object.
     *
     * @param string|null $extension
     * @throws BigIntegerException
     * @todo add big_int support
     */
    public function __construct($extension = null)
    {
        if ($extension === null) {
            if (extension_loaded('gmp')) {
                $extension = 'gmp';
            } else if (extension_loaded('bcmath')) {
                $extension = 'bcmath';
            } else {
                throw new BigIntegerException(
                    'gmp or bcmath extensions required'
                );
            }
        }

        $this->_math = $this->factory($extension);
    }

    /**
     * Factory for instantiating the big integer driver
     *
     * @param string $driver
     * @return mixed
     * @throws BigIntegerException
     */
    protected function factory($driver)
    {
        $extensions = [
            'gmp' => Gmp::class,
            'bcmath' => Bcmath::class,
        ];

        if (!isset($extensions[$driver])) {
            throw new BigIntegerException('Invalid big integer precision math extension');
        }

        $class = $extensions[$driver];

        return new $class;
    }

    /**
     * Redirect all public method calls to the wrapped extension object.
     *
     * @param string $methodName
     * @param array $args
     * @return mixed
     * @throws BigIntegerException
     */
    public function __call($methodName, $args)
    {
        if (!method_exists($this->_math, $methodName)) {
            throw new BigIntegerException('Invalid method call: ' . get_class($this->_math) . '::' . $methodName . '() does not exist');
        }

        return call_user_func_array(array($this->_math, $methodName), $args);
    }

}
