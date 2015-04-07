<?php

/*
 * This file is part of the Cordillera framework.
 *
 * (c) Robert Adrián Díaz <rad8329@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Made with love in Medellín
 */

namespace cordillera\base\interfaces;

interface Auth
{
    /**
     * @param mixed $id
     * @param array $data
     */
    public function login($id, array $data = []);

    public function logout();
}