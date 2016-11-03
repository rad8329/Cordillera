<?php

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
