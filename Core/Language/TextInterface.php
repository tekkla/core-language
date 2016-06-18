<?php
namespace Core\Language;

/**
 * TextInterface.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
interface TextInterface
{
    /**
     * Sets language object
     *
     * @param LanguageInterface $language
     */
    public function setLanguage(LanguageInterface $language);


    /**
     * Sets the name of the language storage to query
     *
     * @param string $storage_name
     */
    public function setStorageName(string $storage_name);

    /**
     * Queries language storage for a string mapped to a key.
     *
     * @param string $key
     *            The text key
     * @param array $strings
     *            Optional array of strings which triggers vsprintf() on the text
     *
     * @throws LanguageException when Language object is not set or languag storage name is missing
     *
     * @return string
     */
    public function get(string $key, array $strings = []): string;
}

