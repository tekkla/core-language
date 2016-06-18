<?php
namespace Core\Language;

use Core\Toolbox\Arrays\Flatten;

/**
 * Language.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class Language implements LanguageInterface
{

    /**
     *
     * @var LanguageStorage
     */
    private $storage;

    /**
     *
     * @var string
     */
    private $fallback = '';

    /**
     * Constructor
     *
     * Inits the internel storage.
     */
    public function __construct()
    {
        $this->storage = new LanguageStorage();
    }

    /**
     * Sets the name of the fallback storage
     *
     * The fallback storage will be queried for a text when the requested storage returns no result.
     *
     * @param string $fallback
     *            name of the fallback language storage
     */
    public function setFallbackStorageName(string $fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Loads a language file
     *
     * When a text already exists in a storage, the loaded texts overwrites and extends the storage with the new data.
     *
     * @param string $storage_name
     *            Name of storage the loaded language texts will be stored in
     * @param string $filename
     *            File path of mthe languagefile to load
     */
    public function loadLanguageFile(string $storage_name, string $filename, string $glue = '.')
    {
        if (file_exists($filename)) {

            $language = include ($filename);

            if (is_array($language)) {
                $this->parseLanguageArray($language, $storage_name);
            }
        }
    }

    /**
     * Parses an array of key/value structured strings, flattens it with the glue and adds it to the a language storage
     *
     * @param array $language
     *            The array with language strings
     * @param string $storage_name
     *            The name of the language storage to place the srings in
     * @param string $glue
     *            Optional glue which should be used in flattening process
     */
    public function parseLanguageArray(array $language, string $storage_name, string $glue = '.')
    {
        $toolbox = new Flatten($language);
        $toolbox->setPreserveFlaggedArraysFlag(true);
        $toolbox->setGlue($glue);

        $language = $toolbox->flatten();

        if (!empty($language) && !isset($this->storage->{$storage_name})) {
            $this->storage->{$storage_name} = new LanguageStorage();
        }

        foreach ($language as $key => $val) {
            $this->storage->{$storage_name}->{$key} = $val;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Core\Language\LanguageInterface::get()
     */
    public function get(string $storage_name, string $key)
    {

        // IMPORTANT! Keys with spaces won't be processed without any further
        // notice to the developer or user. Spaces mean texts and no keys.
        if (is_array($key) || strpos($key, ' ')) {
            return $key;
        }

        // Return key when key is not found
        if (!isset($this->storage->{$storage_name}->{$key})) {

            // Do we have a fallback storage set to look for?
            if (!empty($this->fallback) && isset($this->storage->{$this->fallback})) {

                if (isset($this->storage->{$this->fallback}->{$key})) {
                    return $this->storage->{$this->fallback}->{$key};
                }

                return $key;
            }
        }

        $text = $this->storage->{$storage_name}->{$key};

        // Prevent infinite loops
        if ($text == $key) {
            Throw new LanguageException(sprintf('There is an infinite loop recursion in language data of storage "%s" on key "%s"', $storage_name, $key));
        }

        // Return requested text
        return $this->get($storage_name, $text);
    }

    /**
     * Returns a reference to a specfic the language storage
     *
     * @param string $storage_name
     *            Name of the storage to get a reference to
     *
     * @return \Core\Language\LanguageStorage
     */
    public function &getStorage($storage_name): LanguageStorage
    {
        $return = false;

        if (isset($storage_name, $this->storage->{$storage_name})) {
            $return = $this->storage->{$storage_name};
        }

        return $return;
    }

    /**
     * Creates and return as Text object
     *
     * @param string $storage_name
     *            Name of the text storage the adapter gets it's data from
     *
     * @return \Core\Language\TextInterface
     */
    public function createTextAdapter($storage_name): TextInterface
    {
        $adapter = new Text();
        $adapter->setLanguage($this);
        $adapter->setStorageName($storage_name);

        return $adapter;
    }
}
