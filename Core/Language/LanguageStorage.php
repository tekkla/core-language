<?php
namespace Core\Language;

use Core\Storage\Storage;

/**
 * LanguageStorage.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class LanguageStorage extends Storage
{

    /**
     * Returns a language string by looking for a matching key
     *
     * Will return the key when no machting entry is found.
     *
     * @param string $key
     *            The key to get text for
     *
     * @return string
     */
    public function getText($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $key;
    }

    /**
     * Alternate version of getText()
     *
     * @see self::getText($key)
     */
    public function text($key)
    {
        return $this->getText($key);
    }

    /**
     * Alternate version of getText()
     *
     * @see self::getText($key)
     */
    public function get($key)
    {
        return $this->getText($key);
    }
}

